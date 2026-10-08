<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\Diagnosis;
use App\Models\Patient;
use App\Models\PatientMedicalHistory;
use App\Models\Appointment;
use App\Models\Visit;
use App\Models\VisitDiagnosis;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\MedicalReport;
use App\Models\PatientProgress;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTransaction;
use App\Models\Expense;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\PatientCommunication;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Clinic Information
        $clinic = Clinic::create([
            'name' => 'CarePoint Super Clinic',
            'tagline' => 'Center for Advanced Internal Medicine & Family Care',
            'doctor_name' => 'Dr. Rajiv Sharma, MD (General Medicine)',
            'doctor_reg_no' => 'MCI-84729-D',
            'phone' => '+91 98101 23456',
            'email' => 'contact@carepointclinic.com',
            'address' => 'SCO 14-15, Sector 14 Urban Estate',
            'city' => 'Gurugram',
            'state' => 'Haryana',
            'pincode' => '122001',
            'website' => 'www.carepointclinic.com',
            'gst_number' => '06AAACH2849P1Z3',
            'consultation_fee' => 800.00,
            'appointment_duration' => 15,
            'working_days' => 'Monday - Saturday',
            'working_hours' => '09:00 AM - 08:00 PM',
            'break_hours' => '01:30 PM - 04:00 PM',
            'prescription_header' => "CAREPOINT CLINIC - COMPREHENSIVE HEALTHCARE\nReg No: MCI-84729-D | Ph: +91 98101 23456\nSector 14, Gurugram, Haryana",
            'invoice_footer' => "Thank you for choosing CarePoint Clinic. Please keep this invoice for your medical insurance and follow-up records.",
        ]);

        // 2. Users
        $adminUser = User::create([
            'name' => 'System SuperAdmin',
            'email' => 'admin@carepoint.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'phone' => '+91 98101 23456',
            'is_active' => true,
        ]);

        $doctorUser = User::create([
            'name' => 'Dr. Rajiv Sharma',
            'email' => 'doctor@carepoint.com',
            'password' => Hash::make('password'),
            'role' => 'doctor',
            'phone' => '+91 98101 99999',
            'is_active' => true,
        ]);

        $staffUser = User::create([
            'name' => 'Pooja Verma (Front Desk)',
            'email' => 'staff@carepoint.com',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
            'phone' => '+91 98101 88888',
            'is_active' => true,
        ]);

        // 3. Doctor Details
        $doctor = Doctor::create([
            'user_id' => $doctorUser->id,
            'name' => 'Dr. Rajiv Sharma',
            'specialization' => 'Senior Consultant Physician & Diabetologist',
            'qualification' => 'MBBS, MD (Internal Medicine), FICP',
            'registration_no' => 'MCI-84729-D',
            'phone' => '+91 98101 99999',
            'email' => 'doctor@carepoint.com',
            'consultation_fee' => 800.00,
            'bio' => 'Dr. Rajiv Sharma has over 18 years of clinical experience in managing chronic conditions like Diabetes, Hypertension, Cardiac risk prevention, and lifestyle disorders.',
        ]);

        // Availabilities
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($days as $day) {
            DoctorAvailability::create([
                'doctor_id' => $doctor->id,
                'day_of_week' => $day,
                'start_time' => '09:30',
                'end_time' => '19:30',
                'break_start' => '13:30',
                'break_end' => '16:00',
                'slot_duration' => 15,
                'max_patients' => 35,
                'is_available' => true,
            ]);
        }
        DoctorAvailability::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 'Sunday',
            'start_time' => '10:00',
            'end_time' => '13:00',
            'break_start' => null,
            'break_end' => null,
            'slot_duration' => 15,
            'max_patients' => 12,
            'is_available' => false, // Sunday closed
        ]);

        // 4. Diagnoses Master List
        $diagnosesMaster = [
            ['code' => 'E11.9', 'name' => 'Type 2 Diabetes Mellitus', 'description' => 'Without acute complications'],
            ['code' => 'I10', 'name' => 'Essential Primary Hypertension', 'description' => 'High blood pressure stage 1-2'],
            ['code' => 'J20.9', 'name' => 'Acute Bronchitis', 'description' => 'Lower respiratory tract infection'],
            ['code' => 'J02.9', 'name' => 'Acute Pharyngitis', 'description' => 'Sore throat and viral pharyngeal inflammation'],
            ['code' => 'K21.9', 'name' => 'Gastro-Esophageal Reflux Disease (GERD)', 'description' => 'Acid reflux and dyspepsia'],
            ['code' => 'G43.9', 'name' => 'Migraine with Aura', 'description' => 'Recurrent unilateral vascular headache'],
            ['code' => 'M54.5', 'name' => 'Chronic Lumbar Spondylosis', 'description' => 'Lower back muscular pain'],
            ['code' => 'L20.9', 'name' => 'Allergic Contact Dermatitis', 'description' => 'Pruritic skin reaction'],
            ['code' => 'E03.9', 'name' => 'Primary Hypothyroidism', 'description' => 'Elevated TSH with low free T4'],
            ['code' => 'A90', 'name' => 'Dengue Fever', 'description' => 'Acute viral illness with thrombocytopenia'],
        ];
        foreach ($diagnosesMaster as $d) {
            Diagnosis::create($d);
        }

        // 5. Patients
        $patientsData = [
            [
                'patient_id' => 'PAT-000001',
                'first_name' => 'Amitabh',
                'last_name' => 'Srivastava',
                'gender' => 'Male',
                'dob' => '1974-05-14',
                'age' => 52,
                'mobile' => '9823456781',
                'alt_mobile' => '9823456799',
                'email' => 'amitabh.s@example.com',
                'address' => 'Flat 402, Royal Palms, DLF Phase 2',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'B+',
                'occupation' => 'Architect',
                'marital_status' => 'Married',
                'emergency_contact' => 'Sunita Srivastava (Wife)',
                'emergency_phone' => '9823456782',
                'referral_source' => 'Google Search',
                'notes' => 'Diabetic patient for 6 years. Compliant with lifestyle modifications.',
                'conditions' => 'Type 2 Diabetes, Mild Hypertension',
                'allergies' => 'Penicillin, Sulfa drugs',
                'surgeries' => 'Appendectomy (2012)',
                'family_history' => 'Father had CAD, Mother has Diabetes',
                'medications' => 'Tab Metformin 500mg BD, Tab Telmisartan 40mg OD',
            ],
            [
                'patient_id' => 'PAT-000002',
                'first_name' => 'Priya',
                'last_name' => 'Nambiar',
                'gender' => 'Female',
                'dob' => '1991-08-22',
                'age' => 35,
                'mobile' => '9876512340',
                'alt_mobile' => null,
                'email' => 'priya.n@example.com',
                'address' => 'House 112, Sector 23',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'O+',
                'occupation' => 'Software Engineer',
                'marital_status' => 'Single',
                'emergency_contact' => 'Ramesh Nambiar (Brother)',
                'emergency_phone' => '9876512349',
                'referral_source' => 'Word of mouth',
                'notes' => 'Recurrent stress-induced migraine attacks during high-workload quarters.',
                'conditions' => 'Migraine with Aura, Vitamin D Deficiency',
                'allergies' => 'Dust mites, NSAIDs (causes gastritis)',
                'surgeries' => 'None',
                'family_history' => 'Maternal migraine',
                'medications' => 'Tab Naproxen 250mg SOS, Vitamin D3 60k weekly',
            ],
            [
                'patient_id' => 'PAT-000003',
                'first_name' => 'Rajesh',
                'last_name' => 'Kapoor',
                'gender' => 'Male',
                'dob' => '1962-11-03',
                'age' => 64,
                'mobile' => '9811223344',
                'alt_mobile' => '9811223300',
                'email' => 'rajesh.kapoor@example.com',
                'address' => 'Villa 18, Nirvana Country',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'A+',
                'occupation' => 'Retired Civil Engineer',
                'marital_status' => 'Married',
                'emergency_contact' => 'Vandana Kapoor',
                'emergency_phone' => '9811223345',
                'referral_source' => 'Doctor Referral',
                'notes' => 'History of high cholesterol and knee joint pain.',
                'conditions' => 'Hyperlipidemia, Bilateral Knee Osteoarthritis',
                'allergies' => 'None reported',
                'surgeries' => 'Right Arthroscopy (2018)',
                'family_history' => 'Hypertension in father',
                'medications' => 'Tab Atorvastatin 20mg OD, Tab Calcium + D3 OD',
            ],
            [
                'patient_id' => 'PAT-000004',
                'first_name' => 'Ananya',
                'last_name' => 'Deshmukh',
                'gender' => 'Female',
                'dob' => '1998-03-19',
                'age' => 28,
                'mobile' => '9955443322',
                'alt_mobile' => null,
                'email' => 'ananya.d@example.com',
                'address' => 'Tower 3, Central Park Resorts',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'AB+',
                'occupation' => 'Financial Analyst',
                'marital_status' => 'Single',
                'emergency_contact' => 'Sunil Deshmukh (Father)',
                'emergency_phone' => '9955443300',
                'referral_source' => 'Practo / Online',
                'notes' => 'Complains of recurrent upper abdominal burning and nocturnal reflux.',
                'conditions' => 'GERD, Chronic Acid Peptic Disease',
                'allergies' => 'Dairy products (lactose intolerance)',
                'surgeries' => 'None',
                'family_history' => 'Mother has thyroid disorder',
                'medications' => 'Cap Pantoprazole 40mg OD before breakfast',
            ],
            [
                'patient_id' => 'PAT-000005',
                'first_name' => 'Vikram',
                'last_name' => 'Malhotra',
                'gender' => 'Male',
                'dob' => '1985-07-12',
                'age' => 41,
                'mobile' => '9711882233',
                'alt_mobile' => null,
                'email' => 'vikram.m@example.com',
                'address' => 'Plot 88, Sushant Lok Phase 1',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'O-',
                'occupation' => 'Marketing VP',
                'marital_status' => 'Married',
                'emergency_contact' => 'Ritu Malhotra',
                'emergency_phone' => '9711882244',
                'referral_source' => 'Direct Walk-in',
                'notes' => 'Executive health checkup follow-up, elevated uric acid.',
                'conditions' => 'Asymptomatic Hyperuricemia, Fatty Liver Grade 1',
                'allergies' => 'None',
                'surgeries' => 'None',
                'family_history' => 'Gout in elder brother',
                'medications' => 'Tab Febuxostat 40mg OD',
            ],
            [
                'patient_id' => 'PAT-000006',
                'first_name' => 'Meenakshi',
                'last_name' => 'Sundaram',
                'gender' => 'Female',
                'dob' => '1979-01-30',
                'age' => 47,
                'mobile' => '9840112233',
                'alt_mobile' => null,
                'email' => 'meenakshi.s@example.com',
                'address' => 'Block C, South City 2',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'B-',
                'occupation' => 'College Professor',
                'marital_status' => 'Married',
                'emergency_contact' => 'Karthik Sundaram',
                'emergency_phone' => '9840112244',
                'referral_source' => 'Colleague',
                'notes' => 'Hypothyroidism being treated for 4 years. Weight gain issues.',
                'conditions' => 'Hypothyroidism, Cervical Spondylosis',
                'allergies' => 'Ciprofloxacin (skin rash)',
                'surgeries' => 'Cholecystectomy (2015)',
                'family_history' => 'Mother has Hypothyroidism',
                'medications' => 'Tab Thyronorm 75mcg empty stomach',
            ],
            [
                'patient_id' => 'PAT-000007',
                'first_name' => 'Gurpreet',
                'last_name' => 'Singh',
                'gender' => 'Male',
                'dob' => '1995-12-10',
                'age' => 31,
                'mobile' => '9988776655',
                'alt_mobile' => null,
                'email' => 'gurpreet.singh@example.com',
                'address' => 'Sector 46, Huda Colony',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'A-',
                'occupation' => 'Fitness Trainer',
                'marital_status' => 'Single',
                'emergency_contact' => 'Harjit Singh (Father)',
                'emergency_phone' => '9988776600',
                'referral_source' => 'Gym Client',
                'notes' => 'Acute lower back spasm after deadlifts. Sports injury.',
                'conditions' => 'Acute Lumbar Muscular Strain',
                'allergies' => 'None',
                'surgeries' => 'None',
                'family_history' => 'None',
                'medications' => 'Tab Thiocolchicoside + Aceclofenac BD',
            ],
            [
                'patient_id' => 'PAT-000008',
                'first_name' => 'Sunita',
                'last_name' => 'Choudhary',
                'gender' => 'Female',
                'dob' => '1968-09-05',
                'age' => 58,
                'mobile' => '9818822334',
                'alt_mobile' => null,
                'email' => 'sunita.choudhary@example.com',
                'address' => 'Sector 15 Part 2',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'AB-',
                'occupation' => 'Homemaker',
                'marital_status' => 'Married',
                'emergency_contact' => 'Devendra Choudhary',
                'emergency_phone' => '9818822335',
                'referral_source' => 'Newspaper Ad',
                'notes' => 'Longstanding hypertension with occasional ankle edema.',
                'conditions' => 'Stage 2 Hypertension, Peripheral Edema',
                'allergies' => 'Aspirin (causes wheezing)',
                'surgeries' => 'Hysterectomy (2014)',
                'family_history' => 'Stroke in father',
                'medications' => 'Tab Cilnidipine 10mg OD, Tab Telmisartan 40mg OD',
            ],
            [
                'patient_id' => 'PAT-000009',
                'first_name' => 'Arjun',
                'last_name' => 'Mehta',
                'gender' => 'Male',
                'dob' => '2001-04-18',
                'age' => 25,
                'mobile' => '9810554433',
                'alt_mobile' => null,
                'email' => 'arjun.mehta@example.com',
                'address' => 'Cyber City Residency, Phase 3',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'O+',
                'occupation' => 'UI/UX Designer',
                'marital_status' => 'Single',
                'emergency_contact' => 'Rekha Mehta (Mother)',
                'emergency_phone' => '9810554400',
                'referral_source' => 'Instagram',
                'notes' => 'Seasonal allergic rhinitis with conjunctival redness.',
                'conditions' => 'Allergic Rhinitis, Sinusitis',
                'allergies' => 'Pollen, Dust',
                'surgeries' => 'None',
                'family_history' => 'Atopic asthma in mother',
                'medications' => 'Tab Bilastine 20mg OD, Fluticasone Nasal Spray',
            ],
            [
                'patient_id' => 'PAT-000010',
                'first_name' => 'Kavita',
                'last_name' => 'Bansal',
                'gender' => 'Female',
                'dob' => '1988-06-25',
                'age' => 38,
                'mobile' => '9871122445',
                'alt_mobile' => null,
                'email' => 'kavita.bansal@example.com',
                'address' => 'Sector 57, Sushant Estate',
                'city' => 'Gurugram',
                'state' => 'Haryana',
                'blood_group' => 'B+',
                'occupation' => 'Teacher',
                'marital_status' => 'Married',
                'emergency_contact' => 'Sanjay Bansal',
                'emergency_phone' => '9871122446',
                'referral_source' => 'School Principal',
                'notes' => 'Chronic iron deficiency anemia with fatigue and hair fall.',
                'conditions' => 'Iron Deficiency Anemia (Hb 9.2 g/dL)',
                'allergies' => 'None',
                'surgeries' => 'LSCS (2017)',
                'family_history' => 'None',
                'medications' => 'Tab Ferrous Ascorbate 100mg + Folic Acid OD',
            ],
        ];

        $patients = [];
        foreach ($patientsData as $pData) {
            $patient = Patient::create([
                'patient_id' => $pData['patient_id'],
                'first_name' => $pData['first_name'],
                'last_name' => $pData['last_name'],
                'gender' => $pData['gender'],
                'dob' => $pData['dob'],
                'age' => $pData['age'],
                'mobile' => $pData['mobile'],
                'alt_mobile' => $pData['alt_mobile'],
                'email' => $pData['email'],
                'address' => $pData['address'],
                'city' => $pData['city'],
                'state' => $pData['state'],
                'country' => 'India',
                'emergency_contact' => $pData['emergency_contact'],
                'emergency_phone' => $pData['emergency_phone'],
                'blood_group' => $pData['blood_group'],
                'occupation' => $pData['occupation'],
                'marital_status' => $pData['marital_status'],
                'referral_source' => $pData['referral_source'],
                'notes' => $pData['notes'],
            ]);

            PatientMedicalHistory::create([
                'patient_id' => $patient->id,
                'conditions' => $pData['conditions'],
                'allergies' => $pData['allergies'],
                'surgeries' => $pData['surgeries'],
                'family_history' => $pData['family_history'],
                'current_medications' => $pData['medications'],
                'notes' => 'Routine monitoring advised every 3-6 months.',
            ]);

            $patients[] = $patient;
        }

        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $twoDaysAgo = now()->subDays(2)->toDateString();
        $threeDaysAgo = now()->subDays(3)->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        // 6. Historic Visits, Progress & Prescriptions for Patient 1 (Amitabh Srivastava)
        // Visit 1: 30 days ago
        $p1 = $patients[0];
        $visit1 = Visit::create([
            'visit_no' => 'VST-20260908-001',
            'patient_id' => $p1->id,
            'doctor_id' => $doctor->id,
            'visit_date' => now()->subDays(30)->toDateString(),
            'visit_type' => 'New',
            'chief_complaint' => 'Uncontrolled fasting sugars, polydipsia, generalized fatigue',
            'symptoms' => 'Excessive thirst, frequent urination at night, post-prandial lethargy',
            'diagnosis_summary' => 'Uncontrolled Type 2 Diabetes Mellitus with Mild Hypertension',
            'vitals_json' => [
                'bp_sys' => 146,
                'bp_dia' => 92,
                'pulse' => 84,
                'temp' => 98.4,
                'weight' => 84.5,
                'height' => 174,
                'bmi' => 27.9,
                'spo2' => 98,
                'random_bs' => 234,
            ],
            'clinical_notes' => 'HbA1c sent. Patient initiated on Metformin dosage optimization and lifestyle diet chart.',
            'treatment_plan' => 'Low glycemic index diet, 45 min brisk walking daily. Tab Metformin 500mg BD. Revisit in 2 weeks.',
            'follow_up_date' => now()->subDays(14)->toDateString(),
        ]);
        VisitDiagnosis::create([
            'visit_id' => $visit1->id,
            'diagnosis_name' => 'Type 2 Diabetes Mellitus',
            'diagnosis_type' => 'primary',
            'notes' => 'Fasting glucose 188 mg/dL, PP 248 mg/dL',
        ]);
        PatientProgress::create([
            'patient_id' => $p1->id,
            'visit_id' => $visit1->id,
            'recorded_date' => now()->subDays(30)->toDateString(),
            'weight' => 84.5,
            'bp_systolic' => 146,
            'bp_diastolic' => 92,
            'pulse' => 84,
            'temperature' => 98.4,
            'spo2' => 98,
            'bmi' => 27.9,
            'pain_level' => 1,
            'symptoms_assessment' => 'High fatigue, frequent nocturia',
            'treatment_response' => 'Initial presentation',
            'doctor_notes' => 'Baseline evaluation established',
        ]);
        $rx1 = Prescription::create([
            'prescription_no' => 'RX-20260908-001',
            'visit_id' => $visit1->id,
            'patient_id' => $p1->id,
            'doctor_id' => $doctor->id,
            'prescription_date' => now()->subDays(30)->toDateString(),
            'diagnosis_summary' => 'Type 2 Diabetes Mellitus & Hypertension',
            'advice' => 'Strict low carb diet, avoid sweets, monitor fasting blood glucose every morning.',
            'follow_up_date' => now()->subDays(14)->toDateString(),
        ]);
        PrescriptionItem::create([
            'prescription_id' => $rx1->id,
            'medicine_name' => 'Tab Metformin Hydrochloride 500mg',
            'dosage' => '500 mg',
            'frequency' => '1-0-1',
            'duration' => '14 Days',
            'route' => 'Oral',
            'timing' => 'After Food',
            'instructions' => 'Take with breakfast and dinner',
        ]);
        PrescriptionItem::create([
            'prescription_id' => $rx1->id,
            'medicine_name' => 'Tab Telmisartan 40mg',
            'dosage' => '40 mg',
            'frequency' => '1-0-0',
            'duration' => '14 Days',
            'route' => 'Oral',
            'timing' => 'Morning After Food',
            'instructions' => 'Monitor BP weekly',
        ]);

        // Invoice for Visit 1 (Paid)
        $inv1 = Invoice::create([
            'invoice_no' => 'INV-202609-001',
            'patient_id' => $p1->id,
            'visit_id' => $visit1->id,
            'doctor_id' => $doctor->id,
            'invoice_date' => now()->subDays(30)->toDateString(),
            'subtotal' => 800.00,
            'discount' => 0.00,
            'additional_charges' => 200.00, // Glucometer test
            'total_amount' => 1000.00,
            'paid_amount' => 1000.00,
            'due_amount' => 0.00,
            'payment_status' => 'paid',
            'payment_method' => 'UPI',
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv1->id,
            'item_description' => 'Comprehensive Physician Consultation Fee',
            'quantity' => 1,
            'unit_price' => 800.00,
            'total' => 800.00,
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv1->id,
            'item_description' => 'Random Blood Sugar (RBS) Instant Point-of-Care Test',
            'quantity' => 1,
            'unit_price' => 200.00,
            'total' => 200.00,
        ]);
        PaymentTransaction::create([
            'invoice_id' => $inv1->id,
            'patient_id' => $p1->id,
            'transaction_no' => 'TXN-202609-001',
            'amount' => 1000.00,
            'payment_method' => 'UPI',
            'payment_date' => now()->subDays(30)->toDateString(),
            'transaction_reference' => 'UPI/GPay/8492049182',
            'collected_by' => 'Pooja Verma',
            'receipt_no' => 'REC-202609-001',
            'notes' => 'Consultation + RBS test',
        ]);

        // Visit 2 for Patient 1: 14 days ago (Weight dropping, BP better)
        $visit2 = Visit::create([
            'visit_no' => 'VST-20260924-002',
            'patient_id' => $p1->id,
            'doctor_id' => $doctor->id,
            'visit_date' => now()->subDays(14)->toDateString(),
            'visit_type' => 'Follow-up',
            'chief_complaint' => 'Routine follow-up review after 2 weeks of Metformin',
            'symptoms' => 'Much reduced thirst, feeling more energetic',
            'diagnosis_summary' => 'Type 2 Diabetes Mellitus - Improving Glycemic control',
            'vitals_json' => [
                'bp_sys' => 134,
                'bp_dia' => 86,
                'pulse' => 78,
                'temp' => 98.6,
                'weight' => 82.8,
                'height' => 174,
                'bmi' => 27.3,
                'spo2' => 99,
                'fasting_bs' => 138,
            ],
            'clinical_notes' => 'Fasting glucose improved from 188 to 138 mg/dL. Weight reduced by 1.7kg. Continue same regimen.',
            'treatment_plan' => 'Maintain 1800 kcal diet. Continue Metformin 500mg BD + Telmisartan 40mg. Follow-up in 2 weeks.',
            'follow_up_date' => $today,
        ]);
        PatientProgress::create([
            'patient_id' => $p1->id,
            'visit_id' => $visit2->id,
            'recorded_date' => now()->subDays(14)->toDateString(),
            'weight' => 82.8,
            'bp_systolic' => 134,
            'bp_diastolic' => 86,
            'pulse' => 78,
            'temperature' => 98.6,
            'spo2' => 99,
            'bmi' => 27.3,
            'pain_level' => 0,
            'symptoms_assessment' => 'Appetite normal, no nocturia',
            'treatment_response' => 'Good response to Metformin',
            'doctor_notes' => 'Significant improvement in blood pressure and weight',
        ]);

        // Invoice for Visit 2 (Follow-up fee ₹500, Paid cash)
        $inv2 = Invoice::create([
            'invoice_no' => 'INV-202609-002',
            'patient_id' => $p1->id,
            'visit_id' => $visit2->id,
            'doctor_id' => $doctor->id,
            'invoice_date' => now()->subDays(14)->toDateString(),
            'subtotal' => 500.00,
            'discount' => 0.00,
            'additional_charges' => 0.00,
            'total_amount' => 500.00,
            'paid_amount' => 500.00,
            'due_amount' => 0.00,
            'payment_status' => 'paid',
            'payment_method' => 'Cash',
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv2->id,
            'item_description' => 'Follow-up Consultation Fee',
            'quantity' => 1,
            'unit_price' => 500.00,
            'total' => 500.00,
        ]);
        PaymentTransaction::create([
            'invoice_id' => $inv2->id,
            'patient_id' => $p1->id,
            'transaction_no' => 'TXN-202609-002',
            'amount' => 500.00,
            'payment_method' => 'Cash',
            'payment_date' => now()->subDays(14)->toDateString(),
            'collected_by' => 'Pooja Verma',
            'receipt_no' => 'REC-202609-002',
            'notes' => 'Follow-up visit fee',
        ]);

        // Add Lab Report for Patient 1
        MedicalReport::create([
            'report_no' => 'REP-202609-001',
            'patient_id' => $p1->id,
            'visit_id' => $visit1->id,
            'report_name' => 'Complete Blood Count & HbA1c Glycated Hemoglobin',
            'report_type' => 'Blood Test',
            'report_date' => now()->subDays(28)->toDateString(),
            'laboratory' => 'Dr. Lal PathLabs, Gurugram',
            'description' => 'HbA1c: 8.6% (Uncontrolled). Fasting Glucose: 182 mg/dL. Hemoglobin: 14.8 g/dL. Total Leukocyte Count: 6,800. Platelets: 240,000.',
            'doctor_notes' => 'Baseline HbA1c is 8.6%. Aiming for < 7.0% within 3 months.',
        ]);

        // 7. TODAY'S APPOINTMENTS & QUEUE
        // Appointment 1: In Consultation (Patient 1 - Amitabh Srivastava)
        $aptToday1 = Appointment::create([
            'appointment_no' => 'APT-20261008-001',
            'patient_id' => $p1->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $today,
            'appointment_time' => '10:00',
            'end_time' => '10:20',
            'appointment_type' => 'follow_up',
            'token_number' => 1,
            'reason' => 'Monthly blood sugar & BP review',
            'notes' => 'Patient has arrived and vitals recorded.',
            'consultation_fee' => 500.00,
            'payment_status' => 'paid',
            'status' => 'in_consultation',
            'waiting_since' => now()->subMinutes(35),
        ]);
        // Invoice for Apt 1
        $invApt1 = Invoice::create([
            'invoice_no' => 'INV-202610-001',
            'patient_id' => $p1->id,
            'appointment_id' => $aptToday1->id,
            'doctor_id' => $doctor->id,
            'invoice_date' => $today,
            'subtotal' => 500.00,
            'discount' => 0.00,
            'additional_charges' => 0.00,
            'total_amount' => 500.00,
            'paid_amount' => 500.00,
            'due_amount' => 0.00,
            'payment_status' => 'paid',
            'payment_method' => 'UPI',
        ]);
        InvoiceItem::create([
            'invoice_id' => $invApt1->id,
            'item_description' => 'Follow-up Consultation Fee',
            'quantity' => 1,
            'unit_price' => 500.00,
            'total' => 500.00,
        ]);
        PaymentTransaction::create([
            'invoice_id' => $invApt1->id,
            'patient_id' => $p1->id,
            'transaction_no' => 'TXN-202610-001',
            'amount' => 500.00,
            'payment_method' => 'UPI',
            'payment_date' => $today,
            'transaction_reference' => 'UPI/Paytm/940294829',
            'collected_by' => 'Pooja Verma',
            'receipt_no' => 'REC-202610-001',
            'notes' => 'Paid at reception upon check-in',
        ]);

        // Appointment 2: Waiting in lobby (Patient 2 - Priya Nambiar)
        $p2 = $patients[1];
        $aptToday2 = Appointment::create([
            'appointment_no' => 'APT-20261008-002',
            'patient_id' => $p2->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $today,
            'appointment_time' => '10:30',
            'end_time' => '10:45',
            'appointment_type' => 'revisit',
            'token_number' => 2,
            'reason' => 'Severe throbbing left hemicranial headache since morning with photophobia',
            'notes' => 'Patient waiting in quiet waiting room.',
            'consultation_fee' => 800.00,
            'payment_status' => 'partially_paid',
            'status' => 'waiting',
            'waiting_since' => now()->subMinutes(18),
        ]);
        // Patient 2 paid partial amount: Total ₹800, Paid ₹500, Due ₹300!
        $invApt2 = Invoice::create([
            'invoice_no' => 'INV-202610-002',
            'patient_id' => $p2->id,
            'appointment_id' => $aptToday2->id,
            'doctor_id' => $doctor->id,
            'invoice_date' => $today,
            'subtotal' => 800.00,
            'discount' => 0.00,
            'additional_charges' => 0.00,
            'total_amount' => 800.00,
            'paid_amount' => 500.00,
            'due_amount' => 300.00,
            'payment_status' => 'partially_paid',
            'payment_method' => 'Cash',
            'notes' => 'Patient paid ₹500 advance in cash. Promised to pay remaining ₹300 via UPI.',
        ]);
        InvoiceItem::create([
            'invoice_id' => $invApt2->id,
            'item_description' => 'Doctor Consultation Fee',
            'quantity' => 1,
            'unit_price' => 800.00,
            'total' => 800.00,
        ]);
        PaymentTransaction::create([
            'invoice_id' => $invApt2->id,
            'patient_id' => $p2->id,
            'transaction_no' => 'TXN-202610-002',
            'amount' => 500.00,
            'payment_method' => 'Cash',
            'payment_date' => $today,
            'collected_by' => 'Pooja Verma',
            'receipt_no' => 'REC-202610-002',
            'notes' => 'Partial advance payment received at front desk',
        ]);

        // Appointment 3: Waiting (Patient 4 - Ananya Deshmukh)
        $p4 = $patients[3];
        $aptToday3 = Appointment::create([
            'appointment_no' => 'APT-20261008-003',
            'patient_id' => $p4->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $today,
            'appointment_time' => '11:00',
            'end_time' => '11:15',
            'appointment_type' => 'new',
            'token_number' => 3,
            'reason' => 'Acute acid reflux, heartburn after meals, nausea',
            'notes' => 'Needs dietary counseling.',
            'consultation_fee' => 800.00,
            'payment_status' => 'unpaid',
            'status' => 'waiting',
            'waiting_since' => now()->subMinutes(10),
        ]);
        $invApt3 = Invoice::create([
            'invoice_no' => 'INV-202610-003',
            'patient_id' => $p4->id,
            'appointment_id' => $aptToday3->id,
            'doctor_id' => $doctor->id,
            'invoice_date' => $today,
            'subtotal' => 800.00,
            'discount' => 0.00,
            'additional_charges' => 0.00,
            'total_amount' => 800.00,
            'paid_amount' => 0.00,
            'due_amount' => 800.00,
            'payment_status' => 'unpaid',
            'payment_method' => null,
            'notes' => 'Payment to be settled after consultation',
        ]);
        InvoiceItem::create([
            'invoice_id' => $invApt3->id,
            'item_description' => 'General Consultation Fee',
            'quantity' => 1,
            'unit_price' => 800.00,
            'total' => 800.00,
        ]);

        // Appointment 4: Scheduled later today (Patient 5 - Vikram Malhotra)
        $p5 = $patients[4];
        $aptToday4 = Appointment::create([
            'appointment_no' => 'APT-20261008-004',
            'patient_id' => $p5->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $today,
            'appointment_time' => '11:30',
            'end_time' => '11:45',
            'appointment_type' => 'follow_up',
            'token_number' => 4,
            'reason' => 'Lipid profile review and uric acid test report check',
            'notes' => 'Confirmed by phone yesterday.',
            'consultation_fee' => 500.00,
            'payment_status' => 'unpaid',
            'status' => 'confirmed',
        ]);

        // Appointment 5: Scheduled afternoon (Patient 7 - Gurpreet Singh)
        $p7 = $patients[6];
        $aptToday5 = Appointment::create([
            'appointment_no' => 'APT-20261008-005',
            'patient_id' => $p7->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $today,
            'appointment_time' => '16:30',
            'end_time' => '16:45',
            'appointment_type' => 'new',
            'token_number' => 5,
            'reason' => 'Acute lower back pain after gym workout, unable to bend',
            'notes' => 'May need pain relief intramuscular injection.',
            'consultation_fee' => 800.00,
            'payment_status' => 'unpaid',
            'status' => 'scheduled',
        ]);

        // Appointment 6: Completed earlier today (Patient 3 - Rajesh Kapoor)
        $p3 = $patients[2];
        $aptToday6 = Appointment::create([
            'appointment_no' => 'APT-20261008-006',
            'patient_id' => $p3->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $today,
            'appointment_time' => '09:00',
            'end_time' => '09:20',
            'appointment_type' => 'revisit',
            'token_number' => 6,
            'reason' => 'Bilateral knee pain, difficulty climbing stairs in morning',
            'notes' => 'Joint examination completed. X-ray bilateral knees ordered.',
            'consultation_fee' => 800.00,
            'payment_status' => 'paid',
            'status' => 'completed',
        ]);
        $visitToday6 = Visit::create([
            'visit_no' => 'VST-20261008-006',
            'patient_id' => $p3->id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $aptToday6->id,
            'visit_date' => $today,
            'visit_type' => 'Revisit',
            'chief_complaint' => 'Knee joint crepitus and stiffness for 3 weeks',
            'symptoms' => 'Mild swelling on medial aspect of both knees, relieved with rest',
            'diagnosis_summary' => 'Bilateral Primary Knee Osteoarthritis (Kellgren-Lawrence Grade 2)',
            'vitals_json' => [
                'bp_sys' => 130,
                'bp_dia' => 82,
                'pulse' => 74,
                'temp' => 98.4,
                'weight' => 76.0,
                'height' => 170,
                'bmi' => 26.3,
                'spo2' => 98,
            ],
            'clinical_notes' => 'Crepitus present on passive flexion. No ligament laxity. Prescribed glucosamine + diacerein and quadriceps strengthening exercises.',
            'treatment_plan' => 'Cap Diacerein + Glucosamine 1 tab daily with dinner. Local Voltaren gel. Physiotherapy consult.',
            'follow_up_date' => now()->addDays(21)->toDateString(),
        ]);
        VisitDiagnosis::create([
            'visit_id' => $visitToday6->id,
            'diagnosis_name' => 'Bilateral Knee Osteoarthritis',
            'diagnosis_type' => 'primary',
            'notes' => 'Grade 2 OA changes',
        ]);
        PatientProgress::create([
            'patient_id' => $p3->id,
            'visit_id' => $visitToday6->id,
            'recorded_date' => $today,
            'weight' => 76.0,
            'bp_systolic' => 130,
            'bp_diastolic' => 82,
            'pulse' => 74,
            'temperature' => 98.4,
            'spo2' => 98,
            'bmi' => 26.3,
            'pain_level' => 6,
            'symptoms_assessment' => 'Knee pain score 6/10 on walking',
            'treatment_response' => 'Initial assessment for knee pain',
            'doctor_notes' => 'Physiotherapy advised for quadriceps strengthening',
        ]);
        $rxToday6 = Prescription::create([
            'prescription_no' => 'RX-20261008-006',
            'visit_id' => $visitToday6->id,
            'patient_id' => $p3->id,
            'doctor_id' => $doctor->id,
            'prescription_date' => $today,
            'diagnosis_summary' => 'Bilateral Knee Osteoarthritis',
            'advice' => 'Avoid squatting and cross-legged sitting. Use western toilet. Quadriceps isometric exercises daily.',
            'follow_up_date' => now()->addDays(21)->toDateString(),
        ]);
        PrescriptionItem::create([
            'prescription_id' => $rxToday6->id,
            'medicine_name' => 'Tab Diacerein 50mg + Glucosamine 750mg',
            'dosage' => '1 Tablet',
            'frequency' => '0-0-1',
            'duration' => '30 Days',
            'route' => 'Oral',
            'timing' => 'After Dinner',
            'instructions' => 'Take with water after food',
        ]);
        PrescriptionItem::create([
            'prescription_id' => $rxToday6->id,
            'medicine_name' => 'Tab Paracetamol 650mg',
            'dosage' => '650 mg',
            'frequency' => 'SOS',
            'duration' => '7 Days',
            'route' => 'Oral',
            'timing' => 'After Food',
            'instructions' => 'Only if knee pain is severe',
        ]);
        $invApt6 = Invoice::create([
            'invoice_no' => 'INV-202610-006',
            'patient_id' => $p3->id,
            'appointment_id' => $aptToday6->id,
            'visit_id' => $visitToday6->id,
            'doctor_id' => $doctor->id,
            'invoice_date' => $today,
            'subtotal' => 800.00,
            'discount' => 0.00,
            'additional_charges' => 0.00,
            'total_amount' => 800.00,
            'paid_amount' => 800.00,
            'due_amount' => 0.00,
            'payment_status' => 'paid',
            'payment_method' => 'Card',
        ]);
        InvoiceItem::create([
            'invoice_id' => $invApt6->id,
            'item_description' => 'Consultation Fee',
            'quantity' => 1,
            'unit_price' => 800.00,
            'total' => 800.00,
        ]);
        PaymentTransaction::create([
            'invoice_id' => $invApt6->id,
            'patient_id' => $p3->id,
            'transaction_no' => 'TXN-202610-006',
            'amount' => 800.00,
            'payment_method' => 'Card',
            'payment_date' => $today,
            'transaction_reference' => 'POS/HDFC/583920',
            'collected_by' => 'Pooja Verma',
            'receipt_no' => 'REC-202610-006',
            'notes' => 'Visa Debit card swipe',
        ]);

        // 8. Historical Appointments & Invoices (Yesterday, This week, Last month)
        // Patient 6 (Meenakshi Sundaram) - Yesterday (Completed, Due amount pending!)
        $p6 = $patients[5];
        $aptYest = Appointment::create([
            'appointment_no' => 'APT-20261007-001',
            'patient_id' => $p6->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $yesterday,
            'appointment_time' => '11:00',
            'end_time' => '11:20',
            'appointment_type' => 'revisit',
            'token_number' => 1,
            'reason' => 'Thyroid dosage adjustment & ECG check',
            'notes' => 'ECG recorded normal sinus rhythm.',
            'consultation_fee' => 800.00,
            'payment_status' => 'partially_paid',
            'status' => 'completed',
        ]);
        $invYest = Invoice::create([
            'invoice_no' => 'INV-202610-007',
            'patient_id' => $p6->id,
            'appointment_id' => $aptYest->id,
            'doctor_id' => $doctor->id,
            'invoice_date' => $yesterday,
            'subtotal' => 800.00,
            'discount' => 100.00, // Courtesy discount
            'additional_charges' => 500.00, // 12-lead ECG test
            'total_amount' => 1200.00, // 800 - 100 + 500
            'paid_amount' => 700.00,
            'due_amount' => 500.00, // Overdue ₹500
            'payment_status' => 'partially_paid',
            'payment_method' => 'UPI',
            'notes' => 'Patient paid ₹700 via UPI. Remaining ₹500 due pending.',
        ]);
        InvoiceItem::create([
            'invoice_id' => $invYest->id,
            'item_description' => 'Consultation Fee',
            'quantity' => 1,
            'unit_price' => 800.00,
            'total' => 800.00,
        ]);
        InvoiceItem::create([
            'invoice_id' => $invYest->id,
            'item_description' => '12-Lead Diagnostic Electrocardiogram (ECG)',
            'quantity' => 1,
            'unit_price' => 500.00,
            'total' => 500.00,
        ]);
        PaymentTransaction::create([
            'invoice_id' => $invYest->id,
            'patient_id' => $p6->id,
            'transaction_no' => 'TXN-202610-007',
            'amount' => 700.00,
            'payment_method' => 'UPI',
            'payment_date' => $yesterday,
            'transaction_reference' => 'UPI/Axis/4829103948',
            'collected_by' => 'Pooja Verma',
            'receipt_no' => 'REC-202610-007',
            'notes' => 'Partial payment made',
        ]);

        // Add ECG Report for Patient 6
        MedicalReport::create([
            'report_no' => 'REP-202610-002',
            'patient_id' => $p6->id,
            'report_name' => '12-Lead Electrocardiogram (ECG) Report',
            'report_type' => 'ECG',
            'report_date' => $yesterday,
            'laboratory' => 'CarePoint Diagnostic Suite',
            'description' => 'Heart Rate: 72 bpm. PR interval: 140ms. QRS duration: 88ms. Normal sinus rhythm. No ST-T segment elevation or inversion.',
            'doctor_notes' => 'Normal baseline ECG. No cardiac ischemia signs.',
        ]);

        // Patient 8 (Sunita Choudhary) - 3 days ago (Completed, Unpaid Due ₹800)
        $p8 = $patients[7];
        $apt3DaysAgo = Appointment::create([
            'appointment_no' => 'APT-20261005-001',
            'patient_id' => $p8->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $threeDaysAgo,
            'appointment_time' => '17:00',
            'end_time' => '17:15',
            'appointment_type' => 'new',
            'token_number' => 1,
            'reason' => 'Blood pressure check, dizziness on standing',
            'notes' => 'Patient forgot purse. Promised to pay online tomorrow.',
            'consultation_fee' => 800.00,
            'payment_status' => 'due',
            'status' => 'completed',
        ]);
        $inv3Days = Invoice::create([
            'invoice_no' => 'INV-202610-008',
            'patient_id' => $p8->id,
            'appointment_id' => $apt3DaysAgo->id,
            'doctor_id' => $doctor->id,
            'invoice_date' => $threeDaysAgo,
            'subtotal' => 800.00,
            'discount' => 0.00,
            'additional_charges' => 0.00,
            'total_amount' => 800.00,
            'paid_amount' => 0.00,
            'due_amount' => 800.00,
            'payment_status' => 'due',
            'payment_method' => null,
            'notes' => 'Payment pending from patient',
        ]);
        InvoiceItem::create([
            'invoice_id' => $inv3Days->id,
            'item_description' => 'Specialist Physician Consultation Fee',
            'quantity' => 1,
            'unit_price' => 800.00,
            'total' => 800.00,
        ]);

        // Patient 9 (Arjun Mehta) - Tomorrow Appointment
        $p9 = $patients[8];
        Appointment::create([
            'appointment_no' => 'APT-20261009-001',
            'patient_id' => $p9->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $tomorrow,
            'appointment_time' => '10:15',
            'end_time' => '10:30',
            'appointment_type' => 'new',
            'token_number' => 1,
            'reason' => 'Allergic rhinitis and nasal congestion consult',
            'notes' => 'Booked online via patient portal.',
            'consultation_fee' => 800.00,
            'payment_status' => 'unpaid',
            'status' => 'confirmed',
        ]);

        // Patient 10 (Kavita Bansal) - Tomorrow Appointment
        $p10 = $patients[9];
        Appointment::create([
            'appointment_no' => 'APT-20261009-002',
            'patient_id' => $p10->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => $tomorrow,
            'appointment_time' => '11:00',
            'end_time' => '11:15',
            'appointment_type' => 'follow_up',
            'token_number' => 2,
            'reason' => 'Hemoglobin re-test and oral iron tolerance follow-up',
            'notes' => 'Scheduled follow-up.',
            'consultation_fee' => 500.00,
            'payment_status' => 'unpaid',
            'status' => 'scheduled',
        ]);

        // 9. Follow-Ups Records
        FollowUp::create([
            'patient_id' => $p1->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visit2->id,
            'follow_up_date' => $today,
            'follow_up_time' => '10:00',
            'reason' => 'Routine 4-week diabetic HbA1c response assessment',
            'notes' => 'Patient in consultation right now.',
            'status' => 'scheduled',
            'reminder_sent' => true,
        ]);
        FollowUp::create([
            'patient_id' => $p3->id,
            'doctor_id' => $doctor->id,
            'visit_id' => $visitToday6->id,
            'follow_up_date' => now()->addDays(21)->toDateString(),
            'follow_up_time' => '09:30',
            'reason' => 'Knee joint pain response to glucosamine therapy',
            'notes' => 'Review with bilateral knee AP and Lateral standing X-rays.',
            'status' => 'scheduled',
            'reminder_sent' => false,
        ]);
        FollowUp::create([
            'patient_id' => $p6->id,
            'doctor_id' => $doctor->id,
            'follow_up_date' => now()->addDays(14)->toDateString(),
            'follow_up_time' => '11:30',
            'reason' => 'TSH response evaluation post Thyronorm titration',
            'notes' => 'Check serum TSH after 2 weeks fasting.',
            'status' => 'scheduled',
            'reminder_sent' => false,
        ]);
        FollowUp::create([
            'patient_id' => $p8->id,
            'doctor_id' => $doctor->id,
            'follow_up_date' => now()->subDay()->toDateString(),
            'follow_up_time' => '16:00',
            'reason' => 'BP tracking verification',
            'notes' => 'Patient missed call yesterday.',
            'status' => 'missed',
            'reminder_sent' => true,
        ]);

        // 10. Expenses
        $expensesData = [
            [
                'category' => 'Rent',
                'amount' => 45000.00,
                'expense_date' => now()->startOfMonth()->toDateString(),
                'payment_method' => 'Bank Transfer',
                'vendor' => 'Urban Estate Realty Ltd',
                'description' => 'Clinic commercial premises monthly lease rent for October 2026',
            ],
            [
                'category' => 'Staff Salary',
                'amount' => 32000.00,
                'expense_date' => now()->startOfMonth()->toDateString(),
                'payment_method' => 'Bank Transfer',
                'vendor' => 'Clinic Staff Payroll',
                'description' => 'Monthly staff remuneration for Front Desk Receptionist & Clinical Nursing Assistant',
            ],
            [
                'category' => 'Electricity',
                'amount' => 6450.00,
                'expense_date' => now()->subDays(5)->toDateString(),
                'payment_method' => 'Online Payment',
                'vendor' => 'DHBVN Power Corporation',
                'description' => 'Commercial power tariff bill for air conditioning, diagnostic lighting and PCs',
            ],
            [
                'category' => 'Medicine',
                'amount' => 14800.00,
                'expense_date' => now()->subDays(8)->toDateString(),
                'payment_method' => 'UPI',
                'vendor' => 'Apollo Medical Wholesale Distributors',
                'description' => 'Emergency medications, disposable syringes, cotton swabs, antiseptic lotions and ECG rolls',
            ],
            [
                'category' => 'Equipment',
                'amount' => 8500.00,
                'expense_date' => now()->subDays(15)->toDateString(),
                'payment_method' => 'Card',
                'vendor' => 'Omron Healthcare India',
                'description' => 'Digital BP monitor calibration kit, pulse oximeters, and digital baby scale',
            ],
            [
                'category' => 'Maintenance',
                'amount' => 3200.00,
                'expense_date' => now()->subDays(12)->toDateString(),
                'payment_method' => 'Cash',
                'vendor' => 'ShineClean Facility Services',
                'description' => 'Bio-medical waste disposal monthly certification and sanitization services',
            ],
        ];
        foreach ($expensesData as $exp) {
            Expense::create(array_merge($exp, ['created_by' => 'System SuperAdmin']));
        }

        // 11. Audit Logs
        AuditLog::create([
            'user_id' => $adminUser->id,
            'user_name' => 'System SuperAdmin',
            'role' => 'super_admin',
            'action' => 'System Initialization',
            'entity_type' => 'System',
            'entity_id' => '1',
            'description' => 'Doctor Clinic Management System initialized with master catalog, doctor profiles, and clinic policies.',
            'ip_address' => '127.0.0.1',
        ]);
        AuditLog::create([
            'user_id' => $staffUser->id,
            'user_name' => 'Pooja Verma',
            'role' => 'receptionist',
            'action' => 'Patient Registered',
            'entity_type' => 'Patient',
            'entity_id' => 'PAT-000001',
            'description' => 'Registered new patient Amitabh Srivastava with demographic and emergency contact details.',
            'ip_address' => '127.0.0.1',
        ]);
        AuditLog::create([
            'user_id' => $staffUser->id,
            'user_name' => 'Pooja Verma',
            'role' => 'receptionist',
            'action' => 'Appointment Checked In',
            'entity_type' => 'Appointment',
            'entity_id' => 'APT-20261008-001',
            'description' => 'Assigned Token #1 to patient Amitabh Srivastava for Dr. Rajiv Sharma.',
            'ip_address' => '127.0.0.1',
        ]);
        AuditLog::create([
            'user_id' => $staffUser->id,
            'user_name' => 'Pooja Verma',
            'role' => 'receptionist',
            'action' => 'Payment Received',
            'entity_type' => 'Invoice',
            'entity_id' => 'INV-202610-001',
            'description' => 'Received payment of ₹500 via UPI against Invoice #INV-202610-001.',
            'ip_address' => '127.0.0.1',
        ]);

        // 12. Notifications
        Notification::create([
            'user_id' => $doctorUser->id,
            'type' => 'appointment',
            'title' => 'Patient Ready in Queue',
            'message' => 'Token #2 Priya Nambiar is waiting in Room 2 for consultation.',
            'link' => '/queue',
            'is_read' => false,
        ]);
        Notification::create([
            'user_id' => $adminUser->id,
            'type' => 'payment',
            'title' => 'Pending Dues Notice',
            'message' => '3 patients have outstanding balances totaling ₹1,600.00.',
            'link' => '/due-payments',
            'is_read' => false,
        ]);
        Notification::create([
            'user_id' => $doctorUser->id,
            'type' => 'follow_up',
            'title' => 'Follow-up Scheduled',
            'message' => 'Rajesh Kapoor scheduled follow-up on ' . now()->addDays(21)->format('d M Y'),
            'link' => '/follow-ups',
            'is_read' => false,
        ]);

        // 13. Patient Communications
        PatientCommunication::create([
            'patient_id' => $p1->id,
            'channel' => 'WhatsApp',
            'type' => 'appointment_reminder',
            'subject' => 'Appointment Reminder',
            'message' => 'Dear Amitabh Srivastava, your appointment with Dr. Rajiv Sharma at CarePoint Clinic is confirmed for today at 10:00 AM (Token #1).',
            'status' => 'Delivered',
            'sent_at' => now()->subHours(2),
        ]);
        PatientCommunication::create([
            'patient_id' => $p3->id,
            'channel' => 'WhatsApp',
            'type' => 'prescription',
            'subject' => 'Prescription Digital Copy',
            'message' => 'Dear Rajesh Kapoor, please find your digital prescription for Visit #VST-20261008-006. Wishing you a speedy recovery.',
            'status' => 'Delivered',
            'sent_at' => now()->subMinutes(45),
        ]);
    }
}
