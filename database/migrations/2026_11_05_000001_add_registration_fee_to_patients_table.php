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
        if (Schema::hasTable('patients') && !Schema::hasColumn('patients', 'registration_fee')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->decimal('registration_fee', 10, 2)->default(0)->after('notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('patients') && Schema::hasColumn('patients', 'registration_fee')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('registration_fee');
            });
        }
    }
};
