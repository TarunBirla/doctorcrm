<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\AuditLog;

class SettingController extends Controller
{
    public function index()
    {
        $clinic = Clinic::first() ?? Clinic::create([
            'name' => 'CarePoint Super Clinic',
            'phone' => '+91 98101 23456',
            'email' => 'contact@carepointclinic.com',
            'address' => 'SCO 14-15, Sector 14 Urban Estate',
            'city' => 'Gurugram',
            'state' => 'Haryana',
            'pincode' => '122001',
        ]);

        $doctor = Doctor::with('availabilities')->first();

        return view('settings.index', compact('clinic', 'doctor'));
    }

    public function updateClinic(Request $request)
    {
        $clinic = Clinic::first();
        if (!$clinic) {
            $clinic = new Clinic();
        }

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'tagline' => 'nullable|string|max:200',
            'doctor_name' => 'required|string|max:200',
            'doctor_reg_no' => 'required|string|max:100',
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
            'working_days' => 'required|string|max:100',
            'working_hours' => 'required|string|max:100',
            'break_hours' => 'nullable|string|max:100',
            'prescription_header' => 'nullable|string',
            'invoice_footer' => 'nullable|string',
        ]);

        $clinic->fill($validated);
        $clinic->save();

        AuditLog::record('Clinic Settings Updated', 'Clinic', (string) $clinic->id, "Updated clinic configurations and operational parameters");

        return back()->with('success', 'Clinic settings updated successfully.');
    }

    public function availability(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $allDoctors = Doctor::active()->orderBy('name')->get();

        if ($currentRole === 'doctor' && auth()->check()) {
            $doctor = Doctor::where('user_id', auth()->id())->first();
            if (!$doctor) {
                return redirect()->route('dashboard')->with('error', 'Doctor profile not found for your account.');
            }
        } else {
            // Super Admin or staff: allow picking doctor via ?doctor_id=
            $requestedDoctorId = $request->get('doctor_id');
            if ($requestedDoctorId) {
                $doctor = Doctor::find($requestedDoctorId);
            }
            if (!isset($doctor) || !$doctor) {
                $doctor = $allDoctors->first() ?? Doctor::first();
            }
        }

        $availabilities = $doctor ? $doctor->availabilities : collect();

        return view('settings.availability', compact('doctor', 'availabilities', 'allDoctors', 'currentRole'));
    }

    public function updateAvailability(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $doctorId = $request->input('doctor_id');

        if ($currentRole === 'doctor' && auth()->check()) {
            $doctor = Doctor::where('user_id', auth()->id())->first();
        } else {
            $doctor = $doctorId ? Doctor::find($doctorId) : Doctor::first();
        }

        if (!$doctor) {
            return back()->with('error', 'Doctor record not found.');
        }

        $daysData = $request->input('days', []);

        foreach ($daysData as $dayName => $data) {
            DoctorAvailability::updateOrCreate(
                [
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $dayName,
                ],
                [
                    'start_time' => $data['start_time'] ?? '09:00',
                    'end_time' => $data['end_time'] ?? '20:00',
                    'break_start' => $data['break_start'] ?? null,
                    'break_end' => $data['break_end'] ?? null,
                    'slot_duration' => $data['slot_duration'] ?? 15,
                    'max_patients' => $data['max_patients'] ?? 30,
                    'is_available' => isset($data['is_available']),
                ]
            );
        }

        AuditLog::record('Doctor Availability Updated', 'Doctor', (string) $doctor->id, "Weekly clinical schedule and slot constraints updated for Dr. {$doctor->name}");

        return back()->with('success', "Availability and consulting hours saved successfully for Dr. {$doctor->name}.");
    }
}
