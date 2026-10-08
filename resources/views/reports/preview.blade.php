<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic Report - {{ $report->report_no }} - {{ $report->report_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            body { background: #ffffff !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .print-sheet { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="p-4 sm:p-8">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Action Bar -->
        <div class="flex items-center justify-between no-print">
            <button onclick="window.history.back()" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition shadow-sm">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
            </button>

            <div class="flex items-center gap-2">
                <a href="{{ route('medical-reports.download', $report->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition shadow-sm">
                    <i data-lucide="download" class="w-4 h-4 text-primary"></i> Download
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-white bg-[#0a2540] hover:bg-slate-800 rounded-xl transition shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i> Print Report
                </button>
            </div>
        </div>

        <!-- Printable Document Sheet -->
        <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 shadow-sm print-sheet space-y-6">
            <!-- Header -->
            <div class="flex items-start justify-between border-b-2 border-slate-900 pb-5">
                <div>
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-[#0a2540] text-white flex items-center justify-center font-black text-xl">
                            +
                        </div>
                        <div>
                            <h1 class="text-xl font-black text-slate-900 tracking-tight">CAREPOINT CLINIC & HEALTHCARE</h1>
                            <p class="text-xs text-slate-500 font-semibold">Diagnostic Pathology & Clinical Imaging Center</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2">Laboratory: <strong class="text-slate-700">{{ $report->laboratory ?? 'CarePoint Diagnostic Center' }}</strong></p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 text-[11px] font-bold rounded-lg uppercase tracking-wider">
                        {{ $report->report_type }}
                    </span>
                    <p class="text-xs font-mono font-bold text-slate-700 mt-2">{{ $report->report_no }}</p>
                    <p class="text-[11px] text-slate-400">Date: {{ $report->report_date->format('d M Y') }}</p>
                </div>
            </div>

            <!-- Patient Demographic Strip -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div>
                    <span class="text-slate-400 block font-semibold">Patient Name</span>
                    <strong class="text-slate-900">{{ $report->patient->full_name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Patient ID</span>
                    <span class="font-mono font-bold text-slate-800">{{ $report->patient->patient_id }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Age / Gender</span>
                    <span class="text-slate-800">{{ $report->patient->age }}y / {{ $report->patient->gender }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Blood Group</span>
                    <strong class="text-rose-600">{{ $report->patient->blood_group ?? 'N/A' }}</strong>
                </div>
            </div>

            <!-- Test Name & Findings -->
            <div class="space-y-4">
                <div class="border-b border-slate-100 pb-2">
                    <h2 class="text-base font-extrabold text-slate-900">{{ $report->report_name }}</h2>
                    <span class="text-xs text-slate-400">Investigation / Diagnostic Panel</span>
                </div>

                <div class="space-y-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Findings & Clinical Interpretation</span>
                    <div class="bg-slate-50/70 rounded-xl p-4 border border-slate-100 text-xs sm:text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                        {{ $report->description ?? 'Standard laboratory examination conducted. Parameters fall within clinical reference ranges.' }}
                    </div>
                </div>

                @if($report->doctor_notes)
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block">Doctor's Clinical Impression</span>
                        <div class="bg-blue-50/50 rounded-xl p-4 border border-blue-100 text-xs sm:text-sm text-blue-950 leading-relaxed">
                            {{ $report->doctor_notes }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Attachment File Notice (If attached) -->
            @if($report->file_path)
                <div class="p-4 rounded-xl border border-dashed border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-3">
                        <i data-lucide="paperclip" class="w-5 h-5 text-slate-400"></i>
                        <div>
                            <span class="font-bold text-slate-800 block">Original Document Attached</span>
                            <span class="text-slate-400 text-[11px]">{{ $report->file_size ?? 'Document' }}</span>
                        </div>
                    </div>
                    <a href="{{ route('medical-reports.download', $report->id) }}" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 font-bold hover:bg-slate-50 transition shadow-xs flex items-center gap-1.5">
                        <i data-lucide="download" class="w-3.5 h-3.5 text-primary"></i> Download Attachment
                    </a>
                </div>
            @endif

            <!-- Sign Off -->
            <div class="pt-8 border-t border-slate-200 flex items-center justify-between text-xs">
                <div class="text-slate-400">
                    <p>Verified Electronically by Pathologist / Radiologist</p>
                    <p class="text-[10px]">CarePoint Electronic Medical Records</p>
                </div>
                <div class="text-right">
                    <div class="w-36 border-b border-slate-300 mb-1"></div>
                    <p class="font-bold text-slate-800">Authorized Medical Signatory</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
