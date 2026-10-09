<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientProgress;
use App\Models\Patient;
use App\Models\AuditLog;
use App\Models\Doctor;

class PatientProgressController extends Controller
{
    public function index(Request $request)
    {
        $patientId = $request->get('patient_id');
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;

        $patientsQuery = Patient::orderBy('first_name');
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor) {
                $patientsQuery->where(function ($q) use ($loggedInDoctor) {
                    $q->where('doctor_id', $loggedInDoctor->id)
                      ->orWhereHas('appointments', fn($aq) => $aq->where('doctor_id', $loggedInDoctor->id))
                      ->orWhereHas('visits', fn($vq) => $vq->where('doctor_id', $loggedInDoctor->id));
                });
            } else {
                $patientsQuery->whereRaw('1 = 0');
            }
        }

        $clinicId = $request->get('clinic_id');
        $categoryId = $request->get('category_id');

        if (!empty($clinicId)) {
            $patientsQuery->where(function ($q) use ($clinicId) {
                $q->where('clinic_id', $clinicId)
                  ->orWhereHas('appointments', fn($aq) => $aq->where('clinic_id', $clinicId));
            });
        }

        if (!empty($categoryId)) {
            $patientsQuery->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                  ->orWhereHas('appointments', fn($aq) => $aq->where('category_id', $categoryId));
            });
        }

        $patients = $patientsQuery->get();

        $selectedPatient = null;
        if ($patientId) {
            $candidate = Patient::with('progressRecords')->find($patientId);
            if ($candidate) {
                if ($loggedInDoctor && $candidate->doctor_id && $candidate->doctor_id !== $loggedInDoctor->id) {
                    $selectedPatient = $patients->first();
                } else {
                    $selectedPatient = $candidate;
                }
            }
        } else {
            $selectedPatient = $patients->first();
        }

        $progressRecords = collect();
        $chartDates = [];
        $chartWeights = [];
        $chartSystolic = [];
        $chartDiastolic = [];
        $chartPulse = [];
        $chartPain = [];

        if ($selectedPatient) {
            $progressRecords = $selectedPatient->progressRecords()->orderBy('recorded_date', 'asc')->get();
            foreach ($progressRecords as $rec) {
                $chartDates[] = $rec->recorded_date->format('d M Y');
                $chartWeights[] = $rec->weight;
                $chartSystolic[] = $rec->bp_systolic;
                $chartDiastolic[] = $rec->bp_diastolic;
                $chartPulse[] = $rec->pulse;
                $chartPain[] = $rec->pain_level;
            }
        }

        $clinics = $loggedInDoctor
            ? ($loggedInDoctor->clinics()->where('is_active', true)->get()->isNotEmpty()
                ? $loggedInDoctor->clinics()->where('is_active', true)->get()
                : \App\Models\Clinic::where('is_active', true)->get())
            : \App\Models\Clinic::where('is_active', true)->get();

        $categories = \App\Models\TreatmentCategory::active()->orderBy('name')->get();

        return view('progress.index', compact(
            'patients', 'selectedPatient', 'progressRecords',
            'chartDates', 'chartWeights', 'chartSystolic', 'chartDiastolic', 'chartPulse', 'chartPain',
            'clinics', 'categories', 'clinicId', 'categoryId'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'recorded_date' => 'required|date',
            'weight' => 'nullable|numeric|min:1|max:300',
            'height' => 'nullable|numeric|min:30|max:250',
            'bp_systolic' => 'nullable|integer|min:50|max:260',
            'bp_diastolic' => 'nullable|integer|min:30|max:160',
            'pulse' => 'nullable|integer|min:30|max:220',
            'temperature' => 'nullable|numeric|min:90|max:110',
            'spo2' => 'nullable|integer|min:50|max:100',
            'pain_level' => 'nullable|integer|min:0|max:10',
            'symptoms_assessment' => 'nullable|string',
            'treatment_response' => 'nullable|string',
            'doctor_notes' => 'nullable|string',
        ]);

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $patient = Patient::findOrFail($validated['patient_id']);
            if ($loggedInDoctor && $patient->doctor_id && $patient->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized access: You can only record progress for your own patients.');
            }
        }

        $bmi = null;
        if (!empty($validated['weight']) && !empty($validated['height']) && $validated['height'] > 0) {
            $heightInM = $validated['height'] / 100.0;
            $bmi = round($validated['weight'] / ($heightInM * $heightInM), 1);
        }

        $progress = PatientProgress::create([
            'patient_id' => $validated['patient_id'],
            'recorded_date' => $validated['recorded_date'],
            'weight' => $validated['weight'] ?? null,
            'bp_systolic' => $validated['bp_systolic'] ?? null,
            'bp_diastolic' => $validated['bp_diastolic'] ?? null,
            'pulse' => $validated['pulse'] ?? null,
            'temperature' => $validated['temperature'] ?? null,
            'spo2' => $validated['spo2'] ?? null,
            'bmi' => $bmi,
            'pain_level' => $validated['pain_level'] ?? 0,
            'symptoms_assessment' => $validated['symptoms_assessment'] ?? null,
            'treatment_response' => $validated['treatment_response'] ?? null,
            'doctor_notes' => $validated['doctor_notes'] ?? null,
        ]);

        $patient = Patient::find($validated['patient_id']);
        AuditLog::record('Patient Progress Recorded', 'PatientProgress', (string) $progress->id, "Vitals and clinical progress logged for {$patient->full_name}");

        return back()->with('success', 'Patient progress and vitals logged successfully.');
    }
}
