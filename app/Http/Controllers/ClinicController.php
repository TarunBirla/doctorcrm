<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class ClinicController extends Controller
{
    /**
     * Display a listing of clinics.
     */
    public function index(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor) {
                // Doctor sees owned clinics and clinics attached via pivot
                $clinicIds = DB::table('doctor_clinics')
                    ->where('doctor_id', $loggedInDoctor->id)
                    ->pluck('clinic_id')
                    ->merge(Clinic::where('doctor_id', $loggedInDoctor->id)->pluck('id'))
                    ->unique();

                $clinics = Clinic::whereIn('id', $clinicIds)
                    ->with(['doctor', 'doctors'])
                    ->withCount(['appointments', 'availabilities'])
                    ->paginate(12);
            } else {
                $clinics = Clinic::whereRaw('1 = 0')->paginate(12);
            }
        } else {
            // Super Admin or staff sees all clinics
            $query = Clinic::with(['doctor', 'doctors'])->withCount(['appointments', 'availabilities']);
            
            if ($request->filled('doctor_id')) {
                $did = $request->input('doctor_id');
                $query->where(function ($q) use ($did) {
                    $q->where('doctor_id', $did)
                      ->orWhereHas('doctors', fn($dq) => $dq->where('doctors.id', $did));
                });
            }

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $clinics = $query->latest()->paginate(12)->withQueryString();
        }

        $allDoctors = Doctor::active()->orderBy('name')->get();

        return view('clinics.index', compact('clinics', 'loggedInDoctor', 'currentRole', 'allDoctors'));
    }

    /**
     * Show form to create a new clinic.
     */
    public function create()
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
        }

        $allDoctors = Doctor::active()->orderBy('name')->get();

        return view('clinics.create', compact('loggedInDoctor', 'currentRole', 'allDoctors'));
    }

    /**
     * Store a new clinic and set up default doctor availability.
     */
    public function store(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $assignedDoctorId = $loggedInDoctor ? $loggedInDoctor->id : null;
        } else {
            $assignedDoctorId = $request->input('doctor_id');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'tagline' => 'nullable|string|max:200',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'address' => 'required|string|max:300',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:50',
            'consultation_fee' => 'required|numeric|min:0',
            'appointment_duration' => 'required|integer|min:5|max:120',
            'working_days' => 'nullable|string|max:150',
            'working_hours' => 'nullable|string|max:100',
            'break_hours' => 'nullable|string|max:100',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        $doctorObj = $assignedDoctorId ? Doctor::find($assignedDoctorId) : null;

        $clinic = DB::transaction(function () use ($validated, $assignedDoctorId, $doctorObj) {
            $clinic = Clinic::create([
                'doctor_id' => $assignedDoctorId,
                'name' => $validated['name'],
                'tagline' => $validated['tagline'] ?? null,
                'doctor_name' => $doctorObj ? $doctorObj->name : 'Medical Director',
                'doctor_reg_no' => $doctorObj ? $doctorObj->registration_no : 'REG-CLINIC',
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'pincode' => $validated['pincode'] ?? null,
                'website' => $validated['website'] ?? null,
                'gst_number' => $validated['gst_number'] ?? null,
                'consultation_fee' => $validated['consultation_fee'],
                'appointment_duration' => $validated['appointment_duration'],
                'working_days' => $validated['working_days'] ?? 'Mon - Sat',
                'working_hours' => $validated['working_hours'] ?? '09:00 AM - 05:00 PM',
                'break_hours' => $validated['break_hours'] ?? '01:00 PM - 02:00 PM',
                'is_active' => true,
            ]);

            // If a doctor is linked, attach to pivot doctor_clinics
            if ($assignedDoctorId) {
                DB::table('doctor_clinics')->updateOrInsert(
                    ['doctor_id' => $assignedDoctorId, 'clinic_id' => $clinic->id],
                    ['is_primary' => true, 'updated_at' => now(), 'created_at' => now()]
                );

                // Initialize 7-day default schedule for this doctor at this clinic
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                foreach ($days as $day) {
                    $isSunday = ($day === 'Sunday');
                    DoctorAvailability::updateOrCreate(
                        [
                            'doctor_id' => $assignedDoctorId,
                            'clinic_id' => $clinic->id,
                            'day_of_week' => $day,
                        ],
                        [
                            'start_time' => '09:00',
                            'end_time' => '17:00',
                            'break_start' => '13:00',
                            'break_end' => '14:00',
                            'slot_duration' => $validated['appointment_duration'] ?? 15,
                            'max_patients' => 30,
                            'is_available' => !$isSunday,
                        ]
                    );
                }
            }

            return $clinic;
        });

        AuditLog::record('Clinic Created', 'Clinic', (string) $clinic->id, "Clinic {$clinic->name} registered successfully.");

        return redirect()->route('clinics.index')->with('success', "Clinic '{$clinic->name}' registered successfully! Operational schedule generated.");
    }

    /**
     * Show form to edit clinic.
     */
    public function edit($id)
    {
        $clinic = Clinic::with('doctor')->findOrFail($id);
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $isLinked = DB::table('doctor_clinics')
                ->where('doctor_id', $loggedInDoctor?->id)
                ->where('clinic_id', $clinic->id)
                ->exists();

            if ($clinic->doctor_id !== $loggedInDoctor?->id && !$isLinked) {
                abort(403, 'Unauthorized access to this clinic.');
            }
        } else {
            $loggedInDoctor = null;
        }

        $allDoctors = Doctor::active()->orderBy('name')->get();

        return view('clinics.edit', compact('clinic', 'loggedInDoctor', 'currentRole', 'allDoctors'));
    }

    /**
     * Update clinic details.
     */
    public function update(Request $request, $id)
    {
        $clinic = Clinic::findOrFail($id);
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $isLinked = DB::table('doctor_clinics')
                ->where('doctor_id', $loggedInDoctor?->id)
                ->where('clinic_id', $clinic->id)
                ->exists();

            if ($clinic->doctor_id !== $loggedInDoctor?->id && !$isLinked) {
                abort(403, 'Unauthorized access to this clinic.');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'tagline' => 'nullable|string|max:200',
            'phone' => 'required|string|max:50',
            'email' => 'required|email|max:100',
            'address' => 'required|string|max:300',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'website' => 'nullable|string|max:100',
            'gst_number' => 'nullable|string|max:50',
            'consultation_fee' => 'required|numeric|min:0',
            'appointment_duration' => 'required|integer|min:5|max:120',
            'working_days' => 'nullable|string|max:150',
            'working_hours' => 'nullable|string|max:100',
            'break_hours' => 'nullable|string|max:100',
            'doctor_id' => 'nullable|exists:doctors,id',
        ]);

        if ($currentRole !== 'doctor' && $request->filled('doctor_id')) {
            $clinic->doctor_id = $request->input('doctor_id');
            // update pivot
            DB::table('doctor_clinics')->updateOrInsert(
                ['doctor_id' => $request->input('doctor_id'), 'clinic_id' => $clinic->id],
                ['is_primary' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $clinic->fill($validated);
        $clinic->save();

        AuditLog::record('Clinic Updated', 'Clinic', (string) $clinic->id, "Clinic {$clinic->name} details updated.");

        return redirect()->route('clinics.index')->with('success', "Clinic '{$clinic->name}' updated successfully.");
    }

    /**
     * Toggle clinic active status.
     */
    public function toggleStatus($id)
    {
        $clinic = Clinic::findOrFail($id);
        $clinic->is_active = !$clinic->is_active;
        $clinic->save();

        $statusStr = $clinic->is_active ? 'Activated' : 'Deactivated';
        AuditLog::record('Clinic Status Changed', 'Clinic', (string) $clinic->id, "Clinic {$clinic->name} was {$statusStr}.");

        return back()->with('success', "Clinic '{$clinic->name}' has been {$statusStr}.");
    }

    /**
     * Remove clinic.
     */
    public function destroy($id)
    {
        $clinic = Clinic::withCount('appointments')->findOrFail($id);

        if ($clinic->appointments_count > 0) {
            return back()->with('error', "Cannot delete clinic '{$clinic->name}' because it has {$clinic->appointments_count} booked appointments associated with it. You can deactivate it instead.");
        }

        $name = $clinic->name;
        $clinic->delete();

        AuditLog::record('Clinic Deleted', 'Clinic', (string) $id, "Clinic {$name} deleted.");

        return redirect()->route('clinics.index')->with('success', "Clinic '{$name}' deleted successfully.");
    }
}
