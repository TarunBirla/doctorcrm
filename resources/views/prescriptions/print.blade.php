<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst($prescription->assessment_type) }} Assessment - {{ $prescription->prescription_no }} - {{ $prescription->patient->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9; 
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .page-sheet { 
                box-shadow: none !important; 
                border: none !important; 
                margin: 0 !important; 
                width: 100% !important; 
                max-width: 100% !important; 
                padding: 24px !important;
            }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="py-8 px-4 text-slate-900 antialiased">

    <!-- TOP ACTION TOOLBAR -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('prescriptions.show', $prescription->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-blue-800 hover:text-blue-900 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Back to Details</span>
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-6 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-black shadow-md flex items-center gap-2 transition cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Print Official Chart & Prescription</span>
            </button>
        </div>
    </div>

    <!-- DOCUMENT CONTAINER -->
    <div class="page-sheet max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-2xl border border-slate-200 shadow-xl space-y-6">

        @php
            $data = $prescription->assessment_data ?? [];
            $prescribedClinic = $prescription->clinic ?? $prescription->patient?->clinic ?? $clinic;
            $docName = $prescription->doctor->name ?? $prescribedClinic->doctor_name ?? 'Dr. Mahesh Sahu PT';
            $docQual = $prescription->doctor->qualification ?? 'M.P.T Neuro, FOMT Australia, CHIROPRACTIC Sweden';
            $docReg = $prescription->doctor->registration_no ?? $prescribedClinic->doctor_reg_no ?? 'M.I.A.P. L-40612, MPPC-32862';
            $isNeuro = ($prescription->assessment_type === 'neurological');
        @endphp

        <!-- ============================================================== -->
        <!-- CLINICAL LETTERHEAD HEADER (MATCHING PHYSICAL CLINIC CHART)    -->
        <!-- ============================================================== -->
        <div class="border-b-2 border-slate-900 pb-5">
            <div class="flex items-start justify-between gap-4">
                
                <!-- Left: Logo & Clinic Title -->
                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-xl bg-blue-900 text-white flex items-center justify-center font-black text-lg tracking-tighter shadow-sm border border-blue-950">
                            SD
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 tracking-tight leading-none uppercase">
                                {{ $prescribedClinic->name }}
                            </h1>
                            <div class="flex items-center gap-1.5 mt-1 text-[11px] font-black tracking-wider uppercase">
                                <span class="bg-blue-900 text-white px-2 py-0.5 rounded text-[10px]">PHYSIOTHERAPY</span>
                                <span class="bg-blue-800 text-white px-2 py-0.5 rounded text-[10px]">OSTEOPATHY</span>
                                <span class="bg-indigo-900 text-white px-2 py-0.5 rounded text-[10px]">CHIROPRACTIC</span>
                            </div>
                        </div>
                    </div>

                    <!-- Branches Display with Active Branch Highlight -->
                    <div class="pt-2 text-[11px] text-slate-600 space-y-1">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span class="inline-flex items-center gap-1 font-bold text-slate-900 bg-amber-100 text-amber-900 px-2 py-0.5 rounded border border-amber-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                Registered Branch: {{ $prescribedClinic->name }}
                            </span>
                        </div>
                        <p class="text-slate-600">
                            <strong>Address:</strong> {{ $prescribedClinic->address }}, {{ $prescribedClinic->city }} - {{ $prescribedClinic->pincode ?? '452014' }} • 
                            <strong>Ph:</strong> {{ $prescribedClinic->phone }}
                        </p>
                        <p class="text-slate-500 text-[10px]">
                            <strong>Email:</strong> {{ $prescribedClinic->email }} • 
                            <strong>Web:</strong> {{ $prescribedClinic->website ?? 'www.sdpcindore.com' }}
                        </p>
                    </div>
                </div>

                <!-- Right: Spine Specialist Badges & Doctor Credentials -->
                <div class="text-right flex flex-col items-end justify-between min-w-[240px]">
                    <!-- Badges -->
                    <div class="text-right text-[11px] font-black text-blue-900 leading-snug space-y-0.5 border-r-4 border-blue-900 pr-2">
                        <p>• Drug Free</p>
                        <p>• Surgery Free</p>
                        <p>• Pain Free</p>
                        <p class="text-xs font-black uppercase text-rose-700 tracking-wide">Spine Specialist</p>
                    </div>

                    <!-- Doctor Info -->
                    <div class="mt-3 text-right">
                        <h2 class="text-sm font-black text-slate-900 leading-tight">{{ $docName }}</h2>
                        <p class="text-[10px] text-slate-600 font-bold leading-tight">{{ $docQual }}</p>
                        <span class="inline-block mt-0.5 text-[9px] font-mono font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">
                            Reg: {{ $docReg }}
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ============================================================== -->
        <!-- CHART TITLE & PATIENT DEMOGRAPHICS BAR                         -->
        <!-- ============================================================== -->
        <div class="text-center py-1">
            <h2 class="text-base font-black tracking-wider uppercase underline underline-offset-4 {{ $isNeuro ? 'text-purple-900' : 'text-blue-900' }}">
                {{ $isNeuro ? 'Neurological Assessment Chart' : 'Musculo Skeletal Assessment Chart' }}
            </h2>
        </div>

        <!-- Patient Demographics Table (Exact Match to Physical Assessment Sheet) -->
        <div class="border border-slate-300 rounded-xl overflow-hidden text-xs">
            <div class="bg-slate-50 p-3 grid grid-cols-2 md:grid-cols-5 gap-3 border-b border-slate-200">
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">NAME (मरीज का नाम):</span>
                    <strong class="text-slate-900 font-bold text-sm">{{ $prescription->patient->full_name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">AGE / SEX (उम्र / लिंग):</span>
                    <span class="font-bold text-slate-800">{{ $prescription->patient->age }} Yrs / {{ $prescription->patient->gender }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">AFFECTED SIDE:</span>
                    <span class="font-black text-slate-900 bg-slate-200/80 px-2 py-0.5 rounded text-[11px] inline-block">
                        {{ $data['affected_side'] ?? 'Right (R)' }}
                    </span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">OCCUPATION (व्यवसाय):</span>
                    <span class="font-semibold text-slate-800">{{ $data['occupation'] ?? $prescription->patient->occupation ?? 'General' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">DATE (दिनांक):</span>
                    <span class="font-bold text-slate-900 font-mono">{{ $prescription->prescription_date->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="p-2.5 bg-white flex flex-wrap items-center justify-between text-[11px] gap-2">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Address:</span>
                    <span class="text-slate-700 font-medium">{{ $prescription->patient->address ?? '-' }}, {{ $prescription->patient->city }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Patient ID:</span>
                        <span class="font-mono font-bold text-blue-800">{{ $prescription->patient->patient_id }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Rx No:</span>
                        <span class="font-mono font-bold text-slate-700">{{ $prescription->prescription_no }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CLINICAL DIAGNOSIS -->
        <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-200 text-xs flex items-start gap-2">
            <span class="font-black uppercase tracking-wider text-blue-900 text-[10px] whitespace-nowrap mt-0.5">Diagnosis:</span>
            <span class="text-blue-950 font-black text-sm">{{ $prescription->diagnosis_summary }}</span>
        </div>

        <!-- ============================================================== -->
        <!-- ASSESSMENT DETAILS (TYPE 1: MUSCULOSKELETAL)                   -->
        <!-- ============================================================== -->
        @if(!$isNeuro)
            <div class="space-y-3.5 text-xs">
                
                <!-- 1. CHIEF COMPLAINT, DURATION, PAIN AGGRAVATION, ASSOCIATE FACTORS, PAST HISTORY -->
                <div class="border border-slate-300 rounded-xl p-3.5 bg-slate-50/40 space-y-2.5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-2">
                            <strong class="text-slate-800 uppercase text-[10px] block">• CHIEF COMPLAINT:</strong>
                            <p class="font-bold text-slate-900 mt-0.5">{{ $data['chief_complaint'] ?? $data['chief_complaints'] ?? '-' }}</p>
                        </div>
                        <div>
                            <strong class="text-slate-800 uppercase text-[10px] block">• DURATION:</strong>
                            <span class="font-bold text-slate-900 mt-0.5 block">{{ $data['duration'] ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- PAIN AGGRAVATION + VAS SCALE -->
                    <div class="p-2.5 bg-amber-50/70 rounded-lg border border-amber-200 grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <strong class="text-amber-950 uppercase text-[10px] block">• PAIN aggravation - day / night / activities:</strong>
                            <div class="flex flex-wrap gap-1 mt-0.5">
                                @forelse((array)($data['pain_aggravation'] ?? []) as $pa)
                                    <span class="px-2 py-0.5 bg-amber-600 text-white rounded font-bold text-[10px]">✓ {{ $pa }}</span>
                                @empty
                                    <span class="text-slate-400">Not recorded</span>
                                @endforelse
                            </div>
                            @if(!empty($data['pain_aggravation_activities']))
                                <p class="text-amber-900 text-[10px] mt-1">{{ $data['pain_aggravation_activities'] }}</p>
                            @endif
                        </div>
                        <div>
                            <strong class="text-amber-950 uppercase text-[10px] block">+ VAS Scale (0 - 10):</strong>
                            @php
                                $vasScore = $data['vas_scale'] ?? (isset($data['vas_pain_score']) ? round($data['vas_pain_score']/7) : 4);
                            @endphp
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2.5 py-0.5 rounded bg-amber-700 text-white font-black text-xs">Score: {{ $vasScore }} / 10</span>
                                <span class="text-slate-600 text-[10px]">
                                    ({{ $vasScore <= 3 ? 'Mild' : ($vasScore <= 6 ? 'Moderate' : 'Severe') }})
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ASSOCIATE FACTORS & PAST HISTORY -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-[11px] pt-1">
                        <div>
                            <strong class="text-slate-800 uppercase text-[10px] block">• Associate factors:</strong>
                            <div class="flex flex-wrap gap-1 mt-0.5">
                                @forelse((array)($data['associate_factors'] ?? []) as $af)
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-900 rounded font-bold text-[10px]">⚡ {{ $af }}</span>
                                @empty
                                    <span class="text-slate-400">Nil</span>
                                @endforelse
                            </div>
                            @if(!empty($data['associate_factors_notes']))
                                <span class="text-slate-600 text-[10px] block mt-0.5">({{ $data['associate_factors_notes'] }})</span>
                            @endif
                        </div>
                        <div>
                            <strong class="text-slate-800 uppercase text-[10px] block">• PAST history:</strong>
                            <p class="text-slate-800 mt-0.5">{{ $data['past_history'] ?? 'None reported' }}</p>
                        </div>
                    </div>
                </div>

                <!-- 2. OBSERVATION & ADVICE & MMT -->
                <div class="border border-slate-300 rounded-xl p-3 bg-white space-y-2 text-[11px]">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• Posture:</strong>
                            <span class="font-semibold text-slate-900">{{ $data['observation_posture'] ?? $data['posture'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• Tenderness:</strong>
                            <span class="font-semibold text-slate-900">{{ $data['observation_tenderness'] ?? $data['tenderness'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• GAIT:</strong>
                            <span class="font-semibold text-slate-900">{{ $data['observation_gait'] ?? $data['gait'] ?? 'Normal' }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• Advice (X-ray, MRI):</strong>
                            <div class="flex flex-wrap gap-1 mt-0.5">
                                @forelse((array)($data['advice_imaging'] ?? []) as $adv)
                                    <span class="px-2 py-0.5 bg-slate-800 text-white rounded font-bold text-[10px]">{{ $adv }}</span>
                                @empty
                                    <span class="text-slate-400">None</span>
                                @endforelse
                            </div>
                            @if(!empty($data['advice_notes']))
                                <span class="text-slate-600 text-[10px] block mt-0.5">({{ $data['advice_notes'] }})</span>
                            @endif
                        </div>
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• MMT:</strong>
                            <span class="text-slate-900 font-bold">R: {{ $data['mmt_right'] ?? '-' }} | L: {{ $data['mmt_left'] ?? '-' }}</span>
                            @if(!empty($data['mmt_notes']))
                                <span class="text-slate-600 text-[10px] block">({{ $data['mmt_notes'] }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 3. RANGE OF MOTION (Flexion, Abduction, Extension, ER, IR) -->
                <div class="border border-slate-300 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px]">
                            <tr>
                                <th class="p-1.5 pl-3">• RANGE OF MOTION</th>
                                <th class="p-1.5 w-36 text-blue-900">Right (R)</th>
                                <th class="p-1.5 w-36 text-indigo-900">Left (L)</th>
                                <th class="p-1.5">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">Flexion</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_flexion_r'] ?? $data['rom_right'] ?? '-' }}</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_flexion_l'] ?? $data['rom_left'] ?? '-' }}</td>
                                <td class="p-1.5 text-slate-500">{{ $data['rom_flexion_notes'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">Abduction</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_abduction_r'] ?? '-' }}</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_abduction_l'] ?? '-' }}</td>
                                <td class="p-1.5 text-slate-500">{{ $data['rom_abduction_notes'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">Extension</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_extension_r'] ?? '-' }}</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_extension_l'] ?? '-' }}</td>
                                <td class="p-1.5 text-slate-500">{{ $data['rom_extension_notes'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">ER (External Rotation)</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_er_r'] ?? '-' }}</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_er_l'] ?? '-' }}</td>
                                <td class="p-1.5 text-slate-500">{{ $data['rom_er_notes'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">IR (Internal Rotation)</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_ir_r'] ?? '-' }}</td>
                                <td class="p-1.5 font-semibold">{{ $data['rom_ir_l'] ?? '-' }}</td>
                                <td class="p-1.5 text-slate-500">{{ $data['rom_ir_notes'] ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- 4. SPECIAL TEST (Image 1) -->
                <div class="border border-slate-300 rounded-xl p-3 bg-slate-50/30 text-[11px] space-y-2">
                    <strong class="text-slate-900 uppercase text-[10px] block">• SPECIAL TEST:</strong>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                        <div class="p-2 bg-white rounded border border-slate-200">
                            <span class="font-bold text-slate-700 block text-[10px] uppercase">Shoulder:</span>
                            <div>Drop arm: <strong>{{ $data['st_drop_arm'] ?? '-ve' }}</strong></div>
                            <div>Impingement: <strong>{{ $data['st_impingement'] ?? '-ve' }}</strong></div>
                        </div>
                        <div class="p-2 bg-white rounded border border-slate-200">
                            <span class="font-bold text-slate-700 block text-[10px] uppercase">Knee:</span>
                            <div>Drawer ant.: <strong>{{ $data['st_drawer_ant'] ?? '-ve' }}</strong></div>
                            <div>McMurray: <strong>{{ $data['st_mcmurray'] ?? '-ve' }}</strong></div>
                        </div>
                        <div class="p-2 bg-white rounded border border-slate-200">
                            <span class="font-bold text-slate-700 block text-[10px] uppercase">Hip:</span>
                            <div>FABER: <strong>{{ $data['st_faber'] ?? '-ve' }}</strong></div>
                            <div>Trendelenburg: <strong>{{ $data['st_trendelenburg'] ?? '-ve' }}</strong></div>
                        </div>
                        <div class="p-2 bg-white rounded border border-slate-200">
                            <span class="font-bold text-slate-700 block text-[10px] uppercase">Spine:</span>
                            <div>SLR: <strong>{{ $data['st_slr'] ?? '-ve' }}</strong></div>
                            <div>Slump: <strong>{{ $data['st_slump'] ?? '-ve' }}</strong></div>
                        </div>
                        <div class="p-2 bg-white rounded border border-slate-200">
                            <span class="font-bold text-slate-700 block text-[10px] uppercase">Cervical:</span>
                            <div>Compression: <strong>{{ $data['st_cervical_compression'] ?? '-ve' }}</strong></div>
                            <div>Spurling: <strong>{{ $data['st_spurling'] ?? '-ve' }}</strong></div>
                        </div>
                    </div>
                    @if(!empty($data['special_test_notes']))
                        <p class="text-slate-600 text-[10px]">Notes: {{ $data['special_test_notes'] }}</p>
                    @endif
                </div>

            </div>

        <!-- ============================================================== -->
        <!-- ASSESSMENT DETAILS (TYPE 2: NEUROLOGICAL)                      -->
        <!-- ============================================================== -->
        @else
            <div class="space-y-3.5 text-xs">
                
                <!-- 1. CHIEF COMPLAINT & FULL HISTORY (Image 2 - Page 1) -->
                <div class="border border-purple-300 rounded-xl p-3.5 bg-purple-50/30 space-y-2">
                    <div>
                        <strong class="text-purple-950 uppercase text-[10px] block">• Chief complain:</strong>
                        <p class="font-bold text-slate-900 mt-0.5">{{ $data['neuro_chief_complaint'] ?? $data['neuro_complaints'] ?? '-' }}</p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 pt-1 border-t border-purple-100 text-[11px]">
                        <div>
                            <strong class="text-purple-900 block text-[10px]">• past H/O:</strong>
                            <span class="text-slate-800">{{ $data['neuro_past_ho'] ?? $data['neuro_ho'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-purple-900 block text-[10px]">• Surgical H/O:</strong>
                            <span class="text-slate-800">{{ $data['neuro_surgical_ho'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-purple-900 block text-[10px]">• Family H/O:</strong>
                            <span class="text-slate-800">{{ $data['neuro_family_ho'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-purple-900 block text-[10px]">• associate factors:</strong>
                            <span class="text-slate-800">{{ $data['neuro_associate_factors'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2. OBSERVATION & EXTERNAL APPLIANCE (Image 2 - Page 1) -->
                <div class="border border-slate-300 rounded-xl p-3 bg-white space-y-2 text-[11px]">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• posture:</strong>
                            <span class="font-semibold text-slate-900">{{ $data['neuro_posture'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• Gait:</strong>
                            <span class="font-semibold text-slate-900">{{ $data['neuro_gait'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• Deformity:</strong>
                            <span class="font-semibold text-slate-900">{{ $data['neuro_deformity'] ?? '-' }}</span>
                        </div>
                    </div>

                    <div class="p-2 bg-slate-50 rounded-lg border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-2 pt-1">
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• functional aids (walking aids / catheter):</strong>
                            <span class="text-slate-900 font-semibold">{{ implode(', ', (array)($data['neuro_functional_aids'] ?? [])) ?: 'None' }}</span>
                            @if(!empty($data['neuro_functional_aids_notes']))
                                <span class="text-slate-500 text-[10px]"> ({{ $data['neuro_functional_aids_notes'] }})</span>
                            @endif
                        </div>
                        <div>
                            <strong class="text-slate-700 uppercase text-[10px] block">• protective aids (brace / prosthetics):</strong>
                            <span class="text-slate-900 font-semibold">{{ implode(', ', (array)($data['neuro_protective_aids'] ?? [])) ?: 'None' }}</span>
                            @if(!empty($data['neuro_protective_aids_notes']))
                                <span class="text-slate-500 text-[10px]"> ({{ $data['neuro_protective_aids_notes'] }})</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- 3. EXAMINATION: CONSCIOUSNESS & HIGHER FUNCTIONS & SPECIAL SENSES (Image 2) -->
                <div class="border border-purple-300 rounded-xl p-3 bg-purple-50/20 space-y-2 text-[11px]">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <div>
                            <strong class="text-purple-950 uppercase text-[10px] block">• conciousness level:</strong>
                            <span class="px-2 py-0.5 rounded bg-purple-800 text-white font-bold text-[10px]">
                                {{ $data['neuro_consciousness_level'] ?? $data['level_of_consciousness'] ?? 'Alert' }}
                            </span>
                        </div>
                        <div>
                            <strong class="text-purple-950 uppercase text-[10px] block">• Orientation:</strong>
                            <span class="text-slate-800 font-semibold">{{ $data['neuro_orientation'] ?? 'Intact' }}</span>
                        </div>
                        <div>
                            <strong class="text-purple-950 uppercase text-[10px] block">• Behaviour:</strong>
                            <span class="text-slate-800 font-semibold">{{ $data['neuro_behaviour'] ?? 'Cooperative' }}</span>
                        </div>
                        <div>
                            <strong class="text-purple-950 uppercase text-[10px] block">• memory:</strong>
                            <span class="text-slate-800 font-semibold">{{ $data['neuro_memory'] ?? 'Intact' }}</span>
                        </div>
                    </div>

                    <!-- special sense -->
                    <div class="p-2 bg-white rounded-lg border border-purple-100 text-[10px]">
                        <strong class="text-purple-900 uppercase block mb-1">• special sense:</strong>
                        <div class="grid grid-cols-5 gap-1.5 text-center">
                            <div class="bg-slate-50 p-1 rounded">Vision: <strong>{{ $data['neuro_sense_vision'] ?? 'Normal' }}</strong></div>
                            <div class="bg-slate-50 p-1 rounded">Hearing: <strong>{{ $data['neuro_sense_hearing'] ?? 'Normal' }}</strong></div>
                            <div class="bg-slate-50 p-1 rounded">Smell: <strong>{{ $data['neuro_sense_smell'] ?? 'Normal' }}</strong></div>
                            <div class="bg-slate-50 p-1 rounded">Taste: <strong>{{ $data['neuro_sense_taste'] ?? 'Normal' }}</strong></div>
                            <div class="bg-slate-50 p-1 rounded">Tactile: <strong>{{ $data['neuro_sense_tactile'] ?? 'Normal' }}</strong></div>
                        </div>
                    </div>
                </div>

                <!-- 4. SENSORY, MOTOR, REFLEX & CORTICAL (Image 3 - Page 2) -->
                <div class="border border-slate-300 rounded-xl p-3 bg-white space-y-2 text-[11px]">
                    <!-- Sensory -->
                    <div>
                        <strong class="text-slate-800 uppercase text-[10px] block">• Sensory examination:</strong>
                        <p class="text-slate-800 mt-0.5">{{ $data['neuro_sensory_exam'] ?? $data['sensory_deficit'] ?? 'Superficial & deep sensations intact' }}</p>
                    </div>

                    <!-- Motor (ROM, MMT, TONE) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pt-1 border-t border-slate-100">
                        <div>
                            <strong class="text-slate-700 text-[10px] block">• Motor - ROM:</strong>
                            <span class="font-semibold">{{ $data['neuro_motor_rom'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-700 text-[10px] block">• Motor - MMT:</strong>
                            <span class="font-semibold">{{ $data['neuro_motor_mmt'] ?? $data['neuro_mmt'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-700 text-[10px] block">• Motor - TONE:</strong>
                            <span class="font-semibold">{{ $data['neuro_motor_tone'] ?? $data['neuro_tone'] ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Reflex (DTR & Pattern) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1 border-t border-slate-100">
                        <div>
                            <strong class="text-purple-900 text-[10px] block">• Reflex - DTR:</strong>
                            <span class="font-semibold text-slate-800">{{ $data['neuro_reflex_dtr'] ?? $data['deep_tendon_reflexes'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-purple-900 text-[10px] block">• pattern:</strong>
                            <span class="font-semibold text-slate-800">{{ implode(', ', (array)($data['neuro_synergic_pattern'] ?? [])) ?: 'None' }}</span>
                        </div>
                    </div>

                    <!-- Cortical Level Reflex -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pt-1 border-t border-slate-100">
                        <div>
                            <strong class="text-slate-700 text-[10px] block">• Cortical - Balance:</strong>
                            <span class="font-semibold">{{ $data['neuro_cortical_balance'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-700 text-[10px] block">• Cortical - Equilibrium:</strong>
                            <span class="font-semibold">{{ $data['neuro_cortical_equilibrium'] ?? '-' }}</span>
                        </div>
                        <div>
                            <strong class="text-slate-700 text-[10px] block">• Cortical - Coordination:</strong>
                            <span class="font-semibold">{{ $data['neuro_cortical_coordination'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 5. INVESTIGATION & TREATMENT PLAN (Image 3 - Page 2) -->
                <div class="border border-purple-300 rounded-xl p-3 bg-purple-50/20 space-y-2 text-[11px]">
                    <div>
                        <strong class="text-purple-950 uppercase text-[10px] block">• Investigation (CT, MRI, EMG, NCV):</strong>
                        <div class="flex flex-wrap gap-1.5 mt-0.5">
                            @forelse((array)($data['neuro_investigation'] ?? $data['investigations'] ?? []) as $inv)
                                <span class="px-2 py-0.5 bg-indigo-700 text-white rounded font-bold text-[10px]">{{ $inv }}</span>
                            @empty
                                <span class="text-slate-400">None indicated</span>
                            @endforelse
                        </div>
                        @if(!empty($data['neuro_investigation_notes']) || !empty($data['investigation_notes']))
                            <p class="text-slate-700 text-[10px] mt-1">{{ $data['neuro_investigation_notes'] ?? $data['investigation_notes'] }}</p>
                        @endif
                    </div>

                    <div class="pt-1 border-t border-purple-100">
                        <strong class="text-slate-900 uppercase text-[10px] block">• Treatment plan (Image 3):</strong>
                        <p class="text-slate-800 font-semibold mt-0.5">
                            {{ $data['neuro_treatment_plan'] ?? $prescription->treatment_plan ?? 'Physiotherapy Neuro-Rehabilitation Protocol' }}
                        </p>
                    </div>
                </div>

            </div>
        @endif

        <!-- ============================================================== -->
        <!-- PRESCRIBED PHYSIOTHERAPY REHABILITATION & EXERCISES            -->
        <!-- ============================================================== -->
        <div class="pt-2 space-y-3">
            <div class="flex items-center justify-between border-b-2 border-slate-900 pb-1">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                    <h3 class="text-xs font-black uppercase tracking-wider text-slate-900">
                        Prescribed Physiotherapy Rehabilitation Protocol
                    </h3>
                </div>
                @if($prescription->treatment_days)
                    <span class="font-bold text-[11px] bg-emerald-100 text-emerald-900 px-2 py-0.5 rounded border border-emerald-300">
                        Treatment Duration: {{ $prescription->treatment_days }} Days
                    </span>
                @endif
            </div>

            <!-- Modalities Badges -->
            @if($prescription->modalities)
                <div class="p-2.5 bg-emerald-50/40 rounded-xl border border-emerald-200 text-xs">
                    <strong class="text-emerald-950 font-bold block text-[10px] uppercase mb-1">Prescribed Modalities / Electrotherapy:</strong>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(explode(',', $prescription->modalities) as $mod)
                            <span class="px-2 py-0.5 bg-white text-emerald-800 rounded-md border border-emerald-200 text-[11px] font-bold shadow-2xs">
                                ⚡ {{ trim($mod) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Exercises Table -->
            @if(!empty($prescription->prescribed_exercises) && count($prescription->prescribed_exercises) > 0)
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-2 pl-3">#</th>
                                <th class="p-2">Exercise Name</th>
                                <th class="p-2">Target Area</th>
                                <th class="p-2">Sets</th>
                                <th class="p-2">Reps</th>
                                <th class="p-2">Duration / Hold</th>
                                <th class="p-2">Instructions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($prescription->prescribed_exercises as $idx => $ex)
                                <tr>
                                    <td class="p-2 pl-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                    <td class="p-2 font-bold text-slate-900">{{ $ex['name'] ?? 'Exercise' }}</td>
                                    <td class="p-2 text-slate-600">{{ $ex['target'] ?? '-' }}</td>
                                    <td class="p-2 font-bold text-emerald-800">{{ $ex['sets'] ?? '-' }}</td>
                                    <td class="p-2 font-bold text-emerald-800">{{ $ex['reps'] ?? '-' }}</td>
                                    <td class="p-2 text-slate-700">{{ $ex['duration'] ?? '-' }}</td>
                                    <td class="p-2 text-slate-600 text-[11px]">{{ $ex['instructions'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- ============================================================== -->
        <!-- MEDICINES (Rx - OPTIONAL)                                      -->
        <!-- ============================================================== -->
        @if($prescription->items->count() > 0)
            <div class="space-y-2 pt-2">
                <div class="flex items-center gap-2 border-b border-slate-200 pb-1">
                    <span class="text-xl font-serif font-black text-blue-900 italic">℞</span>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Prescribed Medications / Supplements</span>
                </div>

                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead>
                        <tr class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase">
                            <th class="p-2 pl-3">#</th>
                            <th class="p-2">Medicine Details</th>
                            <th class="p-2">Dosage</th>
                            <th class="p-2">Frequency</th>
                            <th class="p-2">Duration</th>
                            <th class="p-2">Timing & Instructions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($prescription->items as $idx => $item)
                            <tr>
                                <td class="p-2 pl-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                <td class="p-2 font-bold text-slate-900">{{ $item->medicine_name }}</td>
                                <td class="p-2 text-slate-600">{{ $item->dosage }}</td>
                                <td class="p-2 font-bold text-blue-800">{{ $item->frequency }}</td>
                                <td class="p-2 text-slate-700">{{ $item->duration }}</td>
                                <td class="p-2 text-slate-600 text-[11px]">{{ $item->timing }} {{ $item->instructions ? '• ' . $item->instructions : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- ADVICE & NEXT FOLLOW-UP REVIEW                                  -->
        <!-- ============================================================== -->
        <div class="pt-2 border-t border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <strong class="text-slate-900 block mb-1 uppercase text-[10px]">Ergonomic Advice & Precautions:</strong>
                <p class="text-slate-700 leading-relaxed">{{ $prescription->advice ?? 'Perform prescribed exercises twice daily. Maintain ergonomic spine posture while sitting. Avoid heavy lifting.' }}</p>
            </div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col justify-between">
                <div>
                    <strong class="text-slate-900 block mb-1 uppercase text-[10px]">Next Scheduled Review:</strong>
                    <span class="font-extrabold text-blue-800 text-sm">
                        {{ $prescription->follow_up_date ? $prescription->follow_up_date->format('d M Y') : 'After completing treatment course / SOS' }}
                    </span>
                </div>
                <p class="text-[10px] text-slate-400 mt-2">Kindly bring this chart during every physiotherapy session.</p>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- FOOTER: CHANGED ADDRESS & DOCTOR DIGITAL SIGNATURE              -->
        <!-- ============================================================== -->
        <div class="pt-6 border-t-2 border-slate-900 flex flex-col sm:flex-row items-end justify-between gap-4 text-xs">
            
            <!-- Changed Address & Branch Contacts -->
            <div class="text-[10px] text-slate-600 space-y-1 max-w-md">
                <p class="font-bold text-slate-800">
                    <span class="bg-slate-800 text-white px-1.5 py-0.5 rounded text-[9px] mr-1">Changed Address</span>
                    <strong>Branch 1:</strong> 584-C, Khatiwala Tank, Indore, Web: www.sdpcindore.com
                </p>
                <p>
                    <strong>Branch 2:</strong> MR6-110, Mahalaxmi Nagar, Indore Ph. 0731-4979170
                </p>
                <p class="text-slate-400 text-[9px]">
                    Prescription generated electronically by CarePoint Clinic System. Valid across all branches.
                </p>
            </div>

            <!-- Doctor Digital Signature -->
            <div class="text-center min-w-[220px]">
                <div class="h-12 border-b border-slate-400 mb-1 flex items-end justify-center">
                    <span class="font-serif italic text-blue-900 font-black text-base">{{ $docName }}</span>
                </div>
                <strong class="text-slate-900 block text-xs">{{ $docName }}</strong>
                <span class="text-[10px] text-slate-500 font-semibold">{{ $docQual }}</span>
                <span class="text-[9px] text-slate-400 block">Doctor's Digital Signature & Stamp</span>
            </div>

        </div>

    </div>

</body>
</html>
