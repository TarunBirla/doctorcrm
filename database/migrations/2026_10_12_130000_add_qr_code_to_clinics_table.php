<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('clinics', 'qr_code')) {
            Schema::table('clinics', function (Blueprint $table) {
                $table->string('qr_code')->nullable()->after('invoice_footer');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clinics', 'qr_code')) {
            Schema::table('clinics', function (Blueprint $table) {
                $table->dropColumn('qr_code');
            });
        }
    }
};
