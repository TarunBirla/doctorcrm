<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('clinic_holidays')) {
            Schema::create('clinic_holidays', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->date('holiday_date');
                $table->string('description')->nullable();
                $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
                $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
                $table->timestamps();
            });
        } else {
            Schema::table('clinic_holidays', function (Blueprint $table) {
                if (!Schema::hasColumn('clinic_holidays', 'clinic_id')) {
                    $table->foreignId('clinic_id')->nullable()->after('description')->constrained('clinics')->nullOnDelete();
                }
                if (!Schema::hasColumn('clinic_holidays', 'doctor_id')) {
                    $table->foreignId('doctor_id')->nullable()->after('clinic_id')->constrained('doctors')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('clinic_holidays')) {
            Schema::table('clinic_holidays', function (Blueprint $table) {
                if (Schema::hasColumn('clinic_holidays', 'doctor_id')) {
                    $table->dropForeign(['doctor_id']);
                    $table->dropColumn('doctor_id');
                }
                if (Schema::hasColumn('clinic_holidays', 'clinic_id')) {
                    $table->dropForeign(['clinic_id']);
                    $table->dropColumn('clinic_id');
                }
            });
        }
    }
};
