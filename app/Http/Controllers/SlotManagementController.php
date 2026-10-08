<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\DoctorSlotOverride;
use App\Models\Appointment;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class SlotManagementController extends Controller
{
    /**
     * Show Calendar & Slot Management UI.
     */
    public function index(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
        }

        // 1. Resolve Available Clinics for this user
        if ($loggedInDoctor) {
            $clinicIds = DB::table('doctor_clinics')
                ->where('doctor_id', $loggedInDoctor->id)
                ->pluck('clinic_id')
                ->merge(Clinic::where('doctor_id', $loggedInDoctor->id)->pluck('id'))
                ->unique();

            $clinics = Clinic::whereIn('id', $clinicIds)->where('is_active', true)->get();
            // Fallback if no clinic yet: get first clinic
            if ($clinics->isEmpty()) {
                $clinics = Clinic::where('is_active', true)->get();
            }
        } else {
            $clinics = Clinic::where('is_active', true)->get();
        }

        // 2. Selected Clinic
        $selectedClinicId = $request->get('clinic_id');
        $selectedClinic = $clinics->firstWhere('id', $selectedClinicId) ?? $clinics->first();

        // 3. Resolve Doctor for the slot schedule
        if ($loggedInDoctor) {
            $doctor = $loggedInDoctor;
        } else {
            $doctorId = $request->get('doctor_id');
            if ($doctorId) {
                $doctor = Doctor::find($doctorId);
            }
            if (!isset($doctor) || !$doctor) {
                $doctor = $selectedClinic?->doctor ?? Doctor::active()->first();
            }
        }

        $allDoctors = Doctor::active()->orderBy('name')->get();

        // 4. Selected Date & Day
        $selectedDate = $request->get('date', now()->toDateString());
        try {
            $carbonDate = Carbon::parse($selectedDate);
        } catch (\Exception $e) {
            $carbonDate = Carbon::today();
            $selectedDate = $carbonDate->toDateString();
        }
        $dayOfWeek = $carbonDate->format('l');

        // 5. Weekly Schedule for this Clinic + Doctor
        $weeklyAvailabilities = collect();
        if ($doctor && $selectedClinic) {
            $weeklyAvailabilities = DoctorAvailability::where('doctor_id', $doctor->id)
                ->where('clinic_id', $selectedClinic->id)
                ->get()
                ->keyBy('day_of_week');

            // If empty, seed standard rows
            if ($weeklyAvailabilities->isEmpty()) {
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days as $day) {
                    $isSun = ($day === 'Sunday');
                    $row = DoctorAvailability::create([
                        'doctor_id' => $doctor->id,
                        'clinic_id' => $selectedClinic->id,
                        'day_of_week' => $day,
                        'start_time' => '09:00',
                        'end_time' => '17:00',
                        'break_start' => '13:00',
                        'break_end' => '14:00',
                        'slot_duration' => $selectedClinic->appointment_duration ?? 15,
                        'max_patients' => 30,
                        'is_available' => !$isSun,
                    ]);
                    $weeklyAvailabilities->put($day, $row);
                }
            }
        }

        $currentDayAvailability = $weeklyAvailabilities->get($dayOfWeek);

        // 6. Slots Calculation for Selected Date
        $slots = [];
        $totalSlotsCount = 0;
        $availableSlotsCount = 0;
        $bookedSlotsCount = 0;
        $blockedSlotsCount = 0;

        if ($doctor && $selectedClinic) {
            // Get booked appointments for this doctor + clinic + date
            $appointments = Appointment::with('patient')
                ->where('doctor_id', $doctor->id)
                ->where('clinic_id', $selectedClinic->id)
                ->whereDate('appointment_date', $selectedDate)
                ->whereNotIn('status', ['cancelled'])
                ->get()
                ->keyBy(function ($apt) {
                    return date('H:i', strtotime($apt->appointment_time));
                });

            // Get slot overrides for this date
            $overrides = DoctorSlotOverride::where('doctor_id', $doctor->id)
                ->where('clinic_id', $selectedClinic->id)
                ->whereDate('slot_date', $selectedDate)
                ->get()
                ->keyBy(function ($ov) {
                    return date('H:i', strtotime($ov->slot_time));
                });

            $isDayAvailable = $currentDayAvailability ? (bool) $currentDayAvailability->is_available : true;
            $rawStart = $currentDayAvailability ? $currentDayAvailability->start_time : '09:00';
            $rawEnd = $currentDayAvailability ? $currentDayAvailability->end_time : '17:00';
            $duration = ($currentDayAvailability && $currentDayAvailability->slot_duration > 0)
                ? (int) $currentDayAvailability->slot_duration
                : ($selectedClinic->appointment_duration ?? 15);

            try {
                $startHour = Carbon::parse("{$selectedDate} " . trim($rawStart));
                $endHour = Carbon::parse("{$selectedDate} " . trim($rawEnd));
            } catch (\Exception $e) {
                $startHour = Carbon::parse("{$selectedDate} 09:00");
                $endHour = Carbon::parse("{$selectedDate} 17:00");
            }

            // Break parser
            $hasBreak = false;
            $bStart = null;
            $bEnd = null;
            if ($currentDayAvailability && !empty($currentDayAvailability->break_start) && !empty($currentDayAvailability->break_end)) {
                try {
                    $bStart = Carbon::parse("{$selectedDate} " . trim($currentDayAvailability->break_start));
                    $bEnd = Carbon::parse("{$selectedDate} " . trim($currentDayAvailability->break_end));
                    $hasBreak = $bStart->lt($bEnd);
                } catch (\Exception $e) {
                    $hasBreak = false;
                }
            }

            $currentSlotTime = $startHour->copy();

            while ($currentSlotTime->lt($endHour)) {
                $time24 = $currentSlotTime->format('H:i');
                $timeEnd = $currentSlotTime->copy()->addMinutes($duration)->format('H:i');
                $timeLabel = $currentSlotTime->format('h:i A') . ' - ' . $currentSlotTime->copy()->addMinutes($duration)->format('h:i A');

                $isBreak = false;
                if ($hasBreak && $currentSlotTime->gte($bStart) && $currentSlotTime->lt($bEnd)) {
                    $isBreak = true;
                }

                $apt = $appointments->get($time24);
                $ov = $overrides->get($time24);

                $status = 'available';
                $statusReason = null;

                if (!$isDayAvailable) {
                    $status = 'blocked';
                    $statusReason = 'Day marked as Non-Working / Off';
                    $blockedSlotsCount++;
                } elseif ($isBreak) {
                    $status = 'break';
                    $statusReason = 'Scheduled Lunch / Break';
                } elseif ($apt) {
                    $status = 'booked';
                    $bookedSlotsCount++;
                } elseif ($ov && $ov->is_blocked) {
                    $status = 'blocked';
                    $statusReason = $ov->reason ?: 'Blocked by doctor/admin';
                    $blockedSlotsCount++;
                } else {
                    $status = 'available';
                    $availableSlotsCount++;
                }

                $slots[] = [
                    'time' => $time24,
                    'time_end' => $timeEnd,
                    'label' => $timeLabel,
                    'status' => $status,
                    'status_reason' => $statusReason,
                    'appointment' => $apt,
                    'override' => $ov,
                ];

                $totalSlotsCount++;
                $currentSlotTime->addMinutes($duration);
            }

            // Also include custom extra slots created via overrides that fall outside normal hours
            foreach ($overrides as $timeKey => $ov) {
                $alreadyExists = collect($slots)->contains('time', $timeKey);
                if (!$alreadyExists && !$ov->is_blocked) {
                    $customTime = Carbon::parse("{$selectedDate} {$timeKey}");
                    $customTimeEnd = $customTime->copy()->addMinutes($duration);
                    $apt = $appointments->get($timeKey);

                    $slots[] = [
                        'time' => $timeKey,
                        'time_end' => $customTimeEnd->format('H:i'),
                        'label' => $customTime->format('h:i A') . ' - ' . $customTimeEnd->format('h:i A') . ' (Custom)',
                        'status' => $apt ? 'booked' : 'available',
                        'status_reason' => 'Custom added slot',
                        'appointment' => $apt,
                        'override' => $ov,
                    ];

                    $totalSlotsCount++;
                    if ($apt) {
                        $bookedSlotsCount++;
                    } else {
                        $availableSlotsCount++;
                    }
                }
            }
        }

        return view('slots.manage', compact(
            'clinics', 'selectedClinic', 'doctor', 'allDoctors',
            'selectedDate', 'carbonDate', 'dayOfWeek',
            'weeklyAvailabilities', 'currentDayAvailability',
            'slots', 'totalSlotsCount', 'availableSlotsCount',
            'bookedSlotsCount', 'blockedSlotsCount', 'currentRole'
        ));
    }

    /**
     * Update clinic working schedule (Day-wise timings, slot durations, breaks).
     */
    public function updateSchedule(Request $request)
    {
        $clinicId = $request->input('clinic_id');
        $doctorId = $request->input('doctor_id');

        $clinic = Clinic::findOrFail($clinicId);
        $doctor = Doctor::findOrFail($doctorId);

        $days = $request->input('days', []);

        foreach ($days as $day => $data) {
            $isAvailable = isset($data['is_available']) && $data['is_available'] == '1';

            DoctorAvailability::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'clinic_id' => $clinic->id,
                    'day_of_week' => $day,
                ],
                [
                    'start_time' => $data['start_time'] ?? '09:00',
                    'end_time' => $data['end_time'] ?? '17:00',
                    'break_start' => $data['break_start'] ?? null,
                    'break_end' => $data['break_end'] ?? null,
                    'slot_duration' => (int) ($data['slot_duration'] ?? $clinic->appointment_duration ?? 15),
                    'is_available' => $isAvailable,
                ]
            );
        }

        AuditLog::record('Clinic Schedule Updated', 'DoctorAvailability', "Clinic #{$clinic->id}", "Updated schedule for Dr. {$doctor->name} at {$clinic->name}");

        return back()->with('success', "Operational schedule for '{$clinic->name}' updated successfully!");
    }

    /**
     * Toggle blocking of an individual slot.
     */
    public function toggleBlock(Request $request)
    {
        $validated = $request->validate([
            'clinic_id' => 'required|exists:clinics,id',
            'doctor_id' => 'required|exists:doctors,id',
            'slot_date' => 'required|date',
            'slot_time' => 'required|string',
            'block' => 'required|boolean',
            'reason' => 'nullable|string|max:200',
        ]);

        // Prevent blocking if appointment already exists
        if ($validated['block']) {
            $hasAppointment = Appointment::where('doctor_id', $validated['doctor_id'])
                ->where('clinic_id', $validated['clinic_id'])
                ->whereDate('appointment_date', $validated['slot_date'])
                ->whereNotIn('status', ['cancelled'])
                ->get()
                ->first(function ($apt) use ($validated) {
                    return date('H:i', strtotime($apt->appointment_time)) === date('H:i', strtotime($validated['slot_time']));
                });

            if ($hasAppointment) {
                return back()->with('error', "Cannot block slot {$validated['slot_time']} because Patient {$hasAppointment->patient?->full_name} is already booked.");
            }
        }

        DoctorSlotOverride::updateOrCreate(
            [
                'doctor_id' => $validated['doctor_id'],
                'clinic_id' => $validated['clinic_id'],
                'slot_date' => $validated['slot_date'],
                'slot_time' => date('H:i', strtotime($validated['slot_time'])),
            ],
            [
                'is_blocked' => $validated['block'],
                'reason' => $validated['reason'] ?? ($validated['block'] ? 'Blocked by doctor' : null),
            ]
        );

        $action = $validated['block'] ? 'blocked' : 'unblocked';
        AuditLog::record('Slot Override', 'DoctorSlotOverride', "{$validated['slot_date']} {$validated['slot_time']}", "Slot {$validated['slot_time']} on {$validated['slot_date']} was {$action}.");

        return back()->with('success', "Time slot {$validated['slot_time']} on {$validated['slot_date']} has been {$action}.");
    }

    /**
     * Add an emergency / custom extra slot for a specific date.
     */
    public function addCustomSlot(Request $request)
    {
        $validated = $request->validate([
            'clinic_id' => 'required|exists:clinics,id',
            'doctor_id' => 'required|exists:doctors,id',
            'slot_date' => 'required|date',
            'slot_time' => 'required|string',
            'reason' => 'nullable|string|max:200',
        ]);

        $time24 = date('H:i', strtotime($validated['slot_time']));

        DoctorSlotOverride::updateOrCreate(
            [
                'doctor_id' => $validated['doctor_id'],
                'clinic_id' => $validated['clinic_id'],
                'slot_date' => $validated['slot_date'],
                'slot_time' => $time24,
            ],
            [
                'is_blocked' => false,
                'reason' => $validated['reason'] ?: 'Custom added extra slot',
            ]
        );

        return back()->with('success', "Custom slot at {$validated['slot_time']} added for {$validated['slot_date']}.");
    }
}
