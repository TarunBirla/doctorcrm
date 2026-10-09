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
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'clinic_id')) {
                $table->foreignId('clinic_id')->nullable()->after('doctor_id')->constrained('clinics')->nullOnDelete();
            }
            if (!Schema::hasColumn('prescriptions', 'assessment_type')) {
                $table->string('assessment_type')->default('musculoskeletal')->after('prescription_date'); // 'musculoskeletal', 'neurological', 'general'
            }
            if (!Schema::hasColumn('prescriptions', 'assessment_data')) {
                $table->json('assessment_data')->nullable()->after('assessment_type');
            }
            if (!Schema::hasColumn('prescriptions', 'prescribed_exercises')) {
                $table->json('prescribed_exercises')->nullable()->after('diagnosis_summary');
            }
            if (!Schema::hasColumn('prescriptions', 'modalities')) {
                $table->text('modalities')->nullable()->after('prescribed_exercises');
            }
            if (!Schema::hasColumn('prescriptions', 'treatment_days')) {
                $table->integer('treatment_days')->nullable()->default(7)->after('modalities');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (Schema::hasColumn('prescriptions', 'treatment_days')) {
                $table->dropColumn('treatment_days');
            }
            if (Schema::hasColumn('prescriptions', 'modalities')) {
                $table->dropColumn('modalities');
            }
            if (Schema::hasColumn('prescriptions', 'prescribed_exercises')) {
                $table->dropColumn('prescribed_exercises');
            }
            if (Schema::hasColumn('prescriptions', 'assessment_data')) {
                $table->dropColumn('assessment_data');
            }
            if (Schema::hasColumn('prescriptions', 'assessment_type')) {
                $table->dropColumn('assessment_type');
            }
            if (Schema::hasColumn('prescriptions', 'clinic_id')) {
                $table->dropForeign(['clinic_id']);
                $table->dropColumn('clinic_id');
            }
        });
    }
};
