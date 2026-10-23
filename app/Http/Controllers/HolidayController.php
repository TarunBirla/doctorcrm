<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClinicHoliday;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        ClinicHoliday::ensureTableExists();

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);

        $query = ClinicHoliday::with(['clinic', 'doctor'])->orderBy('holiday_date', 'desc');

        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $clinicIds = $loggedInDoctor->allClinicIds();
            $query->where(function ($q) use ($loggedInDoctor, $clinicIds) {
                $q->whereNull('doctor_id')
                  ->orWhere('doctor_id', $loggedInDoctor->id);
                if (!empty($clinicIds)) {
                    $q->orWhereIn('clinic_id', $clinicIds);
                }
            });
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('clinic_id')) {
            $query->where('clinic_id', $request->input('clinic_id'));
        }

        $holidays = $query->paginate(15)->withQueryString();

        // Dropdown clinics and doctors
        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $clinicIds = $loggedInDoctor->allClinicIds();
            $clinics = !empty($clinicIds) ? Clinic::whereIn('id', $clinicIds)->where('is_active', true)->get() : Clinic::where('is_active', true)->get();
            $doctors = collect([$loggedInDoctor]);
        } else {
            $clinics = Clinic::where('is_active', true)->orderBy('name')->get();
            $doctors = Doctor::active()->orderBy('name')->get();
        }

        $todayStr = now()->toDateString();
        $todayAppointmentsCount = Appointment::whereDate('appointment_date', $todayStr)
            ->whereIn('status', ['scheduled', 'waiting', 'confirmed', 'in_consultation'])
            ->when($loggedInDoctor, fn($q) => $q->where('doctor_id', $loggedInDoctor->id))
            ->count();

        $upcomingHolidaysCount = ClinicHoliday::whereDate('holiday_date', '>=', $todayStr)->count();

        return view('holidays.index', compact(
            'holidays', 'clinics', 'doctors', 'loggedInDoctor', 'currentRole',
            'todayAppointmentsCount', 'upcomingHolidaysCount', 'todayStr'
        ));
    }

    public function store(Request $request)
    {
        ClinicHoliday::ensureTableExists();

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);

        $validated = $request->validate([
            'holiday_date' => 'required|date',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'clinic_id' => 'nullable|exists:clinics,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'auto_reschedule' => 'nullable|boolean',
        ]);

        if ($currentRole === 'doctor' && $loggedInDoctor && empty($validated['doctor_id'])) {
            $validated['doctor_id'] = $loggedInDoctor->id;
        }

        $dateStr = Carbon::parse($validated['holiday_date'])->toDateString();

        // Check if holiday already exists for this date and scope
        $existing = ClinicHoliday::whereDate('holiday_date', $dateStr)
            ->where('clinic_id', $validated['clinic_id'] ?? null)
            ->where('doctor_id', $validated['doctor_id'] ?? null)
            ->first();

        if ($existing) {
            $existing->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
            ]);
            $holiday = $existing;
        } else {
            $holiday = ClinicHoliday::create([
                'holiday_date' => $dateStr,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'clinic_id' => $validated['clinic_id'] ?? null,
                'doctor_id' => $validated['doctor_id'] ?? null,
            ]);
        }

        $rescheduledCount = 0;
        $shouldReschedule = $request->boolean('auto_reschedule');

        if ($shouldReschedule) {
            $rescheduledCount = $this->rescheduleAppointmentsForHoliday($dateStr, $holiday->clinic_id, $holiday->doctor_id, $holiday->title);
        }

        AuditLog::record(
            'Clinic Holiday Added',
            'ClinicHoliday',
            (string) $holiday->id,
            "Added holiday '{$holiday->title}' on {$dateStr}" . ($rescheduledCount > 0 ? " and auto-rescheduled {$rescheduledCount} appointments to end of package" : "")
        );

        $msg = "Holiday '{$holiday->title}' for " . Carbon::parse($dateStr)->format('d M Y') . " saved successfully!";
        if ($rescheduledCount > 0) {
            $msg .= " Total {$rescheduledCount} patient appointments were automatically shifted to after their last scheduled package date.";
        }

        return redirect()->route('holidays.index')->with('success', $msg);
    }

    /**
     * Emergency off action for today or specific date
     */
    public function emergencyOff(Request $request)
    {
        ClinicHoliday::ensureTableExists();

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);

        $validated = $request->validate([
            'date' => 'required|date',
            'reason' => 'nullable|string|max:150',
            'clinic_id' => 'nullable|exists:clinics,id',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $dateStr = Carbon::parse($validated['date'])->toDateString();
        $title = $validated['reason'] ?: 'Doctor Emergency Off / Clinic Leave';

        $holiday = ClinicHoliday::firstOrCreate(
            [
                'holiday_date' => $dateStr,
                'clinic_id' => $validated['clinic_id'] ?? null,
                'doctor_id' => $loggedInDoctor ? $loggedInDoctor->id : ($validated['doctor_id'] ?? null),
            ],
            [
                'title' => $title,
                'description' => 'Marked as Emergency Off. Appointments automatically postponed to after package end date.',
            ]
        );

        $rescheduledCount = $this->rescheduleAppointmentsForHoliday($dateStr, $holiday->clinic_id, $holiday->doctor_id, $title);

        AuditLog::record(
            'Emergency Off Applied',
            'ClinicHoliday',
            (string) $holiday->id,
            "Emergency off marked on {$dateStr}. {$rescheduledCount} appointments pushed to after package end."
        );

        return back()->with('success', "Emergency Off applied for " . Carbon::parse($dateStr)->format('d M Y') . "! {$rescheduledCount} patient appointments have been moved to after their last package date.");
    }

    public function destroy($id)
    {
        $holiday = ClinicHoliday::findOrFail($id);
        $title = $holiday->title;
        $date = $holiday->holiday_date->format('d M Y');
        $holiday->delete();

        AuditLog::record('Clinic Holiday Removed', 'ClinicHoliday', (string) $id, "Deleted holiday '{$title}' on {$date}");

        return redirect()->route('holidays.index')->with('success', "Holiday '{$title}' ({$date}) deleted successfully.");
    }

    /**
     * Helper to reschedule appointments on a holiday date to after each patient's last package appointment
     */
    protected function rescheduleAppointmentsForHoliday(string $dateStr, ?int $clinicId, ?int $doctorId, string $reason): int
    {
        $query = Appointment::whereDate('appointment_date', $dateStr)
            ->whereIn('status', ['scheduled', 'waiting', 'confirmed', 'in_consultation']);

        if ($clinicId) {
            $query->where('clinic_id', $clinicId);
        }
        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        $affectedAppointments = $query->get();
        $count = 0;

        foreach ($affectedAppointments as $apt) {
            // Find patient's last scheduled appointment date
            $lastApt = Appointment::where('patient_id', $apt->patient_id)
                ->where('id', '!=', $apt->id)
                ->whereNotIn('status', ['cancelled'])
                ->orderBy('appointment_date', 'desc')
                ->first();

            $baseDate = ($lastApt && $lastApt->appointment_date)
                ? Carbon::parse($lastApt->appointment_date)
                : Carbon::parse($dateStr);

            if ($baseDate->lt(Carbon::parse($dateStr))) {
                $baseDate = Carbon::parse($dateStr);
            }

            // Find next working day after baseDate (skipping Sundays and Holidays)
            $newDate = ClinicHoliday::nextWorkingDay($baseDate, $apt->clinic_id, $apt->doctor_id);
            $newDateStr = $newDate->format('Y-m-d');
            $newToken = Appointment::nextTokenForDate($newDateStr, $apt->doctor_id);

            $oldDateStr = $apt->appointment_date->format('d M Y');
            $apt->update([
                'appointment_date' => $newDateStr,
                'token_number' => $newToken,
                'status' => 'rescheduled',
                'notes' => trim(($apt->notes ? $apt->notes . " | " : "") . "Auto-rescheduled from {$oldDateStr} due to Off/Holiday: {$reason}"),
            ]);

            $count++;
        }

        return $count;
    }
}
