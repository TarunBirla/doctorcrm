<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Execution Result - CarePoint Clinic</title>
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
        <!-- Status Card -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center gap-4">
                @if($success)
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i data-lucide="check-circle-2" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Database Ready & Seeded!</h1>
                        <p class="text-xs text-slate-500">{{ $message }}</p>
                    </div>
                @else
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center">
                        <i data-lucide="x-circle" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Setup Encountered an Issue</h1>
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    </div>
                @endif
            </div>

            <!-- Terminal Output Log -->
            <div class="space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Artisan Execution Log</span>
                <div class="bg-slate-950 text-slate-200 p-4 rounded-xl font-mono text-xs max-h-96 overflow-y-auto space-y-1">
                    @foreach($output as $line)
                        <div class="whitespace-pre-wrap">{{ $line }}</div>
                    @endforeach

                    @foreach($errors as $err)
                        <div class="text-rose-400 font-bold whitespace-pre-wrap">{{ $err }}</div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                @if($success)
                    <a href="{{ route('dashboard') }}" class="w-full sm:w-auto flex-1 py-3 px-6 rounded-xl bg-[#0a2540] hover:bg-slate-800 text-white font-bold text-sm shadow-md transition flex items-center justify-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-4 h-4 text-emerald-400"></i> Open Clinic Management Dashboard
                    </a>
                @endif
                <a href="{{ route('database.setup') }}" class="w-full sm:w-auto py-3 px-6 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm transition flex items-center justify-center gap-2">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Database Hub
                </a>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
