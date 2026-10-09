<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;

class DatabaseSetupController extends Controller
{
    /**
     * Show Database Setup Dashboard
     */
    public function index()
    {
        $dbDriver = config('database.default');
        $dbConnection = config("database.connections.{$dbDriver}");
        
        $connectionStatus = false;
        $connectionError = null;
        $tablesExist = false;
        $tableCount = 0;
        $usersCount = 0;
        $patientsCount = 0;

        try {
            DB::connection()->getPdo();
            $connectionStatus = true;

            $tablesExist = Schema::hasTable('users') && Schema::hasTable('patients');

            if ($dbDriver === 'sqlite') {
                $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%';");
                $tableCount = count($tables);
            } else {
                $tables = DB::select("SHOW TABLES");
                $tableCount = count($tables);
            }

            if (Schema::hasTable('users')) {
                $usersCount = DB::table('users')->count();
            }
            if (Schema::hasTable('patients')) {
                $patientsCount = DB::table('patients')->count();
            }
        } catch (Exception $e) {
            $connectionStatus = false;
            $connectionError = $e->getMessage();
        }

        return view('setup.database', compact(
            'dbDriver',
            'dbConnection',
            'connectionStatus',
            'connectionError',
            'tablesExist',
            'tableCount',
            'usersCount',
            'patientsCount'
        ));
    }

    /**
     * Run Migrations & Seed Default Data
     */
    public function run(Request $request)
    {
        $output = [];
        $errors = [];

        try {
            $driver = config('database.default');

            // 1. If SQLite, ensure database file exists
            if ($driver === 'sqlite') {
                $sqlitePath = database_path('database.sqlite');
                if (!file_exists($sqlitePath)) {
                    touch($sqlitePath);
                    $output[] = "✓ Created SQLite database file at: {$sqlitePath}";
                }
            }

            // 2. Test Connection
            DB::connection()->getPdo();
            $output[] = "✓ Connected to {$driver} database successfully.";

            // 3. Run Migrations
            Artisan::call('migrate', ['--force' => true]);
            $output[] = "--- MIGRATIONS OUTPUT ---";
            $output[] = trim(Artisan::output());

            // 4. Run Seeders
            Artisan::call('db:seed', ['--force' => true]);
            $output[] = "--- SEEDERS OUTPUT ---";
            $output[] = trim(Artisan::output());

            // 5. Create storage symlink
            try {
                Artisan::call('storage:link');
                $output[] = "✓ Storage link verified: " . trim(Artisan::output());
            } catch (Exception $ex) {
                // Ignore if symlink already exists
            }

            // 6. Clear & optimize caches
            try {
                Artisan::call('optimize:clear');
                $output[] = "✓ Application cache refreshed.";
            } catch (Exception $ex) {
                // Ignore
            }

            $success = true;
            $message = "Database successfully set up with all tables and initial records!";
        } catch (Exception $e) {
            $success = false;
            $message = "Database Setup Failed: " . $e->getMessage();
            $errors[] = $e->getMessage();
        }

        return view('setup.result', compact('success', 'message', 'output', 'errors'));
    }

    /**
     * Run Fresh Migration (Drop All & Re-seed)
     */
    public function fresh(Request $request)
    {
        $output = [];
        $errors = [];

        try {
            $driver = config('database.default');

            if ($driver === 'sqlite') {
                $sqlitePath = database_path('database.sqlite');
                if (!file_exists($sqlitePath)) {
                    touch($sqlitePath);
                }
            }

            DB::connection()->getPdo();

            Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
            $output[] = "--- MIGRATE FRESH & SEED OUTPUT ---";
            $output[] = trim(Artisan::output());

            try {
                Artisan::call('optimize:clear');
            } catch (Exception $ex) {
                // Ignore
            }

            $success = true;
            $message = "Database cleanly reset and re-seeded from scratch!";
        } catch (Exception $e) {
            $success = false;
            $message = "Reset Failed: " . $e->getMessage();
            $errors[] = $e->getMessage();
        }

        return view('setup.result', compact('success', 'message', 'output', 'errors'));
    }

    /**
     * Clean all patient/transaction data and retain 2 clinics
     */
    public function clean(Request $request)
    {
        $output = [];
        $errors = [];

        try {
            DB::connection()->getPdo();

            Artisan::call('app:clean-fresh-db');
            $output[] = "--- CLEAN FRESH DB OUTPUT ---";
            $output[] = trim(Artisan::output());

            try {
                Artisan::call('optimize:clear');
            } catch (Exception $ex) {
                // Ignore
            }

            $success = true;
            $message = "Database successfully cleaned! All dummy patients removed, exactly 2 clinics ready for doctor testing.";
        } catch (Exception $e) {
            $success = false;
            $message = "Clean Failed: " . $e->getMessage();
            $errors[] = $e->getMessage();
        }

        return view('setup.result', compact('success', 'message', 'output', 'errors'));
    }
}
