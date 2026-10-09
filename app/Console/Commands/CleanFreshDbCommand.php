<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\TreatmentCategory;
use App\Models\Exercise;
use App\Models\DoctorVideo;

class CleanFreshDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-fresh-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean all dummy patient/appointment/invoice data and setup exactly 2 active clinics for Physiotherapy doctor testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database cleanup for fresh Physiotherapy Doctor testing...');

        // 1. Delete all patient transactional data
        $tablesToClean = [
            'prescription_items',
            'prescriptions',
            'visit_diagnoses',
            'visits',
            'invoice_items',
            'payment_transactions',
            'invoices',
            'appointments',
            'medical_reports',
            'patient_progress',
            'follow_ups',
            'patient_medical_histories',
            'patient_communications',
            'patients',
            'notifications',
        ];

        // Disable foreign keys temporarily for clean wipe
        if (DB::getDriverName() === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
            foreach ($tablesToClean as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line("  ✓ Truncated table {$table}");
                }
            }
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        } else {
            // SQLite
            DB::statement('PRAGMA foreign_keys = OFF;');
            foreach ($tablesToClean as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::table($table)->delete();
                    $this->line("  ✓ Cleared table {$table}");
                }
            }
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        // 2. Setup exactly 2 clean clinics matching SDPC letterhead
        DB::table('doctor_clinics')->delete();
        Clinic::query()->delete();

        $clinic1 = Clinic::create([
            'name' => 'SD Physiotherapy Clinic - Branch 1 (Khatiwala Tank)',
            'tagline' => 'Physiotherapy • Osteopathy • Chiropractic | Drug Free • Surgery Free • Pain Free Spine Specialist',
            'doctor_name' => 'Dr. Mahesh Sahu PT',
            'doctor_reg_no' => 'M.I.A.P. L-40612, MPPC-32862',
            'phone' => '0731-4976163 / 098933 62477',
            'email' => 'maheshsahu1983@gmail.com',
            'address' => '562, Khatiwala Tank',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452014',
            'website' => 'sdphysiotherapy.in',
            'consultation_fee' => 500.00,
            'appointment_duration' => 30,
            'working_days' => 'Monday - Saturday (Sunday Closed)',
            'working_hours' => '09:00 AM - 08:00 PM',
            'break_hours' => '01:30 PM - 03:30 PM',
            'is_active' => true,
        ]);
        $this->info("✓ Created Clinic 1: {$clinic1->name} (562, Khatiwala Tank, Indore)");

        $clinic2 = Clinic::create([
            'name' => 'SD Physiotherapy Clinic - Branch 2 (Mahalaxmi Nagar)',
            'tagline' => 'Physiotherapy • Osteopathy • Chiropractic | Drug Free • Surgery Free • Pain Free Spine Specialist',
            'doctor_name' => 'Dr. Mahesh Sahu PT',
            'doctor_reg_no' => 'M.I.A.P. L-40612, MPPC-32862',
            'phone' => '0731-4979170 / 098933 62477',
            'email' => 'maheshsahu1983@gmail.com',
            'address' => 'MR6-110, Mahalaxmi Nagar',
            'city' => 'Indore',
            'state' => 'Madhya Pradesh',
            'pincode' => '452010',
            'website' => 'sdphysiotherapy.in',
            'consultation_fee' => 500.00,
            'appointment_duration' => 30,
            'working_days' => 'Monday - Saturday (Sunday Closed)',
            'working_hours' => '09:00 AM - 08:00 PM',
            'break_hours' => '01:30 PM - 03:30 PM',
            'is_active' => true,
        ]);
        $this->info("✓ Created Clinic 2: {$clinic2->name} (MR6-110, Mahalaxmi Nagar, Indore)");

        // 3. Setup Doctors & Link to both Clinics
        $doctorUser1 = User::updateOrCreate(
            ['email' => 'doctor@carepoint.com'],
            [
                'name' => 'Dr. Mahesh Sahu PT',
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'phone' => '098933 62477',
                'is_active' => true,
            ]
        );

        $doctorUser2 = User::updateOrCreate(
            ['email' => 'tarun@physiopii.in'],
            [
                'name' => 'Dr. Tarun Birla',
                'password' => Hash::make('password'),
                'role' => 'doctor',
                'phone' => '+91 91117 58467',
                'is_active' => true,
            ]
        );

        $adminUser = User::updateOrCreate(
            ['email' => 'admin@carepoint.com'],
            [
                'name' => 'System SuperAdmin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $doc1 = Doctor::updateOrCreate(
            ['user_id' => $doctorUser1->id],
            [
                'name' => 'Dr. Mahesh Sahu PT',
                'specialization' => 'Spine Specialist, Neuro Rehab & Chiropractic',
                'qualification' => 'M.P.T Neuro, FOMT Australia, CHIROPRACTIC Sweden',
                'registration_no' => 'M.I.A.P. L-40612, MPPC-32862',
                'phone' => '098933 62477',
                'email' => 'doctor@carepoint.com',
                'consultation_fee' => 500.00,
                'bio' => 'Drug Free • Surgery Free • Pain Free Spine Specialist. Expert in Osteopathy, Chiropractic (Sweden) and Neurological Rehabilitation.',
                'is_active' => true,
            ]
        );

        $doc2 = Doctor::updateOrCreate(
            ['user_id' => $doctorUser2->id],
            [
                'name' => 'Dr. Tarun Birla',
                'specialization' => 'Consultant Physiotherapist & Spine Care',
                'qualification' => 'B.P.T, M.P.T (Ortho), MIAP',
                'registration_no' => 'MPPC-48192',
                'phone' => '+91 91117 58467',
                'email' => 'tarun@physiopii.in',
                'consultation_fee' => 500.00,
                'bio' => 'Consultant Physiotherapist with expertise in spine rehabilitation and sport injury conditioning.',
                'is_active' => true,
            ]
        );

        // Assign both clinics to both doctors
        DB::table('doctor_clinics')->insert([
            ['doctor_id' => $doc1->id, 'clinic_id' => $clinic1->id, 'created_at' => now(), 'updated_at' => now()],
            ['doctor_id' => $doc1->id, 'clinic_id' => $clinic2->id, 'created_at' => now(), 'updated_at' => now()],
            ['doctor_id' => $doc2->id, 'clinic_id' => $clinic1->id, 'created_at' => now(), 'updated_at' => now()],
            ['doctor_id' => $doc2->id, 'clinic_id' => $clinic2->id, 'created_at' => now(), 'updated_at' => now()],
        ]);
        $this->info("✓ Linked both doctors to Clinic 1 and Clinic 2.");

        // 4. Ensure Physiotherapy Categories exist
        $categoriesData = [
            ['name' => 'Cervical & Neck Rehabilitation', 'description' => 'Cervical spondylosis, neck spasms, radiculopathy, and postural strain'],
            ['name' => 'Lumbar & Low Back Care', 'description' => 'Sciatica, disc herniation, lumbar canal stenosis, and mechanical back pain'],
            ['name' => 'Knee & Joint Mobilization', 'description' => 'Osteoarthritis, ligament injuries (ACL/PCL), meniscus strain, and patellar tracking'],
            ['name' => 'Shoulder & Rotator Cuff Therapy', 'description' => 'Frozen shoulder, impingement syndrome, rotator cuff tendinitis, and bursitis'],
            ['name' => 'Post-Surgical Orthopedic Rehab', 'description' => 'TKR, THR, arthroscopy recovery, fracture stiffness, and joint mobilization'],
            ['name' => 'Neurological & Stroke Recovery', 'description' => 'Hemiplegia, Parkinsonism, facial palsy, cerebral palsy, and ataxia rehab'],
            ['name' => 'Sports Injury & Conditioning', 'description' => 'Ankle sprains, muscle tears, agility training, and sports biomechanics'],
            ['name' => 'Pediatric & Postural Correction', 'description' => 'Scoliosis, kyphosis, flat feet, and developmental delay therapy'],
        ];

        foreach ($categoriesData as $cData) {
            TreatmentCategory::firstOrCreate(
                ['name' => $cData['name']],
                ['description' => $cData['description'], 'is_active' => true]
            );
        }
        $this->info("✓ Ensured 8 Treatment Categories active.");

        $this->info('Database is now 100% clean, fresh, and ready for 2 test patients!');
        return 0;
    }
}
