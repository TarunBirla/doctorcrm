<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Safe fallback if SESSION_DRIVER=database is set before migrations are run
        if (config('session.driver') === 'database') {
            try {
                if (!\Illuminate\Support\Facades\Schema::hasTable('sessions')) {
                    config(['session.driver' => 'file']);
                }
            } catch (\Throwable $e) {
                config(['session.driver' => 'file']);
            }
        }

        // Auto-ensure staff columns exist on users table for live server compatibility
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && !\Illuminate\Support\Facades\Schema::hasColumn('users', 'doctor_id')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'doctor_id')) {
                        $table->unsignedBigInteger('doctor_id')->nullable()->after('role');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'position')) {
                        $table->string('position', 100)->nullable()->after('phone');
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'status')) {
                        $table->string('status', 30)->default('active')->after('is_active');
                    }
                });
            }
        } catch (\Throwable $e) {
            // Ignore schema inspection errors if DB is not yet configured
        }
    }
}
