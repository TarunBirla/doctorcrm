<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Clinic;
use App\Models\Doctor;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $now = now();

        // 1. Ensure SD Physiotherapy Clinic Branch 1 exists
        $branch1 = Clinic::where('name', 'like', '%SD Physiotherapy Clinic - Branch 1%')
            ->orWhere('address', 'like', '%Khatiwala Tank%')
            ->first();

        if (!$branch1) {
            $branch1 = Clinic::create([
                'name' => 'SD Physiotherapy Clinic - Branch 1',
                'tagline' => 'Physiotherapy • Osteopathy • Chiropractic | Spine Specialist (Drug Free • Surgery Free • Pain Free)',
                'doctor_name' => 'Dr. Mahesh Sahu PT',
                'doctor_reg_no' => 'M.I.A.P. L-40612, MPPC-32862',
                'phone' => '+91 98933 62477, 0731-4976163',
                'email' => 'maheshsahu1983@gmail.com',
                'address' => '584-C, Khatiwala Tank',
                'city' => 'Indore',
                'state' => 'Madhya Pradesh',
                'pincode' => '452014',
                'website' => 'www.sdpcindore.com',
                'gst_number' => '23AAACS9182P1Z1',
                'consultation_fee' => 500.00,
                'appointment_duration' => 30,
                'working_days' => 'Monday - Saturday',
                'working_hours' => '09:00 AM - 08:30 PM',
                'break_hours' => '01:30 PM - 04:00 PM',
                'prescription_header' => "SD PHYSIOTHERAPY CLINIC\nPhysiotherapy • Osteopathy • Chiropractic\n584-C, Khatiwala Tank, Indore | Ph: 0731-4976163",
                'invoice_footer' => "Thank you for visiting SD Physiotherapy Clinic. Stay active and pain-free!",
                'is_active' => true,
            ]);
        }

        // 2. Ensure SD Physiotherapy Clinic Branch 2 exists
        $branch2 = Clinic::where('name', 'like', '%SD Physiotherapy Clinic - Branch 2%')
            ->orWhere('address', 'like', '%Mahalaxmi Nagar%')
            ->first();

        if (!$branch2) {
            $branch2 = Clinic::create([
                'name' => 'SD Physiotherapy Clinic - Branch 2',
                'tagline' => 'Physiotherapy • Osteopathy • Chiropractic | Spine Specialist (Drug Free • Surgery Free • Pain Free)',
                'doctor_name' => 'Dr. Mahesh Sahu PT',
                'doctor_reg_no' => 'M.I.A.P. L-40612, MPPC-32862',
                'phone' => '0731-4979170, +91 98933 62477',
                'email' => 'maheshsahu1983@gmail.com',
                'address' => 'MR6-110, Mahalaxmi Nagar',
                'city' => 'Indore',
                'state' => 'Madhya Pradesh',
                'pincode' => '452010',
                'website' => 'www.sdphysiotherapy.in',
                'gst_number' => '23AAACS9182P1Z1',
                'consultation_fee' => 500.00,
                'appointment_duration' => 30,
                'working_days' => 'Monday - Saturday',
                'working_hours' => '10:00 AM - 08:00 PM',
                'break_hours' => '02:00 PM - 04:30 PM',
                'prescription_header' => "SD PHYSIOTHERAPY CLINIC\nPhysiotherapy • Osteopathy • Chiropractic\nMR6-110, Mahalaxmi Nagar, Indore | Ph: 0731-4979170",
                'invoice_footer' => "Thank you for visiting SD Physiotherapy Clinic. Stay active and pain-free!",
                'is_active' => true,
            ]);
        }

        // Link with any existing doctors
        $doctors = Doctor::all();
        foreach ($doctors as $doc) {
            DB::table('doctor_clinics')->updateOrInsert(
                ['doctor_id' => $doc->id, 'clinic_id' => $branch1->id],
                ['is_primary' => true, 'created_at' => $now, 'updated_at' => $now]
            );
            DB::table('doctor_clinics')->updateOrInsert(
                ['doctor_id' => $doc->id, 'clinic_id' => $branch2->id],
                ['is_primary' => false, 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe to leave
    }
};
