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
        // 1. Clinics: Link clinic to a doctor (owner/manager) and is_active flag
        Schema::table('clinics', function (Blueprint $table) {
            if (!Schema::hasColumn('clinics', 'doctor_id')) {
                $table->foreignId('doctor_id')->nullable()->after('id')->constrained('doctors')->nullOnDelete();
            }
            if (!Schema::hasColumn('clinics', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('invoice_footer');
            }
        });

        // 2. Pivot table: doctor_clinics (allowing doctors to practice at multiple clinics)
        if (!Schema::hasTable('doctor_clinics')) {
            Schema::create('doctor_clinics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
            });
        }

        // 3. Patients: strict doctor isolation (patient belongs to doctor who registered them)
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'doctor_id')) {
                $table->foreignId('doctor_id')->nullable()->after('id')->constrained('doctors')->nullOnDelete();
            }
            if (!Schema::hasColumn('patients', 'created_by_user_id')) {
                $table->foreignId('created_by_user_id')->nullable()->after('doctor_id')->constrained('users')->nullOnDelete();
            }
        });

        // 4. Appointments: link appointment to specific clinic
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'clinic_id')) {
                $table->foreignId('clinic_id')->nullable()->after('doctor_id')->constrained('clinics')->nullOnDelete();
            }
        });

        // 5. Doctor Availabilities: schedule is clinic-wise (Doctor + Clinic + Day)
        Schema::table('doctor_availabilities', function (Blueprint $table) {
            if (!Schema::hasColumn('doctor_availabilities', 'clinic_id')) {
                $table->foreignId('clinic_id')->nullable()->after('doctor_id')->constrained('clinics')->cascadeOnDelete();
            }
        });

        // 6. Specific slot overrides table (for granular slot customization: block, add, delete, change)
        if (!Schema::hasTable('doctor_slot_overrides')) {
            Schema::create('doctor_slot_overrides', function (Blueprint $table) {
                $table->id();
                $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
                $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
                $table->date('slot_date');
                $table->string('slot_time'); // "10:30"
                $table->boolean('is_blocked')->default(false);
                $table->string('reason')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_slot_overrides');
        Schema::dropIfExists('doctor_clinics');

        Schema::table('doctor_availabilities', function (Blueprint $table) {
            if (Schema::hasColumn('doctor_availabilities', 'clinic_id')) {
                $table->dropForeign(['clinic_id']);
                $table->dropColumn('clinic_id');
            }
        });

        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'clinic_id')) {
                $table->dropForeign(['clinic_id']);
                $table->dropColumn('clinic_id');
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'doctor_id')) {
                $table->dropForeign(['doctor_id']);
                $table->dropColumn('doctor_id');
            }
            if (Schema::hasColumn('patients', 'created_by_user_id')) {
                $table->dropForeign(['created_by_user_id']);
                $table->dropColumn('created_by_user_id');
            }
        });

        Schema::table('clinics', function (Blueprint $table) {
            if (Schema::hasColumn('clinics', 'doctor_id')) {
                $table->dropForeign(['doctor_id']);
                $table->dropColumn('doctor_id');
            }
            if (Schema::hasColumn('clinics', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
