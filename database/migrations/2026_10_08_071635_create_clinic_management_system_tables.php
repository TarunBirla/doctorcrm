<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update users table with role, phone, avatar
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('super_admin')->after('email'); // super_admin, doctor, receptionist
            $table->string('phone')->nullable()->after('role');
            $table->string('avatar')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('avatar');
        });

        // 2. Clinics table
        Schema::create('clinics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('CarePoint Clinic');
            $table->string('tagline')->nullable()->default('Advanced Care & Healing Center');
            $table->string('doctor_name')->default('Dr. Rajiv Sharma, MD (Medicine)');
            $table->string('doctor_reg_no')->default('MCI-84729-D');
            $table->string('phone')->default('+91 98765 43210');
            $table->string('email')->default('carepoint.clinic@example.com');
            $table->string('address')->default('Plot 45, Sector 12, Medical Enclave');
            $table->string('city')->default('New Delhi');
            $table->string('state')->default('Delhi');
            $table->string('pincode')->default('110001');
            $table->string('website')->nullable()->default('www.carepointclinic.com');
            $table->string('gst_number')->nullable()->default('07AAAAA0000A1Z5');
            $table->decimal('consultation_fee', 10, 2)->default(500.00);
            $table->integer('appointment_duration')->default(15); // minutes
            $table->string('working_days')->default('Monday - Saturday');
            $table->string('working_hours')->default('09:00 AM - 08:00 PM');
            $table->string('break_hours')->default('01:30 PM - 03:30 PM');
            $table->text('prescription_header')->nullable();
            $table->text('invoice_footer')->nullable();
            $table->timestamps();
        });

        // 3. Doctors table
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('specialization')->default('General Physician & Diabetologist');
            $table->string('qualification')->default('MBBS, MD (General Medicine)');
            $table->string('registration_no')->default('MCI-84729-D');
            $table->string('phone')->default('+91 98765 43210');
            $table->string('email')->default('doctor@carepoint.com');
            $table->decimal('consultation_fee', 10, 2)->default(500.00);
            $table->text('bio')->nullable();
            $table->string('signature_image')->nullable();
            $table->timestamps();
        });

        // 4. Doctor Availabilities
        Schema::create('doctor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->string('day_of_week'); // Monday, Tuesday, ...
            $table->string('start_time')->default('09:00');
            $table->string('end_time')->default('20:00');
            $table->string('break_start')->nullable()->default('13:30');
            $table->string('break_end')->nullable()->default('15:30');
            $table->integer('slot_duration')->default(15);
            $table->integer('max_patients')->default(30);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // 5. Clinic Holidays
        Schema::create('clinic_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->date('holiday_date');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // 6. Patients table
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->unique(); // e.g. PAT-000001
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender')->default('Male'); // Male, Female, Other
            $table->date('dob')->nullable();
            $table->integer('age')->default(0);
            $table->string('mobile')->index();
            $table->string('alt_mobile')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('India');
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone')->nullable();
            $table->string('blood_group')->nullable(); // A+, B+, etc.
            $table->string('occupation')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('referral_source')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 7. Patient Medical History
        Schema::create('patient_medical_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->text('conditions')->nullable(); // Existing conditions
            $table->text('allergies')->nullable(); // Drug / food allergies
            $table->text('surgeries')->nullable(); // Past surgeries
            $table->text('family_history')->nullable(); // Diabetes, Hypertension
            $table->text('current_medications')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Appointments table
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_no')->unique(); // APT-20261008-001
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->date('appointment_date')->index();
            $table->string('appointment_time'); // "10:30"
            $table->string('end_time')->nullable();
            $table->string('appointment_type')->default('new'); // new, follow_up, revisit, emergency
            $table->integer('token_number')->default(1);
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('consultation_fee', 10, 2)->default(500.00);
            $table->string('payment_status')->default('unpaid'); // unpaid, partially_paid, paid, due, refunded
            $table->string('status')->default('scheduled'); // scheduled, confirmed, waiting, in_consultation, completed, cancelled, no_show, rescheduled
            $table->timestamp('waiting_since')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 9. Visits table
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_no')->unique(); // VST-20261008-001
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->date('visit_date');
            $table->string('visit_type')->default('New'); // New, Follow-up, Revisit, Emergency
            $table->text('chief_complaint');
            $table->text('symptoms')->nullable();
            $table->text('diagnosis_summary')->nullable();
            $table->json('vitals_json')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });

        // 10. Diagnoses master catalog
        Schema::create('diagnoses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 11. Visit Diagnoses
        Schema::create('visit_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->string('diagnosis_name');
            $table->string('diagnosis_type')->default('primary'); // primary, secondary
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 12. Prescriptions
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('prescription_no')->unique();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->date('prescription_date');
            $table->text('diagnosis_summary')->nullable();
            $table->text('advice')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });

        // 13. Prescription Items
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->string('medicine_name');
            $table->string('dosage')->nullable(); // 500mg, 10ml, etc.
            $table->string('frequency')->default('1-0-1'); // 1-0-1, 1-1-1, etc.
            $table->string('duration')->default('5 Days');
            $table->string('route')->default('Oral');
            $table->string('timing')->default('After Food'); // Before Food, After Food, etc.
            $table->string('instructions')->nullable();
            $table->timestamps();
        });

        // 14. Medical Reports
        Schema::create('medical_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_no')->unique();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->string('report_name');
            $table->string('report_type')->default('Blood Test'); // Blood Test, Urine Test, X-Ray, MRI, CT Scan, Ultrasound, ECG, Pathology, Other
            $table->date('report_date');
            $table->string('laboratory')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_size')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->timestamps();
        });

        // 15. Patient Progress
        Schema::create('patient_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->date('recorded_date');
            $table->decimal('weight', 5, 2)->nullable();
            $table->integer('bp_systolic')->nullable();
            $table->integer('bp_diastolic')->nullable();
            $table->integer('pulse')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('spo2')->nullable();
            $table->decimal('bmi', 4, 1)->nullable();
            $table->integer('pain_level')->nullable(); // 0 to 10
            $table->text('symptoms_assessment')->nullable();
            $table->text('treatment_response')->nullable();
            $table->text('doctor_notes')->nullable();
            $table->timestamps();
        });

        // 16. Follow-ups
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->date('follow_up_date');
            $table->string('follow_up_time')->nullable();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, completed, missed, cancelled
            $table->boolean('reminder_sent')->default(false);
            $table->timestamps();
        });

        // 17. Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // INV-202610-001
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->date('invoice_date');
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('additional_charges', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->decimal('due_amount', 10, 2)->default(0.00);
            $table->string('payment_status')->default('unpaid'); // unpaid, partially_paid, paid, due, refunded
            $table->string('payment_method')->nullable(); // Cash, UPI, Card, Bank Transfer, Online Payment, Other
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 18. Invoice Items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('item_description');
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // 19. Payment Transactions
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('transaction_no')->unique(); // TXN-202610-001
            $table->decimal('amount', 10, 2);
            $table->string('payment_method')->default('Cash'); // Cash, UPI, Card, Bank Transfer, Online Payment, Other
            $table->date('payment_date');
            $table->string('transaction_reference')->nullable();
            $table->string('collected_by')->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_no')->unique(); // REC-202610-001
            $table->timestamps();
        });

        // 20. Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category'); // Rent, Staff Salary, Electricity, Medicine, Equipment, Maintenance, Other
            $table->decimal('amount', 10, 2);
            $table->date('expense_date');
            $table->string('payment_method')->default('Cash');
            $table->string('vendor')->nullable();
            $table->text('description');
            $table->string('receipt_path')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });

        // 21. Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('role')->nullable();
            $table->string('action'); // e.g. "Patient Created", "Payment Received"
            $table->string('entity_type')->nullable(); // Patient, Appointment, Payment, etc.
            $table->string('entity_id')->nullable();
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });

        // 22. Notifications
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('general'); // appointment, payment, follow_up, alert
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // 23. Patient Communications
        Schema::create('patient_communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('channel')->default('WhatsApp'); // WhatsApp, SMS, Email, Call
            $table->string('type')->default('appointment_reminder'); // appointment_reminder, payment_reminder, prescription, report, invoice, general
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status')->default('Sent');
            $table->timestamp('sent_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_communications');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('patient_progress');
        Schema::dropIfExists('medical_reports');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('visit_diagnoses');
        Schema::dropIfExists('diagnoses');
        Schema::dropIfExists('visits');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('patient_medical_histories');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('clinic_holidays');
        Schema::dropIfExists('doctor_availabilities');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('clinics');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone', 'avatar', 'is_active']);
        });
    }
};
