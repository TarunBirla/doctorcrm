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
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'recovery_percentage')) {
                $table->unsignedTinyInteger('recovery_percentage')->default(0)->after('treatment_days');
            }
            if (!Schema::hasColumn('appointments', 'extended_days')) {
                $table->unsignedSmallInteger('extended_days')->default(0)->after('recovery_percentage');
            }
            if (!Schema::hasColumn('appointments', 'recovery_status')) {
                $table->string('recovery_status')->nullable()->after('extended_days');
            }
            if (!Schema::hasColumn('appointments', 'recovery_notes')) {
                $table->text('recovery_notes')->nullable()->after('recovery_status');
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'recovery_percentage')) {
                $table->unsignedTinyInteger('recovery_percentage')->default(0)->after('category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'recovery_percentage')) {
                $table->dropColumn('recovery_percentage');
            }
            if (Schema::hasColumn('appointments', 'extended_days')) {
                $table->dropColumn('extended_days');
            }
            if (Schema::hasColumn('appointments', 'recovery_status')) {
                $table->dropColumn('recovery_status');
            }
            if (Schema::hasColumn('appointments', 'recovery_notes')) {
                $table->dropColumn('recovery_notes');
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'recovery_percentage')) {
                $table->dropColumn('recovery_percentage');
            }
        });
    }
};
