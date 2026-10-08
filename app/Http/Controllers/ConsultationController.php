<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Visit;
use App\Models\VisitDiagnosis;
use App\Models\Diagnosis;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\PatientProgress;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AuditLog;

class ConsultationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $date = $request->get('date');

        $query = Visit::with(['patient', 'doctor', 'diagnoses', 'prescriptions']);

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor) {
                $query->where('doctor_id', $loggedInDoctor->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%");
            })->orWhere('visit_no', 'like', "%{$search}%");
        }

        if (!empty($date)) {
            $query->where('visit_date', $date);
        }

        $visits = $query->orderBy('visit_date', 'desc')->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        return view('consultations.index', compact('visits', 'search', 'date'));
    }

    public function create(Request $request)
    {
        $appointmentId = $request->get('appointment_id');
        $patientId = $request->get('patient_id');

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
        }

        $appointment = null;
        if ($appointmentId) {
            $appointment = Appointment::with(['patient.medicalHistory', 'patient.visits.prescriptions', 'patient.reports'])->findOrFail($appointmentId);
            $patient = $appointment->patient;
            $doctor = $appointment->doctor;
        } elseif ($patientId) {
            $patient = Patient::with(['medicalHistory', 'visits.prescriptions', 'reports'])->findOrFail($patientId);
            $doctor = $loggedInDoctor ?? Doctor::first();
        } else {
            return redirect()->route('queue.index')->with('error', 'Please select a patient from queue or directory to start consultation.');
        }

        if ($loggedInDoctor && $patient) {
            if ($patient->doctor_id && $patient->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized access: Patient is registered under another doctor.');
            }
        }

        // Get previous visit for smart revisit carry-forward
        $previousVisit = $patient->visits()->with(['diagnoses', 'prescriptions.items'])->latest('visit_date')->first();

        $diagnosesCatalog = Diagnosis::orderBy('name')->get();
        $doctors = Doctor::all();

        return view('consultations.create', compact(
            'patient', 'appointment', 'doctor', 'previousVisit', 'diagnosesCatalog', 'doctors'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'visit_date' => 'required|date',
            'visit_type' => 'required|in:New,Follow-up,Revisit,Emergency',
            'chief_complaint' => 'required|string',
            'symptoms' => 'nullable|string',
            'diagnosis_summary' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            // Vitals
            'bp_sys' => 'nullable|numeric',
            'bp_dia' => 'nullable|numeric',
            'pulse' => 'nullable|numeric',
            'temperature' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'height' => 'nullable|numeric',
            'spo2' => 'nullable|numeric',
            'pain_level' => 'nullable|integer|min:0|max:10',
            // Prescription medicines arrays
            'medicines' => 'nullable|array',
            'medicines.*.name' => 'required_with:medicines|string',
            'medicines.*.dosage' => 'nullable|string',
            'medicines.*.frequency' => 'nullable|string',
            'medicines.*.duration' => 'nullable|string',
            'medicines.*.route' => 'nullable|string',
            'medicines.*.timing' => 'nullable|string',
            'medicines.*.instructions' => 'nullable|string',
            'advice' => 'nullable|string',
            // Diagnoses array
            'diagnoses' => 'nullable|array',
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);
        $doctor = Doctor::findOrFail($validated['doctor_id']);

        // Calculate BMI if weight & height provided
        $bmi = null;
        if (!empty($validated['weight']) && !empty($validated['height']) && $validated['height'] > 0) {
            $heightInMeters = $validated['height'] / 100.0;
            $bmi = round($validated['weight'] / ($heightInMeters * $heightInMeters), 1);
        }

        $vitals = [
            'bp_sys' => $validated['bp_sys'] ?? null,
            'bp_dia' => $validated['bp_dia'] ?? null,
            'pulse' => $validated['pulse'] ?? null,
            'temp' => $validated['temperature'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'bmi' => $bmi,
            'spo2' => $validated['spo2'] ?? null,
            'pain_level' => $validated['pain_level'] ?? 0,
        ];

        // 1. Create Visit Record
        $visitNo = Visit::generateVisitNo();
        $visit = Visit::create([
            'visit_no' => $visitNo,
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $validated['appointment_id'] ?? null,
            'visit_date' => $validated['visit_date'],
            'visit_type' => $validated['visit_type'],
            'chief_complaint' => $validated['chief_complaint'],
            'symptoms' => $validated['symptoms'] ?? null,
            'diagnosis_summary' => $validated['diagnosis_summary'] ?? null,
            'vitals_json' => $vitals,
            'clinical_notes' => $validated['clinical_notes'] ?? null,
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
        ]);

        // 2. Save Diagnoses
        if (!empty($validated['diagnoses'])) {
            foreach ($validated['diagnoses'] as $diagName) {
                if (trim($diagName)) {
                    VisitDiagnosis::create([
                        'visit_id' => $visit->id,
                        'diagnosis_name' => trim($diagName),
                        'diagnosis_type' => 'primary',
                    ]);
                }
            }
        } elseif (!empty($validated['diagnosis_summary'])) {
            VisitDiagnosis::create([
                'visit_id' => $visit->id,
                'diagnosis_name' => $validated['diagnosis_summary'],
                'diagnosis_type' => 'primary',
            ]);
        }

        // 3. Save Patient Progress Record
        PatientProgress::create([
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'recorded_date' => $validated['visit_date'],
            'weight' => $validated['weight'] ?? null,
            'bp_systolic' => $validated['bp_sys'] ?? null,
            'bp_diastolic' => $validated['bp_dia'] ?? null,
            'pulse' => $validated['pulse'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
            'spo2' => $validated['spo2'] ?? null,
            'bmi' => $bmi,
            'pain_level' => $validated['pain_level'] ?? 0,
            'symptoms_assessment' => $validated['symptoms'] ?? $validated['chief_complaint'],
            'treatment_response' => $validated['treatment_plan'] ?? null,
            'doctor_notes' => $validated['clinical_notes'] ?? null,
        ]);

        // 4. Save Prescription
        $prescription = null;
        if (!empty($validated['medicines']) && count($validated['medicines']) > 0) {
            $prescriptionNo = Prescription::generatePrescriptionNo();
            $prescription = Prescription::create([
                'prescription_no' => $prescriptionNo,
                'visit_id' => $visit->id,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'prescription_date' => $validated['visit_date'],
                'diagnosis_summary' => $validated['diagnosis_summary'] ?? 'Clinical examination',
                'advice' => $validated['advice'] ?? 'Follow dosage timings strictly.',
                'follow_up_date' => $validated['follow_up_date'] ?? null,
            ]);

            foreach ($validated['medicines'] as $med) {
                if (!empty($med['name'])) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_name' => $med['name'],
                        'dosage' => $med['dosage'] ?? '',
                        'frequency' => $med['frequency'] ?? '1-0-1',
                        'duration' => $med['duration'] ?? '5 Days',
                        'route' => $med['route'] ?? 'Oral',
                        'timing' => $med['timing'] ?? 'After Food',
                        'instructions' => $med['instructions'] ?? '',
                    ]);
                }
            }
        }

        // 5. Update Appointment to Completed if linked
        if (!empty($validated['appointment_id'])) {
            $apt = Appointment::find($validated['appointment_id']);
            if ($apt) {
                $apt->status = 'completed';
                $apt->save();
            }
        }

        // 6. Schedule Follow-up if date specified
        if (!empty($validated['follow_up_date'])) {
            FollowUp::create([
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'visit_id' => $visit->id,
                'follow_up_date' => $validated['follow_up_date'],
                'follow_up_time' => '10:00',
                'reason' => 'Scheduled post-consultation review for ' . ($validated['diagnosis_summary'] ?? 'routine evaluation'),
                'notes' => 'Generated automatically from consultation.',
                'status' => 'scheduled',
            ]);
        }

        AuditLog::record('Consultation Completed', 'Visit', $visit->visit_no, "Completed consultation visit #{$visit->visit_no} for patient {$patient->full_name}");

        // If prescription was generated, redirect to its print/view page, otherwise to visit
        if ($prescription) {
            return redirect()->route('prescriptions.show', $prescription->id)
                ->with('success', "Consultation completed and Prescription #{$prescription->prescription_no} generated successfully!");
        }

        return redirect()->route('patients.show', ['patient' => $patient->id, 'tab' => 'visits'])
            ->with('success', "Consultation visit #{$visit->visit_no} saved successfully!");
    }

    public function show($id)
    {
        $visit = Visit::with(['patient.medicalHistory', 'doctor', 'diagnoses', 'prescriptions.items', 'invoice.items'])->findOrFail($id);
        
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor && $visit->doctor_id !== $loggedInDoctor->id && $visit->patient?->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized access to consultation record.');
            }
        }

        return view('consultations.show', compact('visit'));
    }
}
