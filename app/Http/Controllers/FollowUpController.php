<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FollowUp;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\AuditLog;

class FollowUpController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $date = $request->get('date');

        $query = FollowUp::with(['patient', 'doctor', 'visit']);

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

        if ($status === 'today') {
            $query->where('follow_up_date', now()->toDateString());
        } elseif ($status === 'upcoming') {
            $query->where('follow_up_date', '>', now()->toDateString())->where('status', 'scheduled');
        } elseif ($status === 'missed') {
            $query->where('status', 'missed');
        } elseif ($status === 'completed') {
            $query->where('status', 'completed');
        }

        if (!empty($date)) {
            $query->where('follow_up_date', $date);
        }

        $followUps = $query->orderBy('follow_up_date', 'asc')->paginate(15)->withQueryString();

        $countQuery = FollowUp::query();
        if ($loggedInDoctor) {
            $countQuery->where('doctor_id', $loggedInDoctor->id);
        } elseif ($currentRole === 'doctor') {
            $countQuery->whereRaw('1 = 0');
        }

        $todayCount = (clone $countQuery)->where('follow_up_date', now()->toDateString())->count();
        $upcomingCount = (clone $countQuery)->where('follow_up_date', '>', now()->toDateString())->where('status', 'scheduled')->count();
        $missedCount = (clone $countQuery)->where('status', 'missed')->count();
        $completedCount = (clone $countQuery)->where('status', 'completed')->count();

        $patients = Patient::orderBy('first_name')->get();
        $doctors = Doctor::all();

        return view('followups.index', compact(
            'followUps', 'status', 'date', 'todayCount', 'upcomingCount',
            'missedCount', 'completedCount', 'patients', 'doctors'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'follow_up_date' => 'required|date',
            'follow_up_time' => 'nullable|string',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $followUp = FollowUp::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'follow_up_date' => $validated['follow_up_date'],
            'follow_up_time' => $validated['follow_up_time'] ?? '10:00',
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'scheduled',
            'reminder_sent' => false,
        ]);

        AuditLog::record('Follow-up Scheduled', 'FollowUp', (string) $followUp->id, "Scheduled follow-up for patient {$followUp->patient->full_name} on {$followUp->follow_up_date->format('d M Y')}");

        return back()->with('success', "Follow-up scheduled successfully for {$followUp->follow_up_date->format('d M Y')}.");
    }

    public function updateStatus(Request $request, $id)
    {
        $followUp = FollowUp::findOrFail($id);
        $newStatus = $request->validate(['status' => 'required|in:scheduled,completed,missed,cancelled'])['status'];
        $followUp->status = $newStatus;
        $followUp->save();

        AuditLog::record('Follow-up Status Updated', 'FollowUp', (string) $followUp->id, "Status changed to {$newStatus}");

        return back()->with('success', "Follow-up status marked as " . ucfirst($newStatus));
    }
}
