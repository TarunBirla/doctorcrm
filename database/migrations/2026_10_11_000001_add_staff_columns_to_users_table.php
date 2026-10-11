<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'doctor_id')) {
                    $table->unsignedBigInteger('doctor_id')->nullable()->after('role');
                }
                if (!Schema::hasColumn('users', 'position')) {
                    $table->string('position', 100)->nullable()->after('phone');
                }
                if (!Schema::hasColumn('users', 'status')) {
                    $table->string('status', 30)->default('active')->after('is_active');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('users', 'doctor_id')) $cols[] = 'doctor_id';
                if (Schema::hasColumn('users', 'position')) $cols[] = 'position';
                if (Schema::hasColumn('users', 'status')) $cols[] = 'status';
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
};
