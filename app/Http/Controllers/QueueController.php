<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\AuditLog;

class QueueController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $doctorId = $request->get('doctor_id');

        $query = Appointment::with(['patient.medicalHistory', 'doctor', 'invoice'])
            ->where('appointment_date', $today);

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        $allToday = $query->orderBy('token_number', 'asc')->get();

        // Separate categories
        $inConsultation = $allToday->where('status', 'in_consultation')->first();
        $waitingList = $allToday->where('status', 'waiting')->sortBy('token_number');
        $nextPatient = $waitingList->first();

        $upcomingList = $allToday->whereIn('status', ['confirmed', 'scheduled'])->sortBy('token_number');
        $completedList = $allToday->where('status', 'completed')->sortByDesc('updated_at');
        $cancelledList = $allToday->whereIn('status', ['cancelled', 'no_show']);

        $doctors = Doctor::all();
        $patients = Patient::orderBy('first_name')->get();

        return view('queue.index', compact(
            'allToday', 'inConsultation', 'waitingList', 'nextPatient',
            'upcomingList', 'completedList', 'cancelledList', 'doctors', 'patients', 'today'
        ));
    }

    public function callPatient(Request $request, $id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);
        
        // Ensure status is waiting
        if ($appointment->status === 'scheduled' || $appointment->status === 'confirmed') {
            $appointment->status = 'waiting';
            $appointment->waiting_since = now();
            $appointment->save();
        }

        AuditLog::record('Patient Called to Room', 'Queue', $appointment->appointment_no, "Called Token #{$appointment->token_number} ({$appointment->patient->full_name}) to Doctor Room");

        return back()->with('success', "Patient {$appointment->patient->full_name} (Token #{$appointment->token_number}) has been summoned to the consultation room.");
    }

    public function startConsultation(Request $request, $id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);
        $appointment->status = 'in_consultation';
        $appointment->save();

        AuditLog::record('Consultation Started', 'Appointment', $appointment->appointment_no, "Doctor started consultation for Token #{$appointment->token_number} ({$appointment->patient->full_name})");

        return redirect()->route('consultations.create', ['appointment_id' => $appointment->id]);
    }

    public function completeConsultation(Request $request, $id)
    {
        $appointment = Appointment::with('patient')->findOrFail($id);
        $appointment->status = 'completed';
        $appointment->save();

        AuditLog::record('Consultation Marked Completed', 'Appointment', $appointment->appointment_no, "Completed consultation for Token #{$appointment->token_number}");

        return back()->with('success', "Consultation for Token #{$appointment->token_number} ({$appointment->patient->full_name}) marked as completed.");
    }
}
