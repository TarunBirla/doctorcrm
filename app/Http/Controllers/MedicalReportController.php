<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalReport;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Storage;

class MedicalReportController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');

        $query = MedicalReport::with(['patient', 'visit']);

        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%");
            })->orWhere('report_name', 'like', "%{$search}%")
              ->orWhere('laboratory', 'like', "%{$search}%");
        }

        if (!empty($type)) {
            $query->where('report_type', $type);
        }

        $reports = $query->orderBy('report_date', 'desc')->paginate(12)->withQueryString();
        $patients = Patient::orderBy('first_name')->get();

        return view('reports.medical', compact('reports', 'patients', 'search', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'visit_id' => 'nullable|exists:visits,id',
            'report_name' => 'required|string|max:200',
            'report_type' => 'required|string',
            'report_date' => 'required|date',
            'laboratory' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'doctor_notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240',
        ]);

        $filePath = null;
        $fileSize = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filePath = $file->store('medical_reports', 'public');
            $fileSize = round($file->getSize() / 1024, 1) . ' KB';
        }

        $reportNo = MedicalReport::generateReportNo();

        $report = MedicalReport::create([
            'report_no' => $reportNo,
            'patient_id' => $validated['patient_id'],
            'visit_id' => $validated['visit_id'] ?? null,
            'report_name' => $validated['report_name'],
            'report_type' => $validated['report_type'],
            'report_date' => $validated['report_date'],
            'laboratory' => $validated['laboratory'] ?? null,
            'description' => $validated['description'] ?? null,
            'doctor_notes' => $validated['doctor_notes'] ?? null,
            'file_path' => $filePath,
            'file_size' => $fileSize,
        ]);

        AuditLog::record('Medical Report Uploaded', 'MedicalReport', $reportNo, "Uploaded {$report->report_name} for patient {$report->patient->full_name}");

        return back()->with('success', "Medical report #{$reportNo} uploaded and attached successfully.");
    }

    public function destroy($id)
    {
        $report = MedicalReport::findOrFail($id);
        if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
            Storage::disk('public')->delete($report->file_path);
        }
        $name = $report->report_name;
        $report->delete();

        AuditLog::record('Medical Report Deleted', 'MedicalReport', (string) $id, "Deleted report {$name}");

        return back()->with('success', "Medical report deleted successfully.");
    }
}
