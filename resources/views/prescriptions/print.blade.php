<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription #{{ $prescription->prescription_no }} - {{ $prescription->patient->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .prescription-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="p-6 md:p-12 text-slate-900">

    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('prescriptions.show', $prescription->id) }}" class="text-xs font-semibold text-blue-700 hover:underline">
            ← Back to Prescription Details
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Prescription</span>
            </button>
        </div>
    </div>

    <!-- PRESCRIPTION DOCUMENT -->
    <div class="prescription-card max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-2xl border border-slate-200 shadow-lg space-y-6">
        
        <!-- HEADER -->
        <div class="border-b-2 border-slate-900 pb-6 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $clinic->name }}</h1>
                <p class="text-xs text-blue-700 font-semibold uppercase tracking-wider">{{ $clinic->tagline }}</p>
                <div class="text-xs text-slate-500 mt-2 space-y-0.5">
                    <p>{{ $clinic->address }}, {{ $clinic->city }}, {{ $clinic->state }} - {{ $clinic->pincode }}</p>
                    <p>Phone: {{ $clinic->phone }} • Email: {{ $clinic->email }}</p>
                </div>
            </div>

            <div class="text-right">
                <h3 class="text-base font-extrabold text-slate-900">{{ $prescription->doctor->name ?? $clinic->doctor_name }}</h3>
                <p class="text-xs text-slate-600 font-semibold">{{ $prescription->doctor->qualification ?? 'MBBS, MD' }}</p>
                <p class="text-xs text-slate-600">{{ $prescription->doctor->specialization ?? 'Senior Consultant Physician' }}</p>
                <span class="inline-block mt-1 text-[11px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                    Reg No: {{ $prescription->doctor->registration_no ?? $clinic->doctor_reg_no }}
                </span>
            </div>
        </div>

        <!-- PATIENT DEMOGRAPHICS BAR -->
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Patient Name:</span>
                <strong class="text-slate-900 font-bold text-sm">{{ $prescription->patient->full_name }}</strong>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Patient ID:</span>
                <span class="font-mono font-bold text-blue-700">{{ $prescription->patient->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Age / Gender:</span>
                <span class="font-semibold text-slate-800">{{ $prescription->patient->age }} Y / {{ $prescription->patient->gender }}</span>
                @if($prescription->patient->blood_group)
                    <span class="font-bold text-rose-700">({{ $prescription->patient->blood_group }})</span>
                @endif
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Date:</span>
                <span class="font-semibold text-slate-800">{{ $prescription->prescription_date->format('d M Y') }}</span>
            </div>
        </div>

        <!-- CLINICAL DIAGNOSIS -->
        <div class="space-y-1 text-xs">
            <span class="font-bold uppercase tracking-wider text-slate-400 text-[10px]">Diagnosis / Clinical Findings:</span>
            <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100 text-blue-950 font-bold text-sm">
                {{ $prescription->diagnosis_summary }}
            </div>
        </div>

        <!-- Rx SYMBOL & MEDICINES TABLE -->
        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-serif font-black text-blue-900 italic">℞</span>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Prescribed Medications</span>
            </div>

            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="border-b-2 border-slate-200 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-2">#</th>
                        <th class="py-2">Medicine Details</th>
                        <th class="py-2">Dosage</th>
                        <th class="py-2">Frequency</th>
                        <th class="py-2">Duration</th>
                        <th class="py-2">Timing & Instructions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($prescription->items as $index => $item)
                        <tr>
                            <td class="py-3 text-slate-400 font-bold">{{ $index + 1 }}</td>
                            <td class="py-3 font-bold text-slate-900">
                                {{ $item->medicine_name }}
                                <span class="text-[10px] text-slate-400 font-normal block">{{ $item->route ?? 'Oral' }}</span>
                            </td>
                            <td class="py-3 text-slate-600">{{ $item->dosage }}</td>
                            <td class="py-3 font-bold text-blue-800">{{ $item->frequency }}</td>
                            <td class="py-3 text-slate-700">{{ $item->duration }}</td>
                            <td class="py-3 text-slate-700">
                                <span class="font-semibold">{{ $item->timing }}</span>
                                @if($item->instructions)
                                    <span class="text-slate-500 block text-[11px]">{{ $item->instructions }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ADVICE & NEXT FOLLOW-UP -->
        <div class="pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            @if($prescription->advice)
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <strong class="text-slate-800 block mb-1">Dietary & General Advice:</strong>
                    <p class="text-slate-600">{{ $prescription->advice }}</p>
                </div>
            @endif

            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex flex-col justify-between">
                <div>
                    <strong class="text-slate-800 block mb-1">Next Review / Follow-up:</strong>
                    <span class="font-extrabold text-blue-800 text-sm">
                        {{ $prescription->follow_up_date ? $prescription->follow_up_date->format('d M Y') : 'As needed / SOS' }}
                    </span>
                </div>
                <p class="text-[10px] text-slate-400 mt-2">Please bring this prescription and lab reports during revisit.</p>
            </div>
        </div>

        <!-- FOOTER & DOCTOR SIGNATURE -->
        <div class="pt-12 flex items-end justify-between text-xs">
            <div class="text-[10px] text-slate-400 max-w-sm">
                <p>Prescription generated electronically by CarePoint Clinic System.</p>
                <p>Not valid for medico-legal purposes.</p>
            </div>

            <div class="text-center min-w-[200px]">
                <div class="h-10 border-b border-slate-400 mb-1 flex items-end justify-center">
                    <span class="font-serif italic text-blue-900 font-bold text-sm">{{ $prescription->doctor->name ?? $clinic->doctor_name }}</span>
                </div>
                <span class="font-bold text-slate-800 block">{{ $prescription->doctor->name ?? $clinic->doctor_name }}</span>
                <span class="text-[10px] text-slate-400">Doctor's Digital Signature</span>
            </div>
        </div>

    </div>

</body>
</html>
