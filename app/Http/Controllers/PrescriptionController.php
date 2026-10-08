<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\AuditLog;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $date = $request->get('date');

        $query = Prescription::with(['patient', 'doctor', 'items']);

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
            })->orWhere('prescription_no', 'like', "%{$search}%");
        }

        if (!empty($date)) {
            $query->where('prescription_date', $date);
        }

        $prescriptions = $query->orderBy('prescription_date', 'desc')->paginate(12)->withQueryString();

        return view('prescriptions.index', compact('prescriptions', 'search', 'date'));
    }

    public function show($id)
    {
        $prescription = Prescription::with(['patient.medicalHistory', 'doctor', 'items', 'visit'])->findOrFail($id);
        $clinic = Clinic::first() ?? new Clinic();

        return view('prescriptions.show', compact('prescription', 'clinic'));
    }

    public function print($id)
    {
        $prescription = Prescription::with(['patient.medicalHistory', 'doctor', 'items', 'visit'])->findOrFail($id);
        $clinic = Clinic::first() ?? new Clinic();

        return view('prescriptions.print', compact('prescription', 'clinic'));
    }

    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $patients = Patient::orderBy('first_name')->get();
        $doctors = Doctor::all();
        $selectedPatient = $patientId ? Patient::find($patientId) : null;

        return view('prescriptions.create', compact('patients', 'doctors', 'selectedPatient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'prescription_date' => 'required|date',
            'diagnosis_summary' => 'required|string',
            'advice' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.medicine_name' => 'required|string',
            'items.*.dosage' => 'nullable|string',
            'items.*.frequency' => 'required|string',
            'items.*.duration' => 'required|string',
            'items.*.route' => 'nullable|string',
            'items.*.timing' => 'nullable|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        $prescriptionNo = Prescription::generatePrescriptionNo();

        // Create visit if not present
        $visit = \App\Models\Visit::create([
            'visit_no' => \App\Models\Visit::generateVisitNo(),
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'visit_date' => $validated['visit_date'],
            'visit_type' => 'Prescription Only',
            'chief_complaint' => 'Medication refill / Direct prescription',
            'diagnosis_summary' => $validated['diagnosis_summary'],
        ]);

        $prescription = Prescription::create([
            'prescription_no' => $prescriptionNo,
            'visit_id' => $visit->id,
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'prescription_date' => $validated['prescription_date'],
            'diagnosis_summary' => $validated['diagnosis_summary'],
            'advice' => $validated['advice'],
            'follow_up_date' => $validated['follow_up_date'],
        ]);

        foreach ($validated['items'] as $item) {
            PrescriptionItem::create([
                'prescription_id' => $prescription->id,
                'medicine_name' => $item['medicine_name'],
                'dosage' => $item['dosage'] ?? '',
                'frequency' => $item['frequency'],
                'duration' => $item['duration'],
                'route' => $item['route'] ?? 'Oral',
                'timing' => $item['timing'] ?? 'After Food',
                'instructions' => $item['instructions'] ?? '',
            ]);
        }

        AuditLog::record('Prescription Created', 'Prescription', $prescriptionNo, "Generated prescription #{$prescriptionNo}");

        return redirect()->route('prescriptions.show', $prescription->id)
            ->with('success', "Prescription #{$prescriptionNo} created successfully!");
    }
}
