<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0a2540">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/logo.png">
    <title>PWA Setup & App Installer - SDPC Physiotherapy Clinic CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex items-center justify-center p-4 sm:p-6">
    <div class="max-w-2xl w-full bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
        <!-- Header Banner -->
        <div class="bg-[#0a2540] text-white p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white p-2 flex items-center justify-center shrink-0 shadow">
                    <img src="{{ asset('logo.png') }}" alt="SDPC" class="w-full h-full object-contain">
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 mb-1">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Server PWA Package Ready
                    </span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">SDPC CRM — PWA Installer</h1>
                    <p class="text-xs text-slate-300 mt-0.5">Progressive Web App Manifest, Service Worker & Cache Setup</p>
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Execution Logs -->
            <div>
                <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                    <i data-lucide="terminal" class="w-4 h-4 text-blue-600"></i> Server Installation & Publish Logs
                </h2>
                <div class="bg-slate-900 text-emerald-400 rounded-xl p-4 font-mono text-xs space-y-1.5 overflow-x-auto">
                    @foreach($logs as $log)
                        <div>{{ $log }}</div>
                    @endforeach
                </div>
            </div>

            <!-- Direct App Download / Install Card -->
            <div class="bg-gradient-to-br from-blue-50 via-indigo-50/60 to-slate-50 border border-blue-200 rounded-2xl p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                            <i data-lucide="smartphone" class="w-5 h-5 text-blue-600"></i>
                            Download & Install SDPC CRM App
                        </h3>
                        <p class="text-xs text-slate-600 font-semibold leading-relaxed">
                            Install the CRM directly on your Mobile (Android / iPhone) or Laptop/Desktop for 1-click home screen access.
                        </p>
                    </div>
                    <button type="button" id="setupInstallBtn" onclick="installPwaFromSetup()"
                        class="w-full sm:w-auto shrink-0 inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#0a2540] hover:bg-slate-800 text-white rounded-xl text-xs font-extrabold transition shadow-md cursor-pointer">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span id="setupInstallBtnText">Download / Install App</span>
                    </button>
                </div>

                <div id="setupInstallGuide" class="hidden mt-4 pt-4 border-t border-blue-200/70 text-xs text-slate-700 space-y-2">
                    <p class="font-extrabold text-slate-900">How to Install on Your Device:</p>
                    <ul class="list-disc list-inside space-y-1 font-semibold text-slate-600">
                        <li><strong>Android / Chrome / Edge (Laptop & Mobile):</strong> Click the <strong>Install App</strong> icon <span class="px-1.5 py-0.5 bg-white rounded border text-[11px]">⊕</span> in the browser address bar or menu <strong>⋮ → Install App / Add to Home Screen</strong>.</li>
                        <li><strong>iPhone / iPad (Safari):</strong> Tap the <strong>Share</strong> button <span class="px-1.5 py-0.5 bg-white rounded border text-[11px]">↑</span> at the bottom and select <strong>"Add to Home Screen"</strong>.</li>
                    </ul>
                </div>
            </div>

            <!-- Action Links -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <a href="/manifest.json" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        <i data-lucide="file-json" class="w-3.5 h-3.5 text-blue-600"></i> View manifest.json
                    </a>
                    <a href="/sw.js" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        <i data-lucide="cpu" class="w-3.5 h-3.5 text-emerald-600"></i> View sw.js
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-xs">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i> Go to My Profile
                    </a>
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#0a2540] hover:bg-slate-800 text-white text-xs font-bold transition shadow-xs">
                        <i data-lucide="layout-grid" class="w-3.5 h-3.5"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        let deferredPrompt = null;
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
        });

        async function installPwaFromSetup() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    document.getElementById('setupInstallBtnText').innerText = 'App Installed ✔';
                }
                deferredPrompt = null;
            } else {
                document.getElementById('setupInstallGuide').classList.remove('hidden');
            }
        }
    </script>
</body>
</html>
