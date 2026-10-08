<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientProgress;
use App\Models\Patient;
use App\Models\AuditLog;

class PatientProgressController extends Controller
{
    public function index(Request $request)
    {
        $patientId = $request->get('patient_id');
        $patients = Patient::orderBy('first_name')->get();

        $selectedPatient = $patientId ? Patient::with('progressRecords')->find($patientId) : $patients->first();

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

        return view('progress.index', compact(
            'patients', 'selectedPatient', 'progressRecords',
            'chartDates', 'chartWeights', 'chartSystolic', 'chartDiastolic', 'chartPulse', 'chartPain'
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
