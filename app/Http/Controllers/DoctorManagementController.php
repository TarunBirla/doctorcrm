<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Doctor;
use App\Models\User;
use App\Models\DoctorAvailability;
use App\Models\AuditLog;

class DoctorManagementController extends Controller
{
    /**
     * Display a listing of doctors.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Doctor::with('user')->withCount(['appointments', 'visits']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('registration_no', 'like', "%{$search}%");
            });
        }

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'suspended') {
            $query->where('is_active', false);
        }

        $doctors = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('admin.doctors.index', compact('doctors', 'search', 'status'));
    }

    /**
     * Store a newly created doctor & user account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email|unique:doctors,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:150',
            'qualification' => 'nullable|string|max:150',
            'registration_no' => 'nullable|string|max:100',
            'consultation_fee' => 'required|numeric|min:0',
            'bio' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create User account for doctor login
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'doctor',
                'phone' => $validated['phone'],
                'is_active' => true,
            ]);

            // 2. Create Doctor profile linked to user
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'specialization' => $validated['specialization'],
                'qualification' => $validated['qualification'] ?? 'MBBS, MD',
                'registration_no' => $validated['registration_no'] ?? 'REG-' . rand(10000, 99999),
                'consultation_fee' => $validated['consultation_fee'],
                'bio' => $validated['bio'] ?? null,
                'is_active' => true,
            ]);

            // 3. Initialize standard working schedule (Monday to Saturday)
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            foreach ($days as $day) {
                DoctorAvailability::create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                    'slot_duration_minutes' => 15,
                    'is_available' => true,
                ]);
            }

            AuditLog::record('Doctor Created', 'Doctor', (string) $doctor->id, "Super Admin created Doctor {$doctor->name} ({$doctor->email}) with login access.");
        });

        return back()->with('success', "Doctor {$validated['name']} registered successfully with login credentials.");
    }

    /**
     * Update the specified doctor.
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id . '|unique:users,email,' . ($doctor->user_id ?? 0),
            'password' => 'nullable|string|min:6',
            'phone' => 'required|string|max:20',
            'specialization' => 'required|string|max:150',
            'qualification' => 'nullable|string|max:150',
            'registration_no' => 'nullable|string|max:100',
            'consultation_fee' => 'required|numeric|min:0',
            'bio' => 'nullable|string',
        ]);

        $doctor->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'specialization' => $validated['specialization'],
            'qualification' => $validated['qualification'],
            'registration_no' => $validated['registration_no'],
            'consultation_fee' => $validated['consultation_fee'],
            'bio' => $validated['bio'],
        ]);

        // Sync linked user
        if ($doctor->user) {
            $userUpdate = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ];
            if (!empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }
            $doctor->user->update($userUpdate);
        }

        AuditLog::record('Doctor Updated', 'Doctor', (string) $doctor->id, "Super Admin updated details for Doctor {$doctor->name}.");

        return back()->with('success', "Doctor {$doctor->name} details updated successfully.");
    }

    /**
     * Toggle suspend / activate status.
     */
    public function toggleStatus($id)
    {
        $doctor = Doctor::findOrFail($id);
        $newStatus = !$doctor->is_active;

        $doctor->is_active = $newStatus;
        $doctor->save();

        if ($doctor->user) {
            $doctor->user->is_active = $newStatus;
            $doctor->user->save();
        }

        $label = $newStatus ? 'activated' : 'suspended';
        AuditLog::record('Doctor Status Changed', 'Doctor', (string) $doctor->id, "Doctor {$doctor->name} was {$label}.");

        return back()->with('success', "Doctor {$doctor->name} has been {$label} successfully.");
    }

    /**
     * Remove the specified doctor.
     */
    public function destroy($id)
    {
        $doctor = Doctor::findOrFail($id);
        $name = $doctor->name;

        if ($doctor->user) {
            $doctor->user->delete();
        }
        $doctor->delete();

        AuditLog::record('Doctor Deleted', 'Doctor', (string) $id, "Super Admin deleted Doctor {$name}.");

        return back()->with('success', "Doctor {$name} archived successfully.");
    }
}
