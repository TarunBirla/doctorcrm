<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Exercise;
use App\Models\TreatmentCategory;
use App\Models\AuditLog;
use App\Models\Visit;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $date = $request->get('date');
        $type = $request->get('type');
        $clinicId = $request->get('clinic_id');

        $query = Prescription::with(['patient.clinic', 'doctor', 'clinic', 'items']);

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
            $query->where(function ($q) use ($search) {
                $q->whereHas('patient', function ($pq) use ($search) {
                    $pq->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('patient_id', 'like', "%{$search}%");
                })
                ->orWhere('prescription_no', 'like', "%{$search}%")
                ->orWhere('diagnosis_summary', 'like', "%{$search}%");
            });
        }

        if (!empty($date)) {
            $query->where('prescription_date', $date);
        }

        if (!empty($type)) {
            $query->where('assessment_type', $type);
        }

        if (!empty($clinicId)) {
            $query->where('clinic_id', $clinicId);
        }

        $prescriptions = $query->orderBy('prescription_date', 'desc')->paginate(12)->withQueryString();
        $clinics = Clinic::where('is_active', true)->get();

        return view('prescriptions.index', compact('prescriptions', 'search', 'date', 'type', 'clinicId', 'clinics'));
    }

    public function show($id)
    {
        $prescription = Prescription::with(['patient.medicalHistory', 'patient.clinic', 'doctor', 'clinic', 'items', 'visit'])->findOrFail($id);
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor && $prescription->doctor_id !== $loggedInDoctor->id && $prescription->patient?->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized access to prescription.');
            }
        }

        $clinic = $prescription->clinic ?? $prescription->patient?->clinic ?? Clinic::first() ?? new Clinic();
        $allClinics = Clinic::where('is_active', true)->get();

        return view('prescriptions.show', compact('prescription', 'clinic', 'allClinics'));
    }

    public function print($id)
    {
        $prescription = Prescription::with(['patient.medicalHistory', 'patient.clinic', 'doctor', 'clinic', 'items', 'visit'])->findOrFail($id);
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor && $prescription->doctor_id !== $loggedInDoctor->id && $prescription->patient?->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized access to prescription.');
            }
        }

        $clinic = $prescription->clinic ?? $prescription->patient?->clinic ?? Clinic::first() ?? new Clinic();
        $allClinics = Clinic::where('is_active', true)->get();

        return view('prescriptions.print', compact('prescription', 'clinic', 'allClinics'));
    }

    public function create(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $patients = $loggedInDoctor ? Patient::with('clinic')->where('doctor_id', $loggedInDoctor->id)->orderBy('first_name')->get() : collect();
            $doctors = collect($loggedInDoctor ? [$loggedInDoctor] : []);
            
            $clinics = Clinic::where('doctor_id', $loggedInDoctor->id ?? 0)
                ->orWhereHas('doctors', fn($q) => $q->where('doctors.id', $loggedInDoctor->id ?? 0))
                ->where('is_active', true)
                ->get();
            if ($clinics->isEmpty()) {
                $clinics = Clinic::where('is_active', true)->get();
            }
        } else {
            $patients = Patient::with('clinic')->orderBy('first_name')->get();
            $doctors = Doctor::where('is_active', true)->get();
            $clinics = Clinic::where('is_active', true)->get();
        }

        $exercises = Exercise::where('is_active', true)->with('category')->orderBy('name')->get();
        $categories = TreatmentCategory::where('is_active', true)->orderBy('name')->get();

        $patientId = $request->get('patient_id');
        $selectedPatient = $patientId ? Patient::with(['clinic', 'category'])->find($patientId) : null;
        $defaultType = $request->get('type', 'musculoskeletal');

        return view('prescriptions.create', compact('patients', 'doctors', 'clinics', 'exercises', 'categories', 'selectedPatient', 'defaultType'));
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
            'doctor_id' => 'required|exists:doctors,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'prescription_date' => 'required|date',
            'assessment_type' => 'required|in:musculoskeletal,neurological,general',
            'diagnosis_summary' => 'required|string',
            'assessment_data' => 'nullable|array',
            'prescribed_exercises' => 'nullable|array',
            'modalities' => 'nullable|string',
            'treatment_days' => 'nullable|integer|min:1',
            'advice' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'items' => 'nullable|array',
            'items.*.medicine_name' => 'nullable|string',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'nullable|string',
            'items.*.duration' => 'nullable|string',
            'items.*.route' => 'nullable|string',
            'items.*.timing' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        $patient = Patient::findOrFail($validated['patient_id']);

        if ($loggedInDoctor) {
            $validated['doctor_id'] = $loggedInDoctor->id;
            if ($patient->doctor_id && $patient->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized access: Patient belongs to another doctor.');
            }
        }

        // Clinic resolution: explicitly selected > patient's assigned clinic > doctor clinic > first clinic
        $clinicId = $validated['clinic_id'] ?? $patient->clinic_id;
        if (!$clinicId && $loggedInDoctor) {
            $clinicId = $loggedInDoctor->clinics()->first()?->id ?? $loggedInDoctor->ownedClinics()->first()?->id;
        }
        if (!$clinicId) {
            $clinicId = Clinic::first()?->id;
        }

        $prescriptionNo = Prescription::generatePrescriptionNo();

        // Create visit if not present
        $visit = Visit::create([
            'visit_no' => Visit::generateVisitNo(),
            'patient_id' => $patient->id,
            'doctor_id' => $validated['doctor_id'],
            'visit_date' => $validated['prescription_date'],
            'visit_type' => ucfirst($validated['assessment_type']) . ' Assessment',
            'chief_complaint' => $request->input('assessment_data.chief_complaints') ?? 'Physiotherapy Assessment & Consultation',
            'diagnosis_summary' => $validated['diagnosis_summary'],
            'treatment_plan' => $validated['advice'] ?? 'Physiotherapy protocol initialized',
            'follow_up_date' => $validated['follow_up_date'] ?? null,
        ]);

        $prescription = Prescription::create([
            'prescription_no' => $prescriptionNo,
            'visit_id' => $visit->id,
            'patient_id' => $patient->id,
            'doctor_id' => $validated['doctor_id'],
            'clinic_id' => $clinicId,
            'prescription_date' => $validated['prescription_date'],
            'assessment_type' => $validated['assessment_type'],
            'assessment_data' => $request->input('assessment_data') ?? [],
            'prescribed_exercises' => $request->input('prescribed_exercises') ?? [],
            'modalities' => $validated['modalities'] ?? null,
            'treatment_days' => $validated['treatment_days'] ?? 7,
            'diagnosis_summary' => $validated['diagnosis_summary'],
            'advice' => $validated['advice'] ?? null,
            'follow_up_date' => $validated['follow_up_date'] ?? null,
        ]);

        // If medicines were added
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (!empty($item['medicine_name'])) {
                    PrescriptionItem::create([
                        'prescription_id' => $prescription->id,
                        'medicine_name' => $item['medicine_name'],
                        'dosage' => $item['dosage'] ?? '',
                        'frequency' => $item['frequency'] ?? '1-0-1',
                        'duration' => $item['duration'] ?? '5 Days',
                        'route' => $item['route'] ?? 'Oral',
                        'timing' => $item['timing'] ?? 'After Food',
                        'instructions' => $item['instructions'] ?? '',
                    ]);
                }
            }
        }

        // Also update patient clinic if patient did not have one
        if (!$patient->clinic_id && $clinicId) {
            $patient->update(['clinic_id' => $clinicId]);
        }

        AuditLog::record(
            'Prescription Created',
            'Prescription',
            $prescriptionNo,
            "Generated {$validated['assessment_type']} clinical assessment & prescription #{$prescriptionNo} for {$patient->full_name}"
        );

        return redirect()->route('prescriptions.show', $prescription->id)
            ->with('success', "Prescription & Clinical Assessment #{$prescriptionNo} created successfully!");
    }
}
