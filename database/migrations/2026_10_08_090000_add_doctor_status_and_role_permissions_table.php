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
        // Add is_active to doctors if not already exists
        if (Schema::hasTable('doctors') && !Schema::hasColumn('doctors', 'is_active')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('signature_image');
            });
        }

        // Create role_menu_permissions table
        if (!Schema::hasTable('role_menu_permissions')) {
            Schema::create('role_menu_permissions', function (Blueprint $table) {
                $table->id();
                $table->string('role'); // doctor, receptionist
                $table->string('menu_key'); // dashboard, queue, appointments, calendar, patients, consultations, prescriptions, reports, progress, followups, billing, dues, payments, expenses, analytical_reports
                $table->boolean('is_visible')->default(true);
                $table->timestamps();

                $table->unique(['role', 'menu_key']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_menu_permissions');

        if (Schema::hasTable('doctors') && Schema::hasColumn('doctors', 'is_active')) {
            Schema::table('doctors', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
