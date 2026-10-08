<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup & Deployment - CarePoint Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between text-slate-800 p-4 sm:p-8">
    <div class="max-w-3xl mx-auto w-full my-auto space-y-6">
        <!-- Brand Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-[#0a2540] text-white shadow-lg mb-2">
                <i data-lucide="database" class="w-7 h-7"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">CarePoint Clinic Database Setup</h1>
            <p class="text-sm text-slate-500">Live Server Deployment & Database Migration Hub (<span class="font-mono text-xs font-semibold text-primary">https://crm.physiopii.in/</span>)</p>
        </div>

        <!-- Connection Status Banner -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-3.5 h-3.5 rounded-full {{ $connectionStatus ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Database Connection Status</h2>
                        <p class="text-xs text-slate-400">Driver: <span class="font-mono font-bold uppercase text-slate-700">{{ $dbDriver }}</span></p>
                    </div>
                </div>

                @if($connectionStatus)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Connected
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Disconnected
                    </span>
                @endif
            </div>

            @if(!$connectionStatus && $connectionError)
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-2">
                    <strong class="font-bold flex items-center gap-1.5"><i data-lucide="alert-circle" class="w-4 h-4"></i> Connection Error:</strong>
                    <p class="font-mono break-all">{{ $connectionError }}</p>
                    <p class="mt-2 text-slate-600">Please check your live server <code class="bg-rose-100 px-1 py-0.5 rounded font-mono">.env</code> file credentials (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD).</p>
                </div>
            @endif

            <!-- Database Diagnostic Metrics -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 text-center">
                    <span class="text-[11px] font-bold text-slate-400 uppercase block">Tables Detected</span>
                    <span class="text-xl font-extrabold text-slate-900">{{ $tableCount }}</span>
                    <span class="text-[10px] text-slate-400 block">{{ $tablesExist ? 'All 23 Installed' : 'Not yet created' }}</span>
                </div>

                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 text-center">
                    <span class="text-[11px] font-bold text-slate-400 uppercase block">Users Seeded</span>
                    <span class="text-xl font-extrabold text-slate-900">{{ $usersCount }}</span>
                    <span class="text-[10px] text-slate-400 block">{{ $usersCount >= 3 ? 'Admin, Doctor, Staff' : 'Pending seed' }}</span>
                </div>

                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 text-center">
                    <span class="text-[11px] font-bold text-slate-400 uppercase block">Patient Records</span>
                    <span class="text-xl font-extrabold text-slate-900">{{ $patientsCount }}</span>
                    <span class="text-[10px] text-slate-400 block">Profiles ready</span>
                </div>

                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 text-center">
                    <span class="text-[11px] font-bold text-slate-400 uppercase block">Target Database</span>
                    <span class="text-xs font-mono font-bold text-slate-800 truncate block mt-1" title="{{ $dbConnection['database'] ?? '' }}">
                        {{ basename($dbConnection['database'] ?? 'database') }}
                    </span>
                    <span class="text-[10px] text-slate-400 block">{{ $dbDriver }}</span>
                </div>
            </div>

            <!-- Migration Actions -->
            <div class="space-y-3 pt-2">
                <form action="{{ route('database.setup.run') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-[#0a2540] hover:bg-slate-800 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2 group">
                        <i data-lucide="play" class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition"></i>
                        {{ $tablesExist ? 'Re-Run Migration & Seed Missing Data' : 'Run Migrations & Create Database Now' }}
                    </button>
                </form>

                <div class="flex items-center gap-3">
                    <form action="{{ route('database.setup.fresh') }}" method="POST" class="flex-1" onsubmit="return confirm('WARNING: This will DROP ALL TABLES and recreate everything fresh with default accounts. Continue?');">
                        @csrf
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition flex items-center justify-center gap-1.5">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Fresh Reset (Drop & Re-seed)
                        </button>
                    </form>

                    @if($tablesExist)
                        <a href="{{ route('dashboard') }}" class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-sm">
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5 text-primary"></i> Open Clinic Dashboard
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Live Server .env Configuration Guide for cPanel / MySQL -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i data-lucide="server" class="w-4 h-4 text-primary"></i> Live Server Environment Setup Guide
            </h3>

            <div class="space-y-2 text-xs text-slate-600">
                <p>If deploying to your live server (e.g. cPanel, Plesk, or Linux VPS) with <strong>MySQL</strong>, make sure your <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-slate-800">.env</code> contains your live database credentials:</p>
                <div class="bg-slate-900 text-slate-200 p-4 rounded-xl font-mono text-[11px] space-y-1 overflow-x-auto">
                    <div><span class="text-slate-400">APP_URL=</span>https://crm.physiopii.in</div>
                    <div><span class="text-slate-400">DB_CONNECTION=</span>mysql</div>
                    <div><span class="text-slate-400">DB_HOST=</span>127.0.0.1</div>
                    <div><span class="text-slate-400">DB_PORT=</span>3306</div>
                    <div><span class="text-slate-400">DB_DATABASE=</span><span class="text-emerald-400">your_live_db_name</span></div>
                    <div><span class="text-slate-400">DB_USERNAME=</span><span class="text-emerald-400">your_live_db_user</span></div>
                    <div><span class="text-slate-400">DB_PASSWORD=</span><span class="text-amber-400">your_live_db_password</span></div>
                </div>
                <p class="text-slate-500">Alternatively, if you want zero-configuration standalone storage without creating a MySQL database, you can leave <code class="font-mono bg-slate-100 px-1 rounded">DB_CONNECTION=sqlite</code> and this button will automatically manage the database file for you.</p>
            </div>
        </div>

        <!-- Default Credentials Reference -->
        <div class="bg-slate-100 rounded-2xl p-5 border border-slate-200/80 text-xs space-y-3">
            <h4 class="font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="key" class="w-4 h-4 text-primary"></i> Default Login Credentials (Created by Seeder)
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="bg-white p-3 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-900 block">Super Admin</span>
                    <span class="text-slate-500 block font-mono text-[11px]">admin@carepoint.com</span>
                    <span class="text-slate-400 block font-mono text-[11px]">Password: password</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-900 block">Doctor</span>
                    <span class="text-slate-500 block font-mono text-[11px]">doctor@carepoint.com</span>
                    <span class="text-slate-400 block font-mono text-[11px]">Password: password</span>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200">
                    <span class="font-bold text-slate-900 block">Receptionist / Staff</span>
                    <span class="text-slate-500 block font-mono text-[11px]">staff@carepoint.com</span>
                    <span class="text-slate-400 block font-mono text-[11px]">Password: password</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
