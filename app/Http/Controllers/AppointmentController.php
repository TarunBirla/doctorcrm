<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\DoctorSlotOverride;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AuditLog;
use App\Models\DoctorAvailability;

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

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor) {
                $query->where('doctor_id', $loggedInDoctor->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($date)) {
            $query->whereDate('appointment_date', $date);
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
            $statsQuery->whereDate('appointment_date', $date);
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

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $patients = $loggedInDoctor ? Patient::where('doctor_id', $loggedInDoctor->id)->orderBy('first_name')->get() : collect();
            $clinics = $loggedInDoctor ? $loggedInDoctor->clinics()->where('is_active', true)->get() : collect();
            if ($clinics->isEmpty() && $loggedInDoctor) {
                $clinics = Clinic::where('doctor_id', $loggedInDoctor->id)->where('is_active', true)->get();
            }
            if ($clinics->isEmpty()) {
                $clinics = Clinic::where('is_active', true)->get();
            }
            $doctors = collect($loggedInDoctor ? [$loggedInDoctor] : []);
        } else {
            $doctors = Doctor::active()->orderBy('name')->get();
            $patients = Patient::orderBy('first_name')->get();
            $clinics = Clinic::where('is_active', true)->orderBy('name')->get();
        }

        return view('appointments.index', compact(
            'appointments', 'totalAppointments', 'completedAppointments',
            'waitingAppointments', 'totalRevenue', 'totalDues',
            'date', 'status', 'type', 'paymentStatus', 'search', 'doctors', 'patients', 'clinics'
        ));
    }

    public function create(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $patients = $loggedInDoctor ? Patient::where('doctor_id', $loggedInDoctor->id)->orderBy('first_name')->get() : collect();
            $clinics = $loggedInDoctor ? $loggedInDoctor->clinics()->where('is_active', true)->get() : collect();
            if ($clinics->isEmpty() && $loggedInDoctor) {
                $clinics = Clinic::where('doctor_id', $loggedInDoctor->id)->where('is_active', true)->get();
            }
            if ($clinics->isEmpty()) {
                $clinics = Clinic::where('is_active', true)->get();
            }
            $doctors = collect($loggedInDoctor ? [$loggedInDoctor] : []);
        } else {
            $patients = Patient::orderBy('first_name')->get();
            $doctors = Doctor::active()->orderBy('name')->get();
            $clinics = Clinic::where('is_active', true)->orderBy('name')->get();
        }

        $preselectedPatientId = $request->get('patient_id');
        $preselectedClinicId = $request->get('clinic_id');
        $preselectedDate = $request->get('date', now()->toDateString());
        $preselectedTime = $request->get('time');

        return view('appointments.create', compact('doctors', 'patients', 'clinics', 'loggedInDoctor', 'preselectedPatientId', 'preselectedClinicId', 'preselectedDate', 'preselectedTime'));
    }

    public function store(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'clinic_id' => 'required|exists:clinics,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string',
            'appointment_type' => 'required|in:new,follow_up,revisit,emergency',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
        ]);

        // Security check: Doctor cannot book for another doctor's patients
        if ($loggedInDoctor) {
            $patient = Patient::findOrFail($validated['patient_id']);
            if ($patient->doctor_id && $patient->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized: You can only book appointments for your own registered patients.');
            }
            // Enforce logged-in doctor
            $validated['doctor_id'] = $loggedInDoctor->id;
        }

        // Slot availability check: Check if doctor already has an active appointment at this exact slot & clinic
        $time24 = date('H:i', strtotime($validated['appointment_time']));
        $slotOccupied = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('clinic_id', $validated['clinic_id'])
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->first(function ($apt) use ($time24) {
                return date('H:i', strtotime($apt->appointment_time)) === $time24;
            });

        if ($slotOccupied && $validated['appointment_type'] !== 'emergency') {
            $doc = Doctor::find($validated['doctor_id']);
            $clinicObj = Clinic::find($validated['clinic_id']);
            return back()->withInput()->withErrors([
                'appointment_time' => "This slot ({$validated['appointment_time']}) is already booked for Dr. {$doc->name} at {$clinicObj->name}. Please pick another available slot."
            ]);
        }

        $appointmentNo = Appointment::generateAppointmentNo();
        $tokenNumber = Appointment::nextTokenForDate($validated['appointment_date'], $validated['doctor_id']);

        $appointment = Appointment::create([
            'appointment_no' => $appointmentNo,
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'clinic_id' => $validated['clinic_id'],
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
            ->with('success', "Appointment booked successfully! Token #{$tokenNumber} assigned at {$appointment->clinic?->name}.");
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
            ->whereDate('appointment_date', $validated['appointment_date'])
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

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
        }

        $query = Appointment::with(['patient', 'doctor']);

        if ($loggedInDoctor) {
            $query->where('doctor_id', $loggedInDoctor->id);
        } elseif ($currentRole === 'doctor') {
            $query->whereRaw('1 = 0');
        }

        // Fetch appointments for month or week
        if ($view === 'day') {
            $appointments = (clone $query)
                ->whereDate('appointment_date', $carbonDate->toDateString())
                ->orderBy('appointment_time', 'asc')
                ->get();
        } elseif ($view === 'week') {
            $startWeek = (clone $carbonDate)->startOfWeek();
            $endWeek = (clone $carbonDate)->endOfWeek();
            $appointments = (clone $query)
                ->whereDate('appointment_date', '>=', $startWeek->toDateString())
                ->whereDate('appointment_date', '<=', $endWeek->toDateString())
                ->orderBy('appointment_time', 'asc')
                ->get();
        } else {
            // Month
            $startMonth = (clone $carbonDate)->startOfMonth();
            $endMonth = (clone $carbonDate)->endOfMonth();
            $appointments = (clone $query)
                ->whereDate('appointment_date', '>=', $startMonth->toDateString())
                ->whereDate('appointment_date', '<=', $endMonth->toDateString())
                ->orderBy('appointment_time', 'asc')
                ->get();
        }

        $appointmentsByDate = $appointments->groupBy(function ($appt) {
            return $appt->appointment_date ? Carbon::parse($appt->appointment_date)->format('Y-m-d') : '';
        });

        $doctors = Doctor::active()->get();
        $patients = $loggedInDoctor 
            ? Patient::where('doctor_id', $loggedInDoctor->id)->orderBy('first_name')->get()
            : Patient::orderBy('first_name')->get();

        return view('appointments.calendar', compact('appointments', 'appointmentsByDate', 'view', 'selectedDate', 'carbonDate', 'doctors', 'patients', 'loggedInDoctor'));
    }

    public function getDoctorSlots(Request $request)
    {
        $doctorId = $request->get('doctor_id');
        $clinicId = $request->get('clinic_id');
        $date = $request->get('date', now()->toDateString());

        if (!$doctorId) {
            return response()->json(['error' => 'Doctor ID is required'], 400);
        }

        $doctor = Doctor::find($doctorId);
        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found'], 404);
        }

        // Resolve Clinic
        $clinic = null;
        if ($clinicId) {
            $clinic = Clinic::find($clinicId);
        }
        if (!$clinic) {
            $clinic = $doctor->clinics()->where('is_active', true)->first()
                   ?? Clinic::where('doctor_id', $doctor->id)->where('is_active', true)->first()
                   ?? Clinic::where('is_active', true)->first();
        }

        try {
            $parsedDate = Carbon::parse($date);
            $dayOfWeek = $parsedDate->format('l'); // Monday, Tuesday...
            $dateFormatted = $parsedDate->toDateString();
        } catch (\Exception $e) {
            $parsedDate = Carbon::today();
            $dayOfWeek = $parsedDate->format('l');
            $dateFormatted = $parsedDate->toDateString();
        }

        // 1. Check clinic-specific availability, fallback to general doctor availability
        $availability = null;
        if ($clinic) {
            $availability = DoctorAvailability::where('doctor_id', $doctorId)
                ->where('clinic_id', $clinic->id)
                ->where('day_of_week', $dayOfWeek)
                ->first();
        }

        if (!$availability) {
            $availability = DoctorAvailability::where('doctor_id', $doctorId)
                ->where('day_of_week', $dayOfWeek)
                ->first();
        }

        $isAvailable = $availability ? (bool) $availability->is_available : ($dayOfWeek !== 'Sunday');

        $effectiveFee = $clinic ? $clinic->consultation_fee : $doctor->consultation_fee;

        if (!$isAvailable) {
            return response()->json([
                'success' => true,
                'doctor' => ['id' => $doctor->id, 'name' => $doctor->name, 'fee' => $effectiveFee],
                'clinic' => $clinic ? ['id' => $clinic->id, 'name' => $clinic->name] : null,
                'date' => $dateFormatted,
                'day' => $dayOfWeek,
                'is_available' => false,
                'message' => "Dr. {$doctor->name} is unavailable at " . ($clinic ? $clinic->name : 'this clinic') . " on {$dayOfWeek}s.",
                'slots' => [],
                'available_count' => 0,
                'booked_count' => 0,
            ]);
        }

        // Clean time strings (handles 09:00:00, 09:00, or AM/PM)
        $rawStart = $availability ? $availability->start_time : ($clinic ? '09:00' : '09:00');
        $rawEnd = $availability ? $availability->end_time : ($clinic ? '17:00' : '18:00');
        $duration = ($availability && $availability->slot_duration > 0)
            ? (int) $availability->slot_duration
            : ($clinic->appointment_duration ?? 15);

        try {
            $startHour = Carbon::parse("{$dateFormatted} " . trim($rawStart));
            $endHour = Carbon::parse("{$dateFormatted} " . trim($rawEnd));
        } catch (\Exception $e) {
            $startHour = Carbon::parse("{$dateFormatted} 09:00");
            $endHour = Carbon::parse("{$dateFormatted} 17:00");
        }

        // Fetch already booked appointments for this doctor (+ clinic if specified)
        $bookedQuery = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $dateFormatted)
            ->whereNotIn('status', ['cancelled']);

        if ($clinic) {
            $bookedQuery->where(function ($q) use ($clinic) {
                $q->where('clinic_id', $clinic->id)->orWhereNull('clinic_id');
            });
        }

        $bookedTimes = $bookedQuery->get()->map(function ($apt) {
            return date('H:i', strtotime($apt->appointment_time));
        })->toArray();

        // Fetch blocked slots from DoctorSlotOverride
        $blockedOverrides = [];
        $customExtraSlots = [];
        if ($clinic) {
            $overrides = \App\Models\DoctorSlotOverride::where('doctor_id', $doctorId)
                ->where('clinic_id', $clinic->id)
                ->whereDate('slot_date', $dateFormatted)
                ->get();

            foreach ($overrides as $ov) {
                $timeKey = date('H:i', strtotime($ov->slot_time));
                if ($ov->is_blocked) {
                    $blockedOverrides[$timeKey] = $ov->reason ?: 'Blocked by doctor';
                } else {
                    $customExtraSlots[$timeKey] = $ov->reason ?: 'Extra slot';
                }
            }
        }

        $slots = [];
        $current = $startHour->copy();

        // Parse breaks safely
        $hasBreak = false;
        $bStart = null;
        $bEnd = null;
        if ($availability && !empty($availability->break_start) && !empty($availability->break_end)) {
            try {
                $bStart = Carbon::parse("{$dateFormatted} " . trim($availability->break_start));
                $bEnd = Carbon::parse("{$dateFormatted} " . trim($availability->break_end));
                $hasBreak = $bStart->lt($bEnd);
            } catch (\Exception $e) {
                $hasBreak = false;
            }
        }

        while ($current->lt($endHour)) {
            $time24 = $current->format('H:i');
            $timeLabel = $current->format('h:i A');

            $isBreak = false;
            if ($hasBreak && $current->gte($bStart) && $current->lt($bEnd)) {
                $isBreak = true;
            }

            if (!$isBreak) {
                $isBooked = in_array($time24, $bookedTimes);
                $isBlocked = isset($blockedOverrides[$time24]);

                $slots[] = [
                    'time' => $time24,
                    'label' => $timeLabel,
                    'is_booked' => $isBooked,
                    'is_blocked' => $isBlocked,
                    'is_available' => (!$isBooked && !$isBlocked),
                    'reason' => $isBlocked ? $blockedOverrides[$time24] : null,
                ];
            }

            $current->addMinutes($duration);
        }

        // Add custom extra slots if not already in slots list
        foreach ($customExtraSlots as $extraTime => $note) {
            $exists = collect($slots)->contains('time', $extraTime);
            if (!$exists) {
                $customCarbon = Carbon::parse("{$dateFormatted} {$extraTime}");
                $isBooked = in_array($extraTime, $bookedTimes);
                $slots[] = [
                    'time' => $extraTime,
                    'label' => $customCarbon->format('h:i A'),
                    'is_booked' => $isBooked,
                    'is_blocked' => false,
                    'is_available' => !$isBooked,
                    'reason' => $note,
                ];
            }
        }

        // Sort slots by time
        usort($slots, fn($a, $b) => strcmp($a['time'], $b['time']));

        $availableCount = count(array_filter($slots, fn($s) => $s['is_available']));
        $bookedCount = count(array_filter($slots, fn($s) => $s['is_booked']));
        $blockedCount = count(array_filter($slots, fn($s) => $s['is_blocked']));

        return response()->json([
            'success' => true,
            'doctor' => ['id' => $doctor->id, 'name' => $doctor->name, 'fee' => $effectiveFee],
            'clinic' => $clinic ? ['id' => $clinic->id, 'name' => $clinic->name, 'fee' => $clinic->consultation_fee, 'duration' => $clinic->appointment_duration] : null,
            'date' => $dateFormatted,
            'day' => $dayOfWeek,
            'is_available' => true,
            'slots' => $slots,
            'available_count' => $availableCount,
            'booked_count' => $bookedCount,
            'blocked_count' => $blockedCount,
        ]);
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
