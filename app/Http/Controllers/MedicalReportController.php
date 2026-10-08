<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalReport;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\AuditLog;
use App\Models\Doctor;
use Illuminate\Support\Facades\Storage;

class MedicalReportController extends Controller
{
    private function authorizeReportAccess(MedicalReport $report)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor && $report->patient && $report->patient->doctor_id && $report->patient->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized: You can only access medical reports of your own patients.');
            }
        }
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');

        $query = MedicalReport::with(['patient', 'visit']);

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor) {
                $query->whereHas('patient', function ($q) use ($loggedInDoctor) {
                    $q->where('doctor_id', $loggedInDoctor->id)
                      ->orWhereHas('appointments', fn($aq) => $aq->where('doctor_id', $loggedInDoctor->id))
                      ->orWhereHas('visits', fn($vq) => $vq->where('doctor_id', $loggedInDoctor->id));
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (!empty($search)) {
            $query->where(function ($sq) use ($search) {
                $sq->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('patient_id', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                })->orWhere('report_name', 'like', "%{$search}%")
                  ->orWhere('laboratory', 'like', "%{$search}%");
            });
        }

        if (!empty($type)) {
            $query->where('report_type', $type);
        }

        $reports = $query->orderBy('report_date', 'desc')->paginate(12)->withQueryString();
        $patients = $loggedInDoctor 
            ? Patient::where(function ($q) use ($loggedInDoctor) {
                $q->where('doctor_id', $loggedInDoctor->id)
                  ->orWhereHas('appointments', fn($aq) => $aq->where('doctor_id', $loggedInDoctor->id))
                  ->orWhereHas('visits', fn($vq) => $vq->where('doctor_id', $loggedInDoctor->id));
            })->orderBy('first_name')->get()
            : Patient::orderBy('first_name')->get();

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

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $patient = Patient::findOrFail($validated['patient_id']);
            if ($loggedInDoctor && $patient->doctor_id && $patient->doctor_id !== $loggedInDoctor->id) {
                abort(403, 'Unauthorized: You can only upload medical reports for your own registered patients.');
            }
        }

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

    public function preview($id)
    {
        $report = MedicalReport::with(['patient', 'visit'])->findOrFail($id);
        $this->authorizeReportAccess($report);

        if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
            $path = Storage::disk('public')->path($report->file_path);
            $mimeType = mime_content_type($path) ?: 'application/octet-stream';
            return response()->file($path, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . basename($report->file_path) . '"',
            ]);
        }

        // Digital interactive report preview sheet
        return view('reports.preview', compact('report'));
    }

    public function download($id)
    {
        $report = MedicalReport::with(['patient', 'visit'])->findOrFail($id);
        $this->authorizeReportAccess($report);

        if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
            $ext = pathinfo($report->file_path, PATHINFO_EXTENSION) ?: 'pdf';
            $safeFileName = \Illuminate\Support\Str::slug($report->report_name . '-' . $report->report_no) . '.' . $ext;
            return Storage::disk('public')->download($report->file_path, $safeFileName);
        }

        // If no file uploaded physically, download a formatted text summary dossier
        $content = "CAREPOINT CLINIC & HEALTHCARE\n";
        $content .= "DIAGNOSTIC & LABORATORY REPORT SUMMARY\n";
        $content .= "=================================================\n\n";
        $content .= "Report No:       " . $report->report_no . "\n";
        $content .= "Report Title:    " . $report->report_name . "\n";
        $content .= "Category:        " . $report->report_type . "\n";
        $content .= "Date:            " . $report->report_date->format('d M Y') . "\n";
        $content .= "Patient:         " . $report->patient->full_name . " (" . $report->patient->patient_id . ")\n";
        $content .= "Laboratory:      " . ($report->laboratory ?? 'CarePoint Diagnostic') . "\n\n";
        $content .= "FINDINGS & INTERPRETATION:\n";
        $content .= ($report->description ?? 'Clinical laboratory examination record.') . "\n\n";
        $content .= "DOCTOR CLINICAL NOTES:\n";
        $content .= ($report->doctor_notes ?? 'No additional remarks.') . "\n\n";
        $content .= "=================================================\n";
        $content .= "Generated from CarePoint Clinic Management System\n";

        $fileName = \Illuminate\Support\Str::slug($report->report_name . '-' . $report->report_no) . '.txt';

        return response($content)
            ->header('Content-Type', 'text/plain')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function destroy($id)
    {
        $report = MedicalReport::with('patient')->findOrFail($id);
        $this->authorizeReportAccess($report);

        if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
            Storage::disk('public')->delete($report->file_path);
        }
        $name = $report->report_name;
        $report->delete();

        AuditLog::record('Medical Report Deleted', 'MedicalReport', (string) $id, "Deleted report {$name}");

        return back()->with('success', "Medical report deleted successfully.");
    }
}
