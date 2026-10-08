<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AuditLog;
use App\Models\Doctor;

class ProfileController extends Controller
{
    /**
     * Show User Profile.
     */
    public function show()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $doctor = Doctor::where('user_id', $user->id)->first();

        return view('profile.show', compact('user', 'doctor'));
    }

    /**
     * Update Profile Information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            // Doctor fields (if applicable)
            'specialization' => 'nullable|string|max:150',
            'qualification' => 'nullable|string|max:150',
            'registration_no' => 'nullable|string|max:100',
            'consultation_fee' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];
        $user->save();

        // If user is a doctor, sync doctor record
        $doctor = Doctor::where('user_id', $user->id)->first();
        if ($doctor) {
            $doctor->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $doctor->phone,
                'specialization' => $validated['specialization'] ?? $doctor->specialization,
                'qualification' => $validated['qualification'] ?? $doctor->qualification,
                'registration_no' => $validated['registration_no'] ?? $doctor->registration_no,
                'consultation_fee' => $validated['consultation_fee'] ?? $doctor->consultation_fee,
                'bio' => $validated['bio'] ?? $doctor->bio,
            ]);
        }

        AuditLog::record('Profile Updated', 'User', (string) $user->id, "User {$user->name} updated their profile information.");

        return back()->with('success', 'Profile details updated successfully.');
    }

    /**
     * Change Password.
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Your current password does not match our records.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        AuditLog::record('Password Changed', 'User', (string) $user->id, "User {$user->name} changed their account password.");

        return back()->with('success', 'Password changed successfully! Please remember your new password.');
    }
}
