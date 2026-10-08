<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Medical Summary - {{ $patient->patient_id }} - {{ $patient->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
            background: #f8fafc;
        }
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-page {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="p-6 md:p-10 text-slate-800">
    <!-- Non-print Action Bar -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-4 py-2 rounded-xl shadow-sm transition">
            ← Back to Patient Profile
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 px-5 py-2 rounded-xl shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Print Health Summary
        </button>
    </div>

    <!-- Document Sheet -->
    <div class="max-w-4xl mx-auto bg-white rounded-2xl border border-slate-200 p-8 md:p-12 shadow-sm print-page space-y-8">
        <!-- Clinic Letterhead -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xl">
                        +
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">CAREPOINT CLINIC & HEALTHCARE</h1>
                        <p class="text-xs text-slate-500 font-semibold">Multi-Specialty Outpatient & Family Medicine Care</p>
                    </div>
                </div>
                <div class="text-xs text-slate-500 mt-3 space-y-0.5">
                    <p>Suite 401, MediCentre Plaza, Ring Road, Mumbai, MH - 400001</p>
                    <p>Phone: +91 98200 12345 • Email: clinic@carepoint.com • Web: www.carepoint.com</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 text-xs font-bold rounded-lg uppercase tracking-wider">
                    Medical Summary Dossier
                </span>
                <p class="text-xs text-slate-400 mt-2">Generated on: {{ date('F d, Y - h:i A') }}</p>
            </div>
        </div>

        <!-- Patient Demographics Grid -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-100">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Patient Profile & Identification</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block font-semibold">Patient Name</span>
                    <strong class="text-sm font-bold text-slate-900">{{ $patient->full_name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Patient ID</span>
                    <span class="font-mono font-bold text-slate-800">{{ $patient->patient_id }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Age / Gender</span>
                    <strong class="text-slate-800">{{ $patient->age }} Years / {{ $patient->gender }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Blood Group</span>
                    <strong class="text-rose-600 font-bold">{{ $patient->blood_group ?? 'Unknown' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Mobile Contact</span>
                    <span class="text-slate-800">{{ $patient->mobile }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-semibold">Emergency Contact</span>
                    <span class="text-slate-800">{{ $patient->emergency_contact ?? 'N/A' }} ({{ $patient->emergency_contact_phone ?? 'N/A' }})</span>
                </div>
                <div class="md:col-span-2">
                    <span class="text-slate-400 block font-semibold">Residential Address</span>
                    <span class="text-slate-800">{{ $patient->address ?? 'N/A' }}{{ $patient->city ? ', ' . $patient->city : '' }}</span>
                </div>
            </div>
        </div>

        <!-- Medical History & Background -->
        @if($patient->medicalHistory)
            <div class="space-y-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">Medical History & Risk Factors</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div class="bg-rose-50/50 border border-rose-100 rounded-xl p-3.5">
                        <span class="font-bold text-rose-800 block mb-1">Known Allergies:</span>
                        <p class="text-rose-900">{{ $patient->medicalHistory->allergies ?: 'No known drug or environmental allergies.' }}</p>
                    </div>
                    <div class="bg-amber-50/50 border border-amber-100 rounded-xl p-3.5">
                        <span class="font-bold text-amber-800 block mb-1">Pre-existing Conditions:</span>
                        <p class="text-amber-900">{{ $patient->medicalHistory->conditions ?: 'No chronic conditions recorded.' }}</p>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5">
                        <span class="font-bold text-slate-700 block mb-1">Current Regular Medications:</span>
                        <p class="text-slate-800">{{ $patient->medicalHistory->current_medications ?: 'None reported.' }}</p>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5">
                        <span class="font-bold text-slate-700 block mb-1">Past Surgeries / Hospitalizations:</span>
                        <p class="text-slate-800">{{ $patient->medicalHistory->surgeries ?: 'None recorded.' }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Clinical Visits -->
        <div class="space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
                Clinical Visit History (Total Visits: {{ $patient->visits->count() }})
            </h2>

            @if($patient->visits->count() > 0)
                <div class="space-y-4">
                    @foreach($patient->visits->sortByDesc('visit_date')->take(5) as $v)
                        <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/40 text-xs space-y-2">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                <div>
                                    <span class="font-mono font-bold text-slate-800">Visit #{{ $v->visit_no }}</span>
                                    <span class="text-slate-400 mx-2">•</span>
                                    <span class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($v->visit_date)->format('M d, Y') }}</span>
                                    <span class="text-slate-400 mx-2">•</span>
                                    <span class="text-slate-600">Type: {{ $v->visit_type }}</span>
                                </div>
                                @php $vit = $v->vitals_json ?? []; @endphp
                                @if(!empty($vit['bp_sys']))
                                    <span class="font-mono text-slate-600 bg-white px-2 py-0.5 rounded border border-slate-200">
                                        BP: {{ $vit['bp_sys'] }}/{{ $vit['bp_dia'] }} mmHg | Pulse: {{ $vit['pulse'] ?? '-' }} bpm
                                    </span>
                                @endif
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <span class="text-slate-400 block font-semibold">Chief Complaint:</span>
                                    <p class="font-semibold text-slate-800">{{ $v->chief_complaint }}</p>
                                </div>
                                <div>
                                    <span class="text-slate-400 block font-semibold">Doctor Diagnosis:</span>
                                    <p class="font-semibold text-slate-800">{{ $v->diagnosis_summary ?: 'Clinical evaluation' }}</p>
                                </div>
                            </div>
                            @if($v->treatment_plan)
                                <div>
                                    <span class="text-slate-400 block font-semibold">Treatment & Directions:</span>
                                    <p class="text-slate-700">{{ $v->treatment_plan }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 italic">No previous clinical encounters recorded.</p>
            @endif
        </div>

        <!-- Prescribed Medications Summary -->
        @if($patient->prescriptions && $patient->prescriptions->count() > 0)
            <div class="space-y-3">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100 pb-2">
                    Latest Prescription Details (#{{ $patient->prescriptions->first()->prescription_no }} - {{ \Carbon\Carbon::parse($patient->prescriptions->first()->prescription_date)->format('M d, Y') }})
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border border-slate-100 rounded-xl overflow-hidden">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase">
                            <tr>
                                <th class="p-2.5">Medicine</th>
                                <th class="p-2.5">Dosage</th>
                                <th class="p-2.5">Schedule</th>
                                <th class="p-2.5">Duration</th>
                                <th class="p-2.5">Timing</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($patient->prescriptions->first()->items as $item)
                                <tr>
                                    <td class="p-2.5 font-bold text-slate-800">{{ $item->medicine_name }}</td>
                                    <td class="p-2.5 text-slate-600">{{ $item->dosage ?: '-' }}</td>
                                    <td class="p-2.5 font-mono text-slate-700">{{ $item->frequency }}</td>
                                    <td class="p-2.5 text-slate-600">{{ $item->duration }}</td>
                                    <td class="p-2.5 text-slate-600">{{ $item->timing }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Account Balance Summary -->
        <div class="flex items-center justify-between border-t border-slate-200 pt-6">
            <div class="text-xs text-slate-500">
                <p>Total Billing Invoices: {{ $patient->invoices->count() }}</p>
                <p>Outstanding Balance: <strong class="{{ $patient->outstanding_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">₹{{ number_format($patient->outstanding_balance, 2) }}</strong></p>
            </div>
            <!-- Doctor Signature Block -->
            <div class="text-right">
                <div class="w-48 border-b border-slate-300 mb-2"></div>
                <p class="text-xs font-bold text-slate-900">Dr. Rajesh Sharma, MD</p>
                <p class="text-[10px] text-slate-400">Chief Medical Officer (Reg #MCI-2012-98432)</p>
            </div>
        </div>
    </div>
</body>
</html>
