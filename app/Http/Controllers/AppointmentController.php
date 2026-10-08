<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AuditLog;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        $status = $request->get('status');
        $type = $request->get('type');
        $paymentStatus = $request->get('payment_status');
        $search = $request->get('search');

        $query = Appointment::with(['patient', 'doctor', 'invoice']);

        if (!empty($date)) {
            $query->where('appointment_date', $date);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if (!empty($type)) {
            $query->where('appointment_type', $type);
        }

        if (!empty($paymentStatus)) {
            $query->where('payment_status', $paymentStatus);
        }

        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            })->orWhere('appointment_no', 'like', "%{$search}%");
        }

        $appointments = $query->orderBy('appointment_time', 'asc')->paginate(15)->withQueryString();

        // Top KPI Cards for the filtered date / all
        $statsQuery = Appointment::query();
        if (!empty($date)) {
            $statsQuery->where('appointment_date', $date);
        }
        $totalAppointments = (clone $statsQuery)->count();
        $completedAppointments = (clone $statsQuery)->where('status', 'completed')->count();
        $waitingAppointments = (clone $statsQuery)->where('status', 'waiting')->count();
        
        $invoicesQuery = Invoice::query();
        if (!empty($date)) {
            $invoicesQuery->where('invoice_date', $date);
        }
        $totalRevenue = (clone $invoicesQuery)->sum('paid_amount');
        $totalDues = (clone $invoicesQuery)->sum('due_amount');

        $doctors = Doctor::all();
        $patients = Patient::orderBy('first_name')->get();

        return view('appointments.index', compact(
            'appointments', 'totalAppointments', 'completedAppointments',
            'waitingAppointments', 'totalRevenue', 'totalDues',
            'date', 'status', 'type', 'paymentStatus', 'search', 'doctors', 'patients'
        ));
    }

    public function create()
    {
        $doctors = Doctor::all();
        $patients = Patient::orderBy('first_name')->get();
        return view('appointments.create', compact('doctors', 'patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'appointment_type' => 'required|in:new,follow_up,revisit,emergency',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
        ]);

        // Check if THIS SAME PATIENT already has an active appointment with the doctor at this exact time
        $duplicatePatient = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('patient_id', $validated['patient_id'])
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->whereNotIn('status', ['cancelled'])
            ->first();

        if ($duplicatePatient) {
            return back()->withInput()->withErrors([
                'appointment_time' => 'This patient already has an active appointment booked with this doctor at ' . $validated['appointment_time'] . '.'
            ]);
        }

        $appointmentNo = Appointment::generateAppointmentNo();
        $tokenNumber = Appointment::nextTokenForDate($validated['appointment_date'], $validated['doctor_id']);

        $appointment = Appointment::create([
            'appointment_no' => $appointmentNo,
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'appointment_type' => $validated['appointment_type'],
            'token_number' => $tokenNumber,
            'reason' => $validated['reason'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'consultation_fee' => $validated['consultation_fee'] ?? 800.00,
            'payment_status' => 'unpaid',
            'status' => 'scheduled',
        ]);

        // Auto-generate invoice for consultation fee
        $invoiceNo = Invoice::generateInvoiceNo();
        $invoice = Invoice::create([
            'invoice_no' => $invoiceNo,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'invoice_date' => $appointment->appointment_date,
            'subtotal' => $appointment->consultation_fee,
            'discount' => 0.00,
            'additional_charges' => 0.00,
            'total_amount' => $appointment->consultation_fee,
            'paid_amount' => 0.00,
            'due_amount' => $appointment->consultation_fee,
            'payment_status' => 'unpaid',
            'notes' => 'Generated automatically for appointment ' . $appointment->appointment_no,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_description' => ucfirst($appointment->appointment_type) . ' Consultation Fee',
            'quantity' => 1,
            'unit_price' => $appointment->consultation_fee,
            'total' => $appointment->consultation_fee,
        ]);

        AuditLog::record('Appointment Created', 'Appointment', $appointment->appointment_no, "Created appointment for {$appointment->patient->full_name} on {$appointment->appointment_date->format('d M Y')} (Token #{$tokenNumber})");

        return redirect()->route('appointments.index', ['date' => $appointment->appointment_date->toDateString()])
            ->with('success', "Appointment booked successfully! Token #{$tokenNumber} assigned.");
    }

    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);
        $newStatus = $request->validate([
            'status' => 'required|in:scheduled,confirmed,waiting,in_consultation,completed,cancelled,no_show,rescheduled',
        ])['status'];

        $oldStatus = $appointment->status;
        $appointment->status = $newStatus;

        if ($newStatus === 'waiting' && !$appointment->waiting_since) {
            $appointment->waiting_since = now();
        }

        $appointment->save();

        AuditLog::record('Appointment Status Changed', 'Appointment', $appointment->appointment_no, "Status changed from {$oldStatus} to {$newStatus} for patient {$appointment->patient->full_name}");

        return back()->with('success', "Appointment status updated to " . ucfirst(str_replace('_', ' ', $newStatus)));
    }

    public function reschedule(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
        ]);

        // Conflict check
        $existing = Appointment::where('doctor_id', $appointment->doctor_id)
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->where('id', '!=', $id)
            ->whereNotIn('status', ['cancelled'])
            ->first();

        if ($existing) {
            return back()->withErrors(['appointment_time' => 'Slot already occupied by another patient.']);
        }

        $appointment->appointment_date = $validated['appointment_date'];
        $appointment->appointment_time = $validated['appointment_time'];
        $appointment->status = 'rescheduled';
        $appointment->token_number = Appointment::nextTokenForDate($validated['appointment_date'], $appointment->doctor_id);
        $appointment->save();

        AuditLog::record('Appointment Rescheduled', 'Appointment', $appointment->appointment_no, "Rescheduled to {$appointment->appointment_date->format('d M Y')} at {$appointment->appointment_time}");

        return back()->with('success', "Appointment rescheduled successfully to {$appointment->appointment_date->format('d M Y')} at {$appointment->appointment_time}.");
    }

    public function calendar(Request $request)
    {
        $view = $request->get('view', 'month'); // day, week, month
        $selectedDate = $request->get('date', now()->toDateString());
        $carbonDate = Carbon::parse($selectedDate);

        // Fetch appointments for month or week
        if ($view === 'day') {
            $appointments = Appointment::with(['patient', 'doctor'])
                ->where('appointment_date', $carbonDate->toDateString())
                ->orderBy('appointment_time', 'asc')
                ->get();
        } elseif ($view === 'week') {
            $startWeek = (clone $carbonDate)->startOfWeek();
            $endWeek = (clone $carbonDate)->endOfWeek();
            $appointments = Appointment::with(['patient', 'doctor'])
                ->whereBetween('appointment_date', [$startWeek->toDateString(), $endWeek->toDateString()])
                ->orderBy('appointment_time', 'asc')
                ->get();
        } else {
            // Month
            $startMonth = (clone $carbonDate)->startOfMonth();
            $endMonth = (clone $carbonDate)->endOfMonth();
            $appointments = Appointment::with(['patient', 'doctor'])
                ->whereBetween('appointment_date', [$startMonth->toDateString(), $endMonth->toDateString()])
                ->orderBy('appointment_time', 'asc')
                ->get();
        }

        $doctors = Doctor::all();
        $patients = Patient::orderBy('first_name')->get();

        return view('appointments.calendar', compact('appointments', 'view', 'selectedDate', 'carbonDate', 'doctors', 'patients'));
    }

    public function destroy($id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);
        $no = $appointment->appointment_no;
        $appointment->delete();

        AuditLog::record('Appointment Cancelled/Deleted', 'Appointment', $no, "Appointment {$no} removed");

        return back()->with('success', "Appointment #{$no} deleted successfully.");
    }
}
