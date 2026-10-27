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
    /**
     * Apply Doctor / Staff scoping to an Appointment query
     */
    protected function scopeAppointmentsForDoctor($query, ?Doctor $loggedInDoctor, string $currentRole)
    {
        if (in_array($currentRole, ['doctor', 'receptionist', 'staff'], true) && $loggedInDoctor) {
            $docId = $loggedInDoctor->id;
            $clinicIds = $loggedInDoctor->allClinicIds();

            // Check if there are appointments matching this doctor/clinics; if single-doctor system, include unlinked ones too
            $totalDoctors = Doctor::count();
            $query->where(function ($q) use ($docId, $clinicIds, $totalDoctors) {
                $q->where('doctor_id', $docId)
                  ->orWhereHas('patient', function ($pq) use ($docId) {
                      $pq->where('doctor_id', $docId);
                  });
                if (!empty($clinicIds)) {
                    $q->orWhereIn('clinic_id', $clinicIds);
                }
                if ($totalDoctors <= 1) {
                    $q->orWhereNotNull('id');
                }
            });
        }
        return $query;
    }

    public function index(Request $request)
    {
        // Default to empty date ('') & period='all' so Doctor sees ALL Appointments by default!
        $date = $request->get('date', '');
        $period = $request->get('period', !empty($date) ? 'custom_date' : 'all');
        $status = $request->get('status');
        $type = $request->get('type');
        $paymentStatus = $request->get('payment_status');
        $search = $request->get('search');
        $clinicId = $request->get('clinic_id');
        $categoryId = $request->get('category_id');

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);

        // Base scoped query for this doctor / staff
        $baseScopedQuery = Appointment::query();
        $this->scopeAppointmentsForDoctor($baseScopedQuery, $loggedInDoctor, $currentRole);

        // Compute quick tab counts before date/status filter
        $todayStr = now()->toDateString();
        $allAppointmentsCount = (clone $baseScopedQuery)->count();
        $todayAppointmentsCount = (clone $baseScopedQuery)->whereDate('appointment_date', $todayStr)->count();
        $upcomingAppointmentsCount = (clone $baseScopedQuery)->whereDate('appointment_date', '>', $todayStr)->count();

        $query = Appointment::with(['patient.appointments', 'doctor', 'invoice', 'clinic', 'category']);
        $this->scopeAppointmentsForDoctor($query, $loggedInDoctor, $currentRole);

        if (!empty($date)) {
            $query->whereDate('appointment_date', $date);
            $period = 'custom_date';
        } elseif ($period === 'today') {
            $query->whereDate('appointment_date', $todayStr);
        } elseif ($period === 'upcoming') {
            $query->whereDate('appointment_date', '>=', $todayStr);
        } elseif ($period === 'past') {
            $query->whereDate('appointment_date', '<', $todayStr);
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

        if (!empty($clinicId)) {
            $query->where('clinic_id', $clinicId);
        }

        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        if (!empty($search)) {
            $query->where(function ($outer) use ($search) {
                $outer->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('patient_id', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                })->orWhere('appointment_no', 'like', "%{$search}%");
            });
        }

        $appointments = $query
            ->orderBy('appointment_date', 'asc')
            ->orderBy('token_number', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(20)
            ->withQueryString();

        // Top KPI Cards for the filtered view
        $statsQuery = Appointment::query();
        $this->scopeAppointmentsForDoctor($statsQuery, $loggedInDoctor, $currentRole);
        if (!empty($date)) {
            $statsQuery->whereDate('appointment_date', $date);
        } elseif ($period === 'today') {
            $statsQuery->whereDate('appointment_date', $todayStr);
        } elseif ($period === 'upcoming') {
            $statsQuery->whereDate('appointment_date', '>=', $todayStr);
        }
        if (!empty($clinicId)) {
            $statsQuery->where('clinic_id', $clinicId);
        }
        if (!empty($categoryId)) {
            $statsQuery->where('category_id', $categoryId);
        }

        $totalAppointments = (clone $statsQuery)->count();
        $completedAppointments = (clone $statsQuery)->where('status', 'completed')->count();
        $waitingAppointments = (clone $statsQuery)->where('status', 'waiting')->count();

        $invoicesQuery = Invoice::query();
        if (in_array($currentRole, ['doctor', 'receptionist', 'staff'], true) && $loggedInDoctor) {
            $docId = $loggedInDoctor->id;
            $clinicIds = $loggedInDoctor->allClinicIds();
            $invoicesQuery->where(function ($q) use ($docId, $clinicIds) {
                $q->where('doctor_id', $docId)
                  ->orWhereHas('patient', fn($pq) => $pq->where('doctor_id', $docId))
                  ->orWhereHas('appointment', function ($aq) use ($docId, $clinicIds) {
                      $aq->where('doctor_id', $docId);
                      if (!empty($clinicIds)) {
                          $aq->orWhereIn('clinic_id', $clinicIds);
                      }
                  });
            });
        }
        if (!empty($date)) {
            $invoicesQuery->whereDate('invoice_date', $date);
        } elseif ($period === 'today') {
            $invoicesQuery->whereDate('invoice_date', $todayStr);
        }
        $totalRevenue = (clone $invoicesQuery)->sum('paid_amount');
        $totalDues = (clone $invoicesQuery)->sum('due_amount');

        if (in_array($currentRole, ['doctor', 'receptionist', 'staff'], true) && $loggedInDoctor) {
            $patients = Patient::where('doctor_id', $loggedInDoctor->id)->orWhereNull('doctor_id')->orderBy('first_name')->get();
            if ($patients->isEmpty()) {
                $patients = Patient::orderBy('first_name')->get();
            }
            $clinicIds = $loggedInDoctor->allClinicIds();
            $clinics = !empty($clinicIds)
                ? Clinic::whereIn('id', $clinicIds)->where('is_active', true)->get()
                : Clinic::where('is_active', true)->get();
            if ($clinics->isEmpty()) {
                $clinics = Clinic::where('is_active', true)->get();
            }
            $doctors = collect([$loggedInDoctor]);
        } else {
            $doctors = Doctor::active()->orderBy('name')->get();
            $patients = Patient::orderBy('first_name')->get();
            $clinics = Clinic::where('is_active', true)->orderBy('name')->get();
        }

        $categories = \App\Models\TreatmentCategory::active()->orderBy('name')->get();

        return view('appointments.index', compact(
            'appointments', 'totalAppointments', 'completedAppointments',
            'waitingAppointments', 'totalRevenue', 'totalDues',
            'allAppointmentsCount', 'todayAppointmentsCount', 'upcomingAppointmentsCount',
            'date', 'period', 'status', 'type', 'paymentStatus', 'search', 'clinicId', 'categoryId',
            'doctors', 'patients', 'clinics', 'categories', 'currentRole'
        ));
    }

    public function create(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);

        if (in_array($currentRole, ['doctor', 'receptionist', 'staff'], true) && $loggedInDoctor) {
            $patients = Patient::where('doctor_id', $loggedInDoctor->id)->orWhereNull('doctor_id')->orderBy('first_name')->get();
            if ($patients->isEmpty()) {
                $patients = Patient::orderBy('first_name')->get();
            }
            $clinicIds = $loggedInDoctor->allClinicIds();
            $clinics = !empty($clinicIds)
                ? Clinic::whereIn('id', $clinicIds)->where('is_active', true)->get()
                : Clinic::where('is_active', true)->get();
            if ($clinics->isEmpty()) {
                $clinics = Clinic::where('is_active', true)->get();
            }
            $doctors = collect([$loggedInDoctor]);
        } else {
            $patients = Patient::orderBy('first_name')->get();
            $doctors = Doctor::active()->orderBy('name')->get();
            $clinics = Clinic::where('is_active', true)->orderBy('name')->get();
        }

        $categories = \App\Models\TreatmentCategory::active()->orderBy('name')->get();

        $preselectedPatientId = $request->get('patient_id');
        $preselectedClinicId = $request->get('clinic_id');
        $preselectedCategoryId = $request->get('category_id');
        $preselectedDate = $request->get('date', now()->toDateString());

        return view('appointments.create', compact('doctors', 'patients', 'clinics', 'categories', 'loggedInDoctor', 'preselectedPatientId', 'preselectedClinicId', 'preselectedCategoryId', 'preselectedDate'));
    }

    public function store(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);

        $validated = $request->validate([
            'clinic_id' => 'required|exists:clinics,id',
            'category_id' => 'required|exists:treatment_categories,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'treatment_days' => 'required|integer|min:1|max:365',
            'daily_fee' => 'nullable|numeric|min:0',
            'consultation_fee' => 'nullable|numeric|min:0',
            'appointment_type' => 'nullable|in:new,follow_up,revisit,emergency',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($loggedInDoctor) {
            $validated['doctor_id'] = $loggedInDoctor->id;
        }

        // Days-based fee calculation: Total Fee = Daily Fee * Treatment Days
        $clinicObj = Clinic::find($validated['clinic_id']);
        $dailyFee = (float) ($validated['daily_fee'] ?? $validated['consultation_fee'] ?? ($clinicObj ? $clinicObj->consultation_fee : 800.00));
        $treatmentDays = (int) $validated['treatment_days'];
        $totalFee = $dailyFee * $treatmentDays;

        $categoryObj = \App\Models\TreatmentCategory::find($validated['category_id']);
        $categoryName = $categoryObj ? $categoryObj->name : 'Physiotherapy Session';

        $currentDate = \Carbon\Carbon::parse($validated['appointment_date']);
        $createdAppointments = [];
        $sessionNum = 1;

        // Check if payment was collected upfront at booking
        $isPaidUpfront = $request->boolean('payment_collected') || ($request->input('payment_status') === 'paid');
        $paymentMethod = $request->input('payment_method', 'Cash');
        $initialPaymentStatus = $isPaidUpfront ? 'paid' : 'unpaid';

        while (count($createdAppointments) < $treatmentDays) {
            if (!$currentDate->isSunday() && !\App\Models\ClinicHoliday::isHolidayDate($currentDate, $validated['clinic_id'], $validated['doctor_id'])) {
                $dateStr = $currentDate->format('Y-m-d');
                $appointmentNo = Appointment::generateAppointmentNo();
                $tokenNumber = Appointment::nextTokenForDate($dateStr, $validated['doctor_id']);

                $apt = Appointment::create([
                    'appointment_no' => $appointmentNo,
                    'patient_id' => $validated['patient_id'],
                    'doctor_id' => $validated['doctor_id'],
                    'clinic_id' => $validated['clinic_id'],
                    'category_id' => $validated['category_id'],
                    'appointment_date' => $dateStr,
                    'treatment_days' => 1,
                    'daily_fee' => $dailyFee,
                    'appointment_time' => $request->input('appointment_time') ?? "Session {$sessionNum}",
                    'appointment_type' => ($sessionNum === 1) ? ($validated['appointment_type'] ?? 'new') : 'follow_up',
                    'token_number' => $tokenNumber,
                    'reason' => ($sessionNum === 1) ? ($validated['reason'] ?? "Session 1") : "Session {$sessionNum} - " . ($validated['reason'] ?? ''),
                    'notes' => $validated['notes'] ?? null,
                    'consultation_fee' => $dailyFee,
                    'payment_status' => $initialPaymentStatus,
                    'status' => 'scheduled',
                ]);
                $createdAppointments[] = $apt;
                $sessionNum++;
            }
            $currentDate->addDay();
        }

        $appointment = $createdAppointments[0] ?? null;

        // Auto-generate invoice for physiotherapy session package
        $invoiceNo = Invoice::generateInvoiceNo();
        $invoice = Invoice::create([
            'invoice_no' => $invoiceNo,
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment ? $appointment->id : null,
            'doctor_id' => $appointment->doctor_id,
            'invoice_date' => $appointment->appointment_date,
            'subtotal' => $totalFee,
            'discount' => 0.00,
            'additional_charges' => 0.00,
            'total_amount' => $totalFee,
            'paid_amount' => $isPaidUpfront ? $totalFee : 0.00,
            'due_amount' => $isPaidUpfront ? 0.00 : $totalFee,
            'payment_status' => $initialPaymentStatus,
            'payment_method' => $isPaidUpfront ? $paymentMethod : null,
            'notes' => "Physiotherapy package: {$categoryName} for {$treatmentDays} days (excluding Sundays) (₹" . number_format($dailyFee, 2) . "/day)",
        ]);

        // Link all created appointments in this package to the generated invoice
        foreach ($createdAppointments as $apt) {
            $aptData = ['payment_status' => $initialPaymentStatus];
            if (\Illuminate\Support\Facades\Schema::hasColumn('appointments', 'invoice_id')) {
                $aptData['invoice_id'] = $invoice->id;
            }
            $apt->update($aptData);
        }

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'item_description' => "{$categoryName} ({$treatmentDays} Days Treatment Package)",
            'quantity' => $treatmentDays,
            'unit_price' => $dailyFee,
            'total' => $totalFee,
        ]);

        // If paid upfront, record payment transaction
        if ($isPaidUpfront) {
            $txnNo = \App\Models\PaymentTransaction::generateTransactionNo();
            $recNo = \App\Models\PaymentTransaction::generateReceiptNo();
            \App\Models\PaymentTransaction::create([
                'invoice_id' => $invoice->id,
                'patient_id' => $invoice->patient_id,
                'transaction_no' => $txnNo,
                'amount' => $totalFee,
                'payment_method' => $paymentMethod,
                'payment_date' => $appointment->appointment_date,
                'collected_by' => auth()->user()->name ?? 'Receptionist',
                'receipt_no' => $recNo,
                'notes' => "Full package payment collected upfront at booking.",
            ]);
        }

        AuditLog::record('Appointment Package Created', 'Appointment', $appointment->appointment_no, "Created {$treatmentDays}-session physiotherapy package for {$appointment->patient->full_name} at {$appointment->clinic?->name} (Total: ₹{$totalFee}, Status: {$initialPaymentStatus})");

        $msg = \App\Models\User::isStaffRole($currentRole)
            ? "Appointment package ({$treatmentDays} sessions) booked successfully!"
            : "Appointment package ({$treatmentDays} sessions) booked successfully! Total Fee: ₹" . number_format($totalFee, 2) . ($isPaidUpfront ? " (Marked as Paid via {$paymentMethod})" : "") . ".";

        return redirect()->route('appointments.index')
            ->with('success', $msg);
    }

    public function edit($id)
    {
        $appointment = Appointment::with(['patient', 'doctor', 'clinic', 'category', 'invoice'])->findOrFail($id);
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);

        if (in_array($currentRole, ['doctor', 'receptionist', 'staff'], true) && $loggedInDoctor) {
            $clinicIds = $loggedInDoctor->allClinicIds();
            $clinics = !empty($clinicIds)
                ? Clinic::whereIn('id', $clinicIds)->where('is_active', true)->get()
                : Clinic::where('is_active', true)->get();
            if ($clinics->isEmpty()) {
                $clinics = Clinic::where('is_active', true)->get();
            }
            $doctors = collect([$loggedInDoctor]);
        } else {
            $doctors = Doctor::active()->orderBy('name')->get();
            $clinics = Clinic::where('is_active', true)->orderBy('name')->get();
        }

        $categories = \App\Models\TreatmentCategory::active()->orderBy('name')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'appointment' => $appointment,
                'patient' => $appointment->patient,
                'clinic' => $appointment->clinic,
                'category' => $appointment->category,
            ]);
        }

        return view('appointments.edit', compact('appointment', 'clinics', 'categories', 'doctors', 'loggedInDoctor'));
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::with(['patient.appointments', 'invoice.items', 'clinic', 'category'])->findOrFail($id);

        $validated = $request->validate([
            'appointment_date' => 'nullable|date',
            'additional_days' => 'nullable|integer|min:0|max:180',
            'daily_fee' => 'nullable|numeric|min:0',
            'recovery_percentage' => 'nullable|integer|min:0|max:100',
            'recovery_status' => 'nullable|string|max:100',
            'recovery_notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:scheduled,confirmed,waiting,in_consultation,completed,cancelled,no_show,rescheduled',
            'category_id' => 'nullable|exists:treatment_categories,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'mark_previous_completed' => 'nullable|boolean',
        ]);

        if (!empty($validated['clinic_id'])) {
            $appointment->clinic_id = $validated['clinic_id'];
        }
        if (!empty($validated['category_id'])) {
            $appointment->category_id = $validated['category_id'];
        }
        if (!empty($validated['appointment_date'])) {
            $appointment->appointment_date = $validated['appointment_date'];
        }
        if (!empty($validated['status'])) {
            $appointment->status = $validated['status'];
        }
        if (isset($validated['reason'])) {
            $appointment->reason = $validated['reason'];
        }
        if (isset($validated['notes'])) {
            $appointment->notes = $validated['notes'];
        }

        if (isset($validated['daily_fee']) && $validated['daily_fee'] !== '' && (float) $validated['daily_fee'] >= 0) {
            $appointment->daily_fee = (float) $validated['daily_fee'];
            $appointment->consultation_fee = (float) $validated['daily_fee'];
        }

        $appointment->save();

        $addDays = (int) ($validated['additional_days'] ?? 0);
        $extSummary = null;

        if ($appointment->patient && (isset($validated['recovery_percentage']) || $addDays > 0 || $request->boolean('mark_previous_completed'))) {
            $extSummary = $appointment->patient->extendTreatmentAndSyncRecovery([
                'additional_days' => $addDays,
                'recovery_percentage' => $validated['recovery_percentage'] ?? ($appointment->patient->recovery_percentage ?? 0),
                'recovery_notes' => $validated['recovery_notes'] ?? null,
                'daily_fee' => $appointment->daily_fee,
                'mark_previous_completed' => $request->boolean('mark_previous_completed'),
                'clinic_id' => $appointment->clinic_id,
                'category_id' => $appointment->category_id,
                'doctor_id' => $appointment->doctor_id,
            ]);
        }

        AuditLog::record(
            'Appointment Updated',
            'Appointment',
            $appointment->appointment_no,
            "Updated appointment #{$appointment->appointment_no}" . ($extSummary && $extSummary['added_days'] > 0 ? ": Extended +{$extSummary['added_days']} new appointments (Total {$extSummary['total_sessions']} sessions), Recovery: {$extSummary['recovery_percentage']}%" : "")
        );

        if ($extSummary && $extSummary['added_days'] > 0) {
            return back()->with('success', "Treatment extended by +{$extSummary['added_days']} Days (Sunday skipped)! {$extSummary['added_days']} new daily appointments created (Total: {$extSummary['total_sessions']} Sessions, Added Fee: ₹" . number_format($extSummary['added_fee'], 2) . "). Recovery: {$extSummary['recovery_percentage']}%.");
        }

        return back()->with('success', "Appointment #{$appointment->appointment_no} updated successfully! Recovery: " . ($extSummary['recovery_percentage'] ?? $appointment->recovery_percentage ?? 0) . "%.");
    }

    public function extendDays(Request $request, $id)
    {
        $appointment = Appointment::with(['patient.appointments', 'invoice.items', 'category'])->findOrFail($id);

        $validated = $request->validate([
            'additional_days' => 'required|integer|min:1|max:180',
            'daily_fee' => 'nullable|numeric|min:0',
            'recovery_percentage' => 'nullable|integer|min:0|max:100',
            'recovery_notes' => 'nullable|string|max:1000',
            'mark_previous_completed' => 'nullable|boolean',
        ]);

        if (!$appointment->patient) {
            return back()->with('error', 'Patient record not found for this appointment.');
        }

        $extSummary = $appointment->patient->extendTreatmentAndSyncRecovery([
            'additional_days' => (int) $validated['additional_days'],
            'recovery_percentage' => $validated['recovery_percentage'] ?? ($appointment->patient->recovery_percentage ?? 0),
            'recovery_notes' => $validated['recovery_notes'] ?? null,
            'daily_fee' => $validated['daily_fee'] ?? $appointment->daily_fee,
            'mark_previous_completed' => $request->boolean('mark_previous_completed', true),
            'clinic_id' => $appointment->clinic_id,
            'category_id' => $appointment->category_id,
            'doctor_id' => $appointment->doctor_id,
        ]);

        AuditLog::record('Appointment Extended', 'Appointment', $appointment->appointment_no, "Extended treatment by +{$extSummary['added_days']} new daily sessions (Total: {$extSummary['total_sessions']} sessions, +₹{$extSummary['added_fee']})");

        return back()->with('success', "Treatment extended by +{$extSummary['added_days']} days (excluding Sunday)! {$extSummary['added_days']} new appointments created (Total: {$extSummary['total_sessions']} Sessions, Added Fee: ₹" . number_format($extSummary['added_fee'], 2) . ").");
    }

    public function updateRecovery(Request $request, $id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);
        $validated = $request->validate([
            'recovery_percentage' => 'required|integer|min:0|max:100',
            'recovery_notes' => 'nullable|string|max:1000',
        ]);

        $pct = (int) $validated['recovery_percentage'];
        $appointment->recovery_percentage = $pct;
        $appointment->recovery_status = $appointment->getRecoveryLevelLabel();
        if (isset($validated['recovery_notes'])) {
            $appointment->recovery_notes = $validated['recovery_notes'];
        }
        $appointment->save();

        if ($appointment->patient) {
            $appointment->patient->update(['recovery_percentage' => $pct]);

            \App\Models\PatientProgress::create([
                'patient_id' => $appointment->patient_id,
                'recorded_date' => now()->toDateString(),
                'pain_level' => max(0, min(10, (int) round((100 - $pct) / 10))),
                'symptoms_assessment' => "Recovery: {$pct}% - " . $appointment->getRecoveryLevelLabel(),
                'treatment_response' => "Updated recovery status: {$pct}%.",
                'doctor_notes' => $validated['recovery_notes'] ?? null,
            ]);
        }

        AuditLog::record('Recovery Updated', 'Appointment', $appointment->appointment_no, "Recovery updated to {$pct}% for #{$appointment->appointment_no}");

        return back()->with('success', "Patient recovery updated to {$pct}% ({$appointment->getRecoveryLevelLabel()}).");
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

        $query = Appointment::with(['patient', 'doctor', 'clinic', 'category']);

        if ($loggedInDoctor) {
            $doctorClinicIds = $loggedInDoctor->clinics()->pluck('clinics.id')
                ->merge($loggedInDoctor->ownedClinics()->pluck('id'))
                ->unique()->filter()->toArray();

            $query->where(function ($q) use ($loggedInDoctor, $doctorClinicIds) {
                $q->where('doctor_id', $loggedInDoctor->id);
                if (!empty($doctorClinicIds)) {
                    $q->orWhereIn('clinic_id', $doctorClinicIds);
                }
            });
        } elseif ($currentRole === 'doctor') {
            $query->whereRaw('1 = 0');
        }

        $clinicId = $request->get('clinic_id');
        $categoryId = $request->get('category_id');

        if (!empty($clinicId)) {
            $query->where('clinic_id', $clinicId);
        }
        if (!empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        // Fetch appointments accounting for multi-day physiotherapy packages
        if ($view === 'day') {
            $targetDateStr = $carbonDate->toDateString();
            $appointments = (clone $query)
                ->whereDate('appointment_date', '<=', $targetDateStr)
                ->whereDate('appointment_date', '>=', (clone $carbonDate)->subDays(90)->toDateString())
                ->orderBy('token_number', 'asc')
                ->get()
                ->filter(function ($a) use ($targetDateStr) {
                    $start = Carbon::parse($a->appointment_date)->startOfDay();
                    $days = max(1, (int) ($a->treatment_days ?? 1));
                    $end = (clone $start)->addDays($days - 1)->endOfDay();
                    $check = Carbon::parse($targetDateStr);
                    return $check->between($start, $end);
                });
        } elseif ($view === 'week') {
            $startWeek = (clone $carbonDate)->startOfWeek();
            $endWeek = (clone $carbonDate)->endOfWeek();
            $appointments = (clone $query)
                ->whereDate('appointment_date', '<=', $endWeek->toDateString())
                ->whereDate('appointment_date', '>=', (clone $startWeek)->subDays(90)->toDateString())
                ->orderBy('token_number', 'asc')
                ->get()
                ->filter(function ($a) use ($startWeek, $endWeek) {
                    $start = Carbon::parse($a->appointment_date)->startOfDay();
                    $days = max(1, (int) ($a->treatment_days ?? 1));
                    $end = (clone $start)->addDays($days - 1)->endOfDay();
                    return $end->gte($startWeek) && $start->lte($endWeek);
                });
        } else {
            // Month View
            $startMonth = (clone $carbonDate)->startOfMonth();
            $endMonth = (clone $carbonDate)->endOfMonth();
            $appointments = (clone $query)
                ->whereDate('appointment_date', '<=', $endMonth->toDateString())
                ->whereDate('appointment_date', '>=', (clone $startMonth)->subDays(90)->toDateString())
                ->orderBy('token_number', 'asc')
                ->get()
                ->filter(function ($a) use ($startMonth, $endMonth) {
                    $start = Carbon::parse($a->appointment_date)->startOfDay();
                    $days = max(1, (int) ($a->treatment_days ?? 1));
                    $end = (clone $start)->addDays($days - 1)->endOfDay();
                    return $end->gte($startMonth) && $start->lte($endMonth);
                });
        }

        $doctors = Doctor::active()->get();
        $patients = $loggedInDoctor 
            ? Patient::where('doctor_id', $loggedInDoctor->id)->orderBy('first_name')->get()
            : Patient::orderBy('first_name')->get();

        $clinics = $loggedInDoctor
            ? ($loggedInDoctor->clinics()->where('is_active', true)->get()->isNotEmpty()
                ? $loggedInDoctor->clinics()->where('is_active', true)->get()
                : Clinic::where('is_active', true)->get())
            : Clinic::where('is_active', true)->get();

        $categories = \App\Models\TreatmentCategory::active()->orderBy('name')->get();

        return view('appointments.calendar', compact('appointments', 'view', 'selectedDate', 'carbonDate', 'doctors', 'patients', 'clinics', 'categories', 'loggedInDoctor', 'clinicId', 'categoryId'));
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

        $isHoliday = \App\Models\ClinicHoliday::isHolidayDate($dateFormatted, $clinic?->id, $doctor->id);
        if ($isHoliday) {
            $isAvailable = false;
        }

        $effectiveFee = $clinic ? $clinic->consultation_fee : $doctor->consultation_fee;

        if (!$isAvailable) {
            $msg = $isHoliday 
                ? "Clinic / Dr. {$doctor->name} is off on {$dateFormatted} due to scheduled holiday."
                : "Dr. {$doctor->name} is unavailable at " . ($clinic ? $clinic->name : 'this clinic') . " on {$dayOfWeek}s.";

            return response()->json([
                'success' => true,
                'doctor' => ['id' => $doctor->id, 'name' => $doctor->name, 'fee' => $effectiveFee],
                'clinic' => $clinic ? ['id' => $clinic->id, 'name' => $clinic->name] : null,
                'date' => $dateFormatted,
                'day' => $dayOfWeek,
                'is_available' => false,
                'message' => $msg,
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

    /**
     * Skip today's/selected appointment (when patient is absent/not attending)
     * and push this session after the patient's last scheduled package appointment.
     */
    public function skipToEnd(Request $request, $id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);

        if ($appointment->status === 'completed') {
            return back()->with('error', 'Cannot skip an appointment that is already marked as completed.');
        }

        $oldDate = $appointment->appointment_date;
        $oldDateFormatted = $oldDate ? $oldDate->format('d M Y') : 'today';

        // Find the patient's last scheduled appointment date among non-cancelled appointments
        $lastApt = Appointment::where('patient_id', $appointment->patient_id)
            ->where('id', '!=', $appointment->id)
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('appointment_date', 'desc')
            ->first();

        $baseDate = ($lastApt && $lastApt->appointment_date)
            ? Carbon::parse($lastApt->appointment_date)
            : ($oldDate ? Carbon::parse($oldDate) : Carbon::today());

        if ($oldDate && $baseDate->lt(Carbon::parse($oldDate))) {
            $baseDate = Carbon::parse($oldDate);
        }

        // Calculate next working day after baseDate, skipping Sundays and Holidays
        $newDate = \App\Models\ClinicHoliday::nextWorkingDay($baseDate, $appointment->clinic_id, $appointment->doctor_id);
        $newDateStr = $newDate->format('Y-m-d');
        $newToken = Appointment::nextTokenForDate($newDateStr, $appointment->doctor_id);

        $appointment->appointment_date = $newDateStr;
        $appointment->token_number = $newToken;
        $appointment->status = 'rescheduled';
        $appointment->notes = trim(($appointment->notes ? $appointment->notes . " | " : "") . "Session skipped on {$oldDateFormatted} (Patient Absent) - moved to end of package.");
        $appointment->save();

        AuditLog::record(
            'Appointment Skipped To End',
            'Appointment',
            $appointment->appointment_no,
            "Patient absent on {$oldDateFormatted}. Appointment #{$appointment->appointment_no} moved to end on {$newDate->format('d M Y')} (Token #{$newToken})"
        );

        return back()->with('success', "Session skipped! Appointment #{$appointment->appointment_no} has been moved to {$newDate->format('d M Y (D)')} (Token #{$newToken}) after the patient's last session.");
    }

    public function destroy($id)
    {
        $appointment = Appointment::with(['patient', 'invoice.items', 'primaryInvoice.items'])->findOrFail($id);

        // Disallow deletion of completed appointments
        if ($appointment->status === 'completed') {
            return back()->with('error', 'Completed appointments cannot be deleted as treatment has already been delivered.');
        }

        $no = $appointment->appointment_no;
        $dateStr = $appointment->appointment_date ? $appointment->appointment_date->format('d M Y') : '';
        $feeToDeduct = (float) ($appointment->daily_fee > 0 ? $appointment->daily_fee : $appointment->consultation_fee);

        $invoice = $appointment->resolved_invoice;
        $deductedAmount = 0;

        if ($invoice) {
            // If this appointment was the primary reference on the invoice, re-link to another session in package
            if ($invoice->appointment_id == $appointment->id) {
                $nextApt = Appointment::where(function ($q) use ($invoice) {
                    $q->where('invoice_id', $invoice->id);
                })->where('id', '!=', $appointment->id)->first();

                $invoice->appointment_id = $nextApt ? $nextApt->id : null;
                $invoice->save();
            }

            if ($feeToDeduct > 0) {
                $items = $invoice->items;
                $itemUpdated = false;

                foreach ($items as $item) {
                    if ($item->quantity > 1) {
                        $newQty = $item->quantity - 1;
                        $unitPrice = (float) $item->unit_price;
                        $item->update([
                            'quantity' => $newQty,
                            'total' => max(0, $newQty * $unitPrice),
                            'item_description' => preg_replace('/\(\d+\s*Days Treatment Package\)/i', "({$newQty} Days Treatment Package)", $item->item_description),
                        ]);
                        $deductedAmount = $unitPrice;
                        $itemUpdated = true;
                        break;
                    } elseif ($item->quantity == 1 && $items->count() > 1) {
                        $deductedAmount = (float) $item->total;
                        $item->delete();
                        $itemUpdated = true;
                        break;
                    }
                }

                if (!$itemUpdated) {
                    $deductedAmount = min((float) $invoice->subtotal, $feeToDeduct);
                    if ($items->count() === 1) {
                        $singleItem = $items->first();
                        $newSub = max(0, (float) $singleItem->total - $deductedAmount);
                        if ($newSub <= 0) {
                            $singleItem->delete();
                        } else {
                            $singleItem->update(['total' => $newSub]);
                        }
                    } else {
                        $invoice->subtotal = max(0, (float) $invoice->subtotal - $deductedAmount);
                    }
                }

                // Update package note text if it mentions total days
                if (!empty($invoice->notes)) {
                    $remainingCount = Appointment::where('invoice_id', $invoice->id)->where('id', '!=', $appointment->id)->count();
                    $invoice->notes = preg_replace('/Total \d+ Days/i', "Total {$remainingCount} Days", $invoice->notes);
                    $invoice->notes = preg_replace('/for \d+ days/i', "for {$remainingCount} days", $invoice->notes);
                }

                // Recalculate invoice totals and payment status
                $invoice->recalculate();
            }
        }

        // Delete the appointment
        $appointment->delete();

        AuditLog::record(
            'Appointment Deleted',
            'Appointment',
            $no,
            "Appointment {$no} ({$dateStr}) deleted. Fee deducted: ₹" . number_format($deductedAmount, 2) . " from invoice " . ($invoice ? "#{$invoice->invoice_no}" : 'N/A')
        );

        $msg = "Appointment #{$no} deleted successfully.";
        if ($deductedAmount > 0) {
            $msg .= " ₹" . number_format($deductedAmount, 2) . " has been deducted from invoice and due payment recalculated.";
        }

        return back()->with('success', $msg);
    }
}
