<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\PatientMedicalHistory;
use App\Models\Doctor;
use App\Models\AuditLog;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $bloodGroup = $request->get('blood_group');
        $gender = $request->get('gender');
        $hasDue = $request->get('has_due');

        $query = Patient::with(['medicalHistory', 'invoices']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if (!empty($bloodGroup)) {
            $query->where('blood_group', $bloodGroup);
        }

        if (!empty($gender)) {
            $query->where('gender', $gender);
        }

        if ($hasDue === 'yes') {
            $query->whereHas('invoices', function ($q) {
                $q->whereIn('payment_status', ['unpaid', 'partially_paid', 'due']);
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $totalPatients = Patient::count();
        $malePatients = Patient::where('gender', 'Male')->count();
        $femalePatients = Patient::where('gender', 'Female')->count();
        $patientsWithDues = Patient::whereHas('invoices', function ($q) {
            $q->whereIn('payment_status', ['unpaid', 'partially_paid', 'due']);
        })->count();

        $doctors = Doctor::all();

        return view('patients.index', compact(
            'patients', 'totalPatients', 'malePatients', 'femalePatients',
            'patientsWithDues', 'search', 'bloodGroup', 'gender', 'hasDue', 'doctors'
        ));
    }

    public function create()
    {
        $nextId = Patient::generatePatientId();
        return view('patients.create', compact('nextId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female,Other',
            'age' => 'required|integer|min:0|max:125',
            'dob' => 'nullable|date',
            'mobile' => 'required|string|max:20',
            'alt_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'occupation' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'referral_source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            // Medical history fields
            'conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'surgeries' => 'nullable|string',
            'family_history' => 'nullable|string',
            'current_medications' => 'nullable|string',
        ]);

        $patientId = Patient::generatePatientId();

        $patient = Patient::create([
            'patient_id' => $patientId,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'age' => $validated['age'],
            'dob' => $validated['dob'] ?? null,
            'mobile' => $validated['mobile'],
            'alt_mobile' => $validated['alt_mobile'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'blood_group' => $validated['blood_group'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'emergency_phone' => $validated['emergency_phone'] ?? null,
            'referral_source' => $validated['referral_source'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        PatientMedicalHistory::create([
            'patient_id' => $patient->id,
            'conditions' => $validated['conditions'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'surgeries' => $validated['surgeries'] ?? null,
            'family_history' => $validated['family_history'] ?? null,
            'current_medications' => $validated['current_medications'] ?? null,
            'notes' => 'Initial medical history recorded during registration.',
        ]);

        AuditLog::record('Patient Registered', 'Patient', $patient->patient_id, "Registered patient {$patient->full_name} ({$patient->patient_id})");

        return redirect()->route('patients.show', $patient->id)
            ->with('success', "Patient {$patient->full_name} ({$patient->patient_id}) registered successfully!");
    }

    public function show(Request $request, $id)
    {
        $patient = Patient::with([
            'medicalHistory',
            'appointments.doctor',
            'visits.doctor',
            'visits.diagnoses',
            'visits.prescriptions.items',
            'prescriptions.items',
            'prescriptions.doctor',
            'reports',
            'progressRecords',
            'followUps.doctor',
            'invoices.items',
            'invoices.transactions',
            'paymentTransactions.invoice',
            'communications',
        ])->findOrFail($id);

        $activeTab = $request->get('tab', 'overview');
        $doctors = Doctor::all();

        // Calculate progress chart data
        $progressRecords = $patient->progressRecords()->orderBy('recorded_date', 'asc')->get();
        $progressDates = $progressRecords->pluck('recorded_date')->map(fn($d) => $d->format('d M'))->toArray();
        $progressWeights = $progressRecords->pluck('weight')->filter()->values()->toArray();
        $progressSystolic = $progressRecords->pluck('bp_systolic')->filter()->values()->toArray();
        $progressDiastolic = $progressRecords->pluck('bp_diastolic')->filter()->values()->toArray();
        $progressPulse = $progressRecords->pluck('pulse')->filter()->values()->toArray();

        // Prepare Timeline events
        $timelineEvents = collect();

        // Registration event
        $timelineEvents->push([
            'date' => $patient->created_at,
            'type' => 'registration',
            'title' => 'Patient Registered',
            'description' => "Patient account created with ID {$patient->patient_id}",
            'icon' => 'user-plus',
            'badge' => 'Registration',
            'badge_color' => 'bg-blue-100 text-blue-700',
        ]);

        // Appointments
        foreach ($patient->appointments as $apt) {
            $timelineEvents->push([
                'date' => $apt->created_at,
                'type' => 'appointment',
                'title' => "Appointment #{$apt->appointment_no}",
                'description' => "Scheduled for {$apt->appointment_date->format('d M Y')} at {$apt->appointment_time} (Token #{$apt->token_number}). Status: " . ucfirst($apt->status),
                'icon' => 'calendar',
                'badge' => ucfirst($apt->appointment_type),
                'badge_color' => 'bg-indigo-100 text-indigo-700',
            ]);
        }

        // Visits
        foreach ($patient->visits as $vst) {
            $timelineEvents->push([
                'date' => $vst->created_at,
                'type' => 'visit',
                'title' => "Clinical Visit #{$vst->visit_no}",
                'description' => "Diagnosis: " . ($vst->diagnosis_summary ?? 'General checkup') . ". Chief complaint: {$vst->chief_complaint}",
                'icon' => 'stethoscope',
                'badge' => 'Consultation',
                'badge_color' => 'bg-emerald-100 text-emerald-700',
            ]);
        }

        // Prescriptions
        foreach ($patient->prescriptions as $rx) {
            $timelineEvents->push([
                'date' => $rx->created_at,
                'type' => 'prescription',
                'title' => "Prescription Generated #{$rx->prescription_no}",
                'description' => "Prescribed " . $rx->items->count() . " medication(s). Follow-up: " . ($rx->follow_up_date ? $rx->follow_up_date->format('d M Y') : 'As advised'),
                'icon' => 'pill',
                'badge' => 'Prescription',
                'badge_color' => 'bg-purple-100 text-purple-700',
            ]);
        }

        // Payments
        foreach ($patient->paymentTransactions as $txn) {
            $timelineEvents->push([
                'date' => $txn->created_at,
                'type' => 'payment',
                'title' => "Payment of ₹" . number_format($txn->amount, 2) . " Received",
                'description' => "Transaction #{$txn->transaction_no} via {$txn->payment_method}. Receipt #{$txn->receipt_no}",
                'icon' => 'receipt',
                'badge' => 'Payment',
                'badge_color' => 'bg-emerald-100 text-emerald-700',
            ]);
        }

        // Follow-ups
        foreach ($patient->followUps as $fu) {
            $timelineEvents->push([
                'date' => $fu->created_at,
                'type' => 'follow_up',
                'title' => "Follow-up Scheduled for " . $fu->follow_up_date->format('d M Y'),
                'description' => "Reason: " . ($fu->reason ?? 'Review') . ". Status: " . ucfirst($fu->status),
                'icon' => 'clock',
                'badge' => 'Follow-up',
                'badge_color' => 'bg-amber-100 text-amber-700',
            ]);
        }

        // Sort timeline descending
        $sortedTimeline = $timelineEvents->sortByDesc('date')->values();

        return view('patients.show', compact(
            'patient', 'activeTab', 'doctors', 'progressDates',
            'progressWeights', 'progressSystolic', 'progressDiastolic', 'progressPulse',
            'sortedTimeline'
        ));
    }

    public function edit($id)
    {
        $patient = Patient::findOrFail($id);
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'required|in:Male,Female,Other',
            'age' => 'required|integer|min:0|max:125',
            'dob' => 'nullable|date',
            'mobile' => 'required|string|max:20',
            'alt_mobile' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'blood_group' => 'nullable|string|max:10',
            'occupation' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:50',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone' => 'nullable|string|max:20',
            'referral_source' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $patient->update($validated);

        AuditLog::record('Patient Updated', 'Patient', $patient->patient_id, "Updated details for patient {$patient->full_name}");

        return redirect()->route('patients.show', $patient->id)
            ->with('success', "Patient details updated successfully!");
    }

    public function updateMedicalHistory(Request $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $history = $patient->medicalHistory ?: new PatientMedicalHistory(['patient_id' => $patient->id]);
        $history->conditions = $request->input('conditions');
        $history->allergies = $request->input('allergies');
        $history->surgeries = $request->input('surgeries');
        $history->family_history = $request->input('family_history');
        $history->current_medications = $request->input('current_medications');
        $history->notes = $request->input('notes');
        $history->save();

        AuditLog::record('Medical History Updated', 'Patient', $patient->patient_id, "Updated medical history for {$patient->full_name}");

        return back()->with('success', 'Patient medical history updated successfully.');
    }

    public function destroy($id)
    {
        $patient = Patient::findOrFail($id);
        $name = $patient->full_name;
        $pid = $patient->patient_id;
        $patient->delete();

        AuditLog::record('Patient Deleted', 'Patient', $pid, "Soft deleted patient {$name} ({$pid})");

        return redirect()->route('patients.index')
            ->with('success', "Patient {$name} has been archived successfully.");
    }

    public function printSummary($id)
    {
        $patient = Patient::with([
            'medicalHistory',
            'visits.diagnoses',
            'visits.prescriptions.items',
            'prescriptions.items',
            'reports',
            'progressRecords',
            'invoices',
        ])->findOrFail($id);

        return view('patients.print-summary', compact('patient'));
    }

    public function export()
    {
        $response = new StreamedResponse(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Patient ID', 'First Name', 'Last Name', 'Gender', 'Age', 'Mobile',
                'Email', 'City', 'Blood Group', 'Conditions', 'Allergies', 'Outstanding Due (INR)', 'Created At'
            ]);

            Patient::with('medicalHistory')->chunk(100, function ($patients) use ($handle) {
                foreach ($patients as $p) {
                    fputcsv($handle, [
                        $p->patient_id,
                        $p->first_name,
                        $p->last_name,
                        $p->gender,
                        $p->age,
                        $p->mobile,
                        $p->email ?? '',
                        $p->city ?? '',
                        $p->blood_group ?? '',
                        $p->medicalHistory->conditions ?? '',
                        $p->medicalHistory->allergies ?? '',
                        $p->outstanding_balance,
                        $p->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="patients_export_' . date('Ymd_His') . '.csv"');

        return $response;
    }
}
