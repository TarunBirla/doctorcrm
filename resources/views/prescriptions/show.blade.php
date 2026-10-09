@extends('layouts.app')

@section('title', 'Prescription #' . $prescription->prescription_no)
@section('breadcrumb', 'Prescriptions / ' . $prescription->prescription_no)
@section('page_title', 'Clinical Assessment & Prescription')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto pb-12">

    @php
        $data = $prescription->assessment_data ?? [];
        $prescribedClinic = $prescription->clinic ?? $prescription->patient?->clinic ?? $clinic;
        $docName = $prescription->doctor->name ?? $prescribedClinic->doctor_name ?? 'Dr. Mahesh Sahu PT';
        $docQual = $prescription->doctor->qualification ?? 'M.P.T Neuro, FOMT Australia, CHIROPRACTIC Sweden';
        $docReg = $prescription->doctor->registration_no ?? $prescribedClinic->doctor_reg_no ?? 'M.I.A.P. L-40612, MPPC-32862';
        $isNeuro = ($prescription->assessment_type === 'neurological');
    @endphp

    <!-- ACTION BAR -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('prescriptions.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Prescriptions History</span>
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('prescriptions.print', $prescription->id) }}" target="_blank" class="px-5 py-2.5 bg-gradient-to-r from-blue-700 to-indigo-800 hover:from-blue-800 hover:to-indigo-900 text-white rounded-xl text-xs font-black transition flex items-center gap-2 shadow-md hover:shadow-lg">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Official Letterhead Chart</span>
            </a>
        </div>
    </div>

    <!-- MAIN CLINICAL CARD -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-8">
        
        <!-- CLINIC BRANDING HEADER -->
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 border-b-2 border-slate-900 pb-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-900 text-white flex items-center justify-center font-black text-xl shadow-sm">
                        SD
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 tracking-tight uppercase leading-none">{{ $prescribedClinic->name }}</h2>
                        <div class="flex items-center gap-1.5 mt-1.5 text-[10px] font-black uppercase tracking-wider">
                            <span class="bg-blue-900 text-white px-2 py-0.5 rounded">PHYSIOTHERAPY</span>
                            <span class="bg-blue-800 text-white px-2 py-0.5 rounded">OSTEOPATHY</span>
                            <span class="bg-indigo-900 text-white px-2 py-0.5 rounded">CHIROPRACTIC</span>
                        </div>
                    </div>
                </div>

                <div class="pt-2 text-xs text-slate-600 space-y-0.5">
                    <p class="font-bold text-blue-900 flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-rose-500"></i>
                        <span>Patient's Registered Branch: <strong>{{ $prescribedClinic->name }}</strong></span>
                    </p>
                    <p>{{ $prescribedClinic->address }}, {{ $prescribedClinic->city }} - {{ $prescribedClinic->pincode ?? '452014' }} • Ph: {{ $prescribedClinic->phone }}</p>
                    <p class="text-[11px] text-slate-500">Email: {{ $prescribedClinic->email }} • Web: {{ $prescribedClinic->website ?? 'www.sdpcindore.com' }}</p>
                </div>
            </div>

            <div class="text-left md:text-right flex flex-col md:items-end justify-between min-w-[240px]">
                <div class="space-y-1">
                    <span class="font-mono font-bold text-blue-800 text-sm block">{{ $prescription->prescription_no }}</span>
                    <span class="text-xs text-slate-500 block">Assessment Date: <strong>{{ $prescription->prescription_date->format('d M Y') }}</strong></span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-black uppercase px-2.5 py-0.5 rounded-full {{ $isNeuro ? 'bg-purple-100 text-purple-900 border border-purple-200' : 'bg-blue-100 text-blue-900 border border-blue-200' }}">
                        {{ $isNeuro ? '🧠 Neurological Chart' : '🦴 Musculoskeletal Chart' }}
                    </span>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <span class="text-xs font-black text-slate-900 block">{{ $docName }}</span>
                    <span class="text-[11px] text-slate-600 font-bold block">{{ $docQual }}</span>
                    <span class="text-[10px] font-mono text-slate-400">Reg: {{ $docReg }}</span>
                </div>
            </div>
        </div>

        <!-- PATIENT INFO CARD (MATCHING CLINICAL SHEETS) -->
        <div class="p-4 bg-slate-50/80 rounded-2xl grid grid-cols-2 md:grid-cols-5 gap-4 text-xs border border-slate-200">
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Patient Name:</span>
                <a href="{{ route('patients.show', $prescription->patient_id) }}" class="font-black text-slate-900 hover:text-blue-700 text-sm">
                    {{ $prescription->patient->full_name }}
                </a>
                <span class="font-mono text-blue-700 font-bold text-[10px] block mt-0.5">{{ $prescription->patient->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Age / Gender:</span>
                <span class="font-bold text-slate-800 text-xs">{{ $prescription->patient->age }} Yrs / {{ $prescription->patient->gender }}</span>
                @if($prescription->patient->blood_group)
                    <span class="text-rose-600 font-bold text-[10px] block">({{ $prescription->patient->blood_group }})</span>
                @endif
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Affected Side:</span>
                <span class="font-black text-blue-900 bg-blue-100/70 px-2.5 py-0.5 rounded border border-blue-200 inline-block mt-0.5">
                    {{ $data['affected_side'] ?? 'Right (R)' }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Occupation:</span>
                <span class="font-bold text-slate-700 block truncate mt-0.5">
                    {{ $data['occupation'] ?? $prescription->patient->occupation ?? 'General' }}
                </span>
            </div>
            <div class="col-span-2 md:col-span-1">
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Clinic Branch:</span>
                <span class="font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 block truncate mt-0.5">
                    {{ $prescribedClinic->name }}
                </span>
            </div>
        </div>

        <!-- DIAGNOSIS BANNER -->
        <div class="p-4 bg-blue-50/70 rounded-2xl border border-blue-200 flex items-start gap-3">
            <div class="p-2 rounded-xl bg-blue-600 text-white shadow-sm mt-0.5">
                <i data-lucide="activity" class="w-4 h-4"></i>
            </div>
            <div class="flex-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-900 block">Primary Clinical Physical Therapy Diagnosis</span>
                <p class="font-black text-slate-900 text-base mt-0.5">{{ $prescription->diagnosis_summary }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $isNeuro ? 'bg-purple-100 text-purple-900 border border-purple-200' : 'bg-blue-100 text-blue-900 border border-blue-200' }}">
                {{ $isNeuro ? '🧠 Neurological Protocol' : '🦴 Musculoskeletal Protocol' }}
            </span>
        </div>

        <!-- ============================================================== -->
        <!-- ASSESSMENT DETAILS ACCORDION/BLOCKS                            -->
        <!-- ============================================================== -->
        @if(!$isNeuro)
            <!-- Musculoskeletal Assessment Details (Matching Image 1) -->
            <div class="space-y-6 text-xs">
                
                <!-- Card 1: Complaints, Duration, Aggravation + VAS, Associate Factors, Past History -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h4 class="font-black text-blue-900 uppercase tracking-wider text-xs flex items-center gap-2">
                            <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-600"></i>
                            Chief Complaints & Pain Analysis (Image 1)
                        </h4>
                        <span class="text-[10px] font-bold text-slate-400">Duration: <strong>{{ $data['duration'] ?? '-' }}</strong></span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div>
                                <strong class="text-slate-800 text-[11px] block uppercase text-[10px] text-slate-400">Chief Complaint:</strong>
                                <p class="text-slate-800 font-semibold bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    {{ $data['chief_complaint'] ?? $data['chief_complaints'] ?? 'General pain and restricted movement' }}
                                </p>
                            </div>
                            <div>
                                <strong class="text-slate-800 text-[11px] block uppercase text-[10px] text-slate-400">Past History:</strong>
                                <p class="text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    {{ $data['past_history'] ?? 'No significant past medical/surgical trauma reported' }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <!-- Pain Aggravation -->
                            <div class="p-3 bg-amber-50/60 rounded-xl border border-amber-200 space-y-2">
                                <span class="text-amber-950 font-black uppercase text-[10px] block">Pain Aggravation (दर्द कब बढ़ता है):</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @php
                                        $aggravations = (array)($data['pain_aggravation'] ?? []);
                                    @endphp
                                    @forelse($aggravations as $agg)
                                        <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-white font-bold text-[10px] shadow-xs">
                                            ✓ {{ $agg }}
                                        </span>
                                    @empty
                                        <span class="text-slate-400 text-xs">Not specified</span>
                                    @endforelse
                                </div>
                                @if(!empty($data['pain_aggravation_activities']))
                                    <p class="text-[11px] text-amber-900 font-medium">Activities: {{ $data['pain_aggravation_activities'] }}</p>
                                @endif
                            </div>

                            <!-- VAS Pain Scale (0-10) -->
                            @php
                                $vasVal = $data['vas_scale'] ?? (isset($data['vas_pain_score']) ? round($data['vas_pain_score'] / 7) : 4);
                            @endphp
                            <div class="p-3 bg-white rounded-xl border border-slate-200 space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <strong class="text-slate-700 font-bold">Pain Intensity (VAS Scale):</strong>
                                    <span class="px-3 py-0.5 rounded-full text-white font-black text-xs {{ $vasVal == 0 ? 'bg-emerald-600' : ($vasVal <= 3 ? 'bg-blue-600' : ($vasVal <= 6 ? 'bg-amber-600' : 'bg-rose-600')) }}">
                                        Score: {{ $vasVal }} / 10
                                    </span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                    <div class="h-full {{ $vasVal <= 3 ? 'bg-blue-500' : ($vasVal <= 6 ? 'bg-amber-500' : 'bg-rose-500') }}" style="width: {{ min(100, $vasVal * 10) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Associate Factors -->
                    <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2">
                        <span class="font-bold text-slate-500 text-[10px] uppercase">Associate Factors:</span>
                        @php
                            $assocFactors = (array)($data['associate_factors'] ?? []);
                        @endphp
                        @forelse($assocFactors as $af)
                            <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-900 font-bold text-xs border border-blue-200">
                                ⚡ {{ $af }}
                            </span>
                        @empty
                            <span class="text-slate-400">None reported</span>
                        @endforelse
                        @if(!empty($data['associate_factors_notes']))
                            <span class="text-slate-600 text-xs">({{ $data['associate_factors_notes'] }})</span>
                        @endif
                    </div>
                </div>

                <!-- Card 2: Observation (Posture, Tenderness, Gait) & Advice & MMT -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <h4 class="font-black text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2 border-b border-slate-100 pb-2">
                        <i data-lucide="eye" class="w-4 h-4 text-indigo-600"></i>
                        Clinical Observation, Advice & MMT (Image 1)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">Posture (मुद्रा):</span>
                            <strong class="text-slate-900 text-xs">{{ $data['observation_posture'] ?? $data['posture'] ?? 'Normal' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">Tenderness (वेदना):</span>
                            <strong class="text-slate-900 text-xs">{{ $data['observation_tenderness'] ?? $data['tenderness'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block mb-1">GAIT (चाल):</span>
                            <strong class="text-slate-900 text-xs">{{ $data['observation_gait'] ?? $data['gait'] ?? 'Normal' }}</strong>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <!-- Advice -->
                        <div class="p-3.5 bg-blue-50/40 rounded-xl border border-blue-200 space-y-1">
                            <span class="font-bold text-blue-900 text-[10px] uppercase block">Advice (जाँच सलाह - X-ray / MRI):</span>
                            <div class="flex flex-wrap gap-2 text-xs">
                                @foreach((array)($data['advice_imaging'] ?? []) as $adv)
                                    <span class="px-2.5 py-0.5 rounded bg-blue-700 text-white font-black text-[10px]">
                                        {{ $adv }}
                                    </span>
                                @endforeach
                            </div>
                            @if(!empty($data['advice_notes']))
                                <p class="text-slate-700 text-xs mt-1">{{ $data['advice_notes'] }}</p>
                            @endif
                        </div>

                        <!-- MMT -->
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                            <span class="font-bold text-slate-700 text-[10px] uppercase block">MMT (Manual Muscle Testing):</span>
                            <div class="flex items-center gap-3 text-xs">
                                <span>Right: <strong class="text-blue-800">{{ $data['mmt_right'] ?? '-' }}</strong></span>
                                <span>Left: <strong class="text-indigo-800">{{ $data['mmt_left'] ?? '-' }}</strong></span>
                            </div>
                            @if(!empty($data['mmt_notes']))
                                <p class="text-slate-600 text-xs">{{ $data['mmt_notes'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card 3: RANGE OF MOTION TABLE (Flexion, Abduction, Extension, ER, IR) -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm bg-white">
                    <div class="bg-slate-50 px-4 py-2.5 border-b border-slate-200 flex items-center justify-between">
                        <h4 class="font-black text-slate-800 uppercase tracking-wider text-xs flex items-center gap-2">
                            <i data-lucide="compass" class="w-4 h-4 text-blue-600"></i>
                            Range of Motion (ROM - Image 1)
                        </h4>
                        <span class="text-[10px] font-bold text-slate-400">Flexion • Abduction • Extension • ER • IR</span>
                    </div>
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px]">
                            <tr>
                                <th class="p-2.5 pl-4">Movement</th>
                                <th class="p-2.5 w-48 text-blue-900">Right (R)</th>
                                <th class="p-2.5 w-48 text-indigo-900">Left (L)</th>
                                <th class="p-2.5">End-Feel / Clinical Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="p-2 pl-4 font-bold text-slate-800">Flexion</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_flexion_r'] ?? $data['rom_right'] ?? '-' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_flexion_l'] ?? $data['rom_left'] ?? '-' }}</td>
                                <td class="p-2 text-slate-500">{{ $data['rom_flexion_notes'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold text-slate-800">Abduction</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_abduction_r'] ?? '-' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_abduction_l'] ?? '-' }}</td>
                                <td class="p-2 text-slate-500">{{ $data['rom_abduction_notes'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold text-slate-800">Extension</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_extension_r'] ?? '-' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_extension_l'] ?? '-' }}</td>
                                <td class="p-2 text-slate-500">{{ $data['rom_extension_notes'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold text-slate-800">ER (External Rotation)</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_er_r'] ?? '-' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_er_l'] ?? '-' }}</td>
                                <td class="p-2 text-slate-500">{{ $data['rom_er_notes'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold text-slate-800">IR (Internal Rotation)</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_ir_r'] ?? '-' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_ir_l'] ?? '-' }}</td>
                                <td class="p-2 text-slate-500">{{ $data['rom_ir_notes'] ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Card 4: SPECIAL TESTS (Image 1) -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <h4 class="font-black text-emerald-900 uppercase tracking-wider text-xs flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                            Special Orthopedic Tests (Image 1)
                        </h4>
                        <span class="text-[10px] font-bold text-slate-400">Shoulder • Knee • Hip • Spine • Cervical</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                        <!-- Shoulder -->
                        <div class="p-3 rounded-xl border border-blue-100 bg-blue-50/30 space-y-1">
                            <span class="font-black text-blue-900 uppercase text-[10px] block">Shoulder</span>
                            <div>Drop arm: <strong class="text-slate-800">{{ $data['st_drop_arm'] ?? '-ve' }}</strong></div>
                            <div>Impingement: <strong class="text-slate-800">{{ $data['st_impingement'] ?? '-ve' }}</strong></div>
                        </div>

                        <!-- Knee -->
                        <div class="p-3 rounded-xl border border-indigo-100 bg-indigo-50/30 space-y-1">
                            <span class="font-black text-indigo-900 uppercase text-[10px] block">Knee</span>
                            <div>Drawer ant.: <strong class="text-slate-800">{{ $data['st_drawer_ant'] ?? '-ve' }}</strong></div>
                            <div>McMurray: <strong class="text-slate-800">{{ $data['st_mcmurray'] ?? '-ve' }}</strong></div>
                        </div>

                        <!-- Hip -->
                        <div class="p-3 rounded-xl border border-teal-100 bg-teal-50/30 space-y-1">
                            <span class="font-black text-teal-900 uppercase text-[10px] block">Hip</span>
                            <div>FABER: <strong class="text-slate-800">{{ $data['st_faber'] ?? '-ve' }}</strong></div>
                            <div>Trendelenburg: <strong class="text-slate-800">{{ $data['st_trendelenburg'] ?? '-ve' }}</strong></div>
                        </div>

                        <!-- Spine -->
                        <div class="p-3 rounded-xl border border-amber-100 bg-amber-50/30 space-y-1">
                            <span class="font-black text-amber-900 uppercase text-[10px] block">Spine</span>
                            <div>SLR: <strong class="text-slate-800">{{ $data['st_slr'] ?? '-ve' }}</strong></div>
                            <div>Slump: <strong class="text-slate-800">{{ $data['st_slump'] ?? '-ve' }}</strong></div>
                        </div>

                        <!-- Cervical -->
                        <div class="p-3 rounded-xl border border-rose-100 bg-rose-50/30 space-y-1">
                            <span class="font-black text-rose-900 uppercase text-[10px] block">Cervical</span>
                            <div>Compression: <strong class="text-slate-800">{{ $data['st_cervical_compression'] ?? '-ve' }}</strong></div>
                            <div>Spurling: <strong class="text-slate-800">{{ $data['st_spurling'] ?? '-ve' }}</strong></div>
                        </div>
                    </div>

                    @if(!empty($data['special_test_notes']))
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                            <span class="font-bold text-slate-500 uppercase text-[10px]">Additional Notes:</span>
                            <p class="text-slate-700 mt-0.5">{{ $data['special_test_notes'] }}</p>
                        </div>
                    @endif
                </div>

            </div>
        @else
            <!-- Neurological Assessment Details (Matching Images 2 & 3) -->
            <div class="space-y-6 text-xs">
                
                <!-- Card B1: Chief Complaint & Full History (Image 2) -->
                <div class="p-5 bg-white rounded-2xl border border-purple-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-purple-100 pb-2">
                        <h4 class="font-black text-purple-900 uppercase tracking-wider text-xs flex items-center gap-2">
                            <i data-lucide="clipboard" class="w-4 h-4 text-purple-600"></i>
                            Chief Complaint & History (Image 2 - Page 1)
                        </h4>
                        <span class="text-[10px] font-bold text-purple-600">Dominance: <strong>{{ $data['dominant_side'] ?? 'Right (R)' }}</strong></span>
                    </div>

                    <div class="space-y-2">
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Chief Complaint:</span>
                        <p class="text-purple-950 font-semibold bg-purple-50/40 p-3 rounded-xl border border-purple-100">
                            {{ $data['neuro_chief_complaint'] ?? $data['neuro_complaints'] ?? '-' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-2">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">past H/O:</span>
                            <strong class="text-slate-800">{{ $data['neuro_past_ho'] ?? $data['neuro_ho'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Surgical H/O:</span>
                            <strong class="text-slate-800">{{ $data['neuro_surgical_ho'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Family H/O:</span>
                            <strong class="text-slate-800">{{ $data['neuro_family_ho'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Associate Factors:</span>
                            <strong class="text-slate-800">{{ $data['neuro_associate_factors'] ?? '-' }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Card B2: Observation & External Appliances (Image 2) -->
                <div class="p-5 bg-white rounded-2xl border border-purple-200 shadow-sm space-y-4">
                    <h4 class="font-black text-purple-900 uppercase tracking-wider text-xs flex items-center gap-2 border-b border-purple-100 pb-2">
                        <i data-lucide="eye" class="w-4 h-4 text-purple-600"></i>
                        Observation & External Appliances (Image 2 - Page 1)
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Posture:</span>
                            <strong class="text-slate-800">{{ $data['neuro_posture'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Gait:</span>
                            <strong class="text-slate-800">{{ $data['neuro_gait'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Deformity:</span>
                            <strong class="text-slate-800">{{ $data['neuro_deformity'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <!-- External Appliances -->
                    <div class="p-3.5 bg-purple-50/30 rounded-xl border border-purple-100 space-y-2">
                        <span class="font-black text-purple-950 uppercase text-[10px] block">External Appliances:</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <span class="text-slate-500 font-bold block text-[11px] mb-1">Functional aids (walking aids / catheter):</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach((array)($data['neuro_functional_aids'] ?? []) as $fa)
                                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-900 font-bold text-[10px]">{{ $fa }}</span>
                                    @endforeach
                                </div>
                                @if(!empty($data['neuro_functional_aids_notes']))
                                    <p class="text-[11px] text-slate-600 mt-1">{{ $data['neuro_functional_aids_notes'] }}</p>
                                @endif
                            </div>
                            <div>
                                <span class="text-slate-500 font-bold block text-[11px] mb-1">Protective aids (brace / prosthetics):</span>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach((array)($data['neuro_protective_aids'] ?? []) as $pa)
                                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-900 font-bold text-[10px]">{{ $pa }}</span>
                                    @endforeach
                                </div>
                                @if(!empty($data['neuro_protective_aids_notes']))
                                    <p class="text-[11px] text-slate-600 mt-1">{{ $data['neuro_protective_aids_notes'] }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card B3: Examination (Consciousness, Orientation, Behaviour, Memory, Special Senses - Image 2) -->
                <div class="p-5 bg-white rounded-2xl border border-purple-200 shadow-sm space-y-4">
                    <h4 class="font-black text-purple-900 uppercase tracking-wider text-xs flex items-center gap-2 border-b border-purple-100 pb-2">
                        <i data-lucide="brain-circuit" class="w-4 h-4 text-purple-600"></i>
                        Examination: Consciousness & Higher Functions (Image 2 - Page 1)
                    </h4>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Consciousness Level:</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-purple-800 text-white font-black text-xs inline-block mt-1">
                                {{ $data['neuro_consciousness_level'] ?? $data['level_of_consciousness'] ?? 'Alert' }}
                            </span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Orientation:</span>
                            <strong class="text-slate-800 block mt-1">{{ $data['neuro_orientation'] ?? $data['orientation'] ?? 'Intact' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Behaviour:</span>
                            <strong class="text-slate-800 block mt-1">{{ $data['neuro_behaviour'] ?? 'Cooperative' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Memory:</span>
                            <strong class="text-slate-800 block mt-1">{{ $data['neuro_memory'] ?? $data['memory'] ?? 'Intact' }}</strong>
                        </div>
                    </div>

                    <!-- Special Senses -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                        <span class="font-bold text-slate-700 text-[10px] uppercase block">Special Sense (विशेष संवेदनाएं):</span>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 text-xs">
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Vision:</span>
                                <strong>{{ $data['neuro_sense_vision'] ?? 'Normal' }}</strong>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Hearing:</span>
                                <strong>{{ $data['neuro_sense_hearing'] ?? 'Normal' }}</strong>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Smell:</span>
                                <strong>{{ $data['neuro_sense_smell'] ?? 'Normal' }}</strong>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Taste:</span>
                                <strong>{{ $data['neuro_sense_taste'] ?? 'Normal' }}</strong>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[10px] text-slate-400 uppercase font-bold block">Tactile:</span>
                                <strong>{{ $data['neuro_sense_tactile'] ?? 'Normal' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card B4: Sensory, Motor, Reflex, Cortical Reflex, Investigations & Treatment Plan (Image 3) -->
                <div class="p-5 bg-white rounded-2xl border border-purple-200 shadow-sm space-y-4">
                    <h4 class="font-black text-purple-900 uppercase tracking-wider text-xs flex items-center gap-2 border-b border-purple-100 pb-2">
                        <i data-lucide="zap" class="w-4 h-4 text-purple-600"></i>
                        Sensory, Motor, Reflex & Treatment Plan (Image 3 - Page 2)
                    </h4>

                    <!-- Sensory Examination -->
                    <div class="space-y-1">
                        <span class="text-slate-400 font-bold uppercase text-[10px] block">Sensory Examination:</span>
                        <p class="text-slate-800 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            {{ $data['neuro_sensory_exam'] ?? $data['sensory_deficit'] ?? 'Superficial & deep sensations intact' }}
                        </p>
                    </div>

                    <!-- Motor Examination (ROM, MMT, TONE) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Motor - ROM:</span>
                            <strong class="text-slate-800">{{ $data['neuro_motor_rom'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Motor - MMT:</span>
                            <strong class="text-slate-800">{{ $data['neuro_motor_mmt'] ?? $data['neuro_mmt'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Motor - TONE:</span>
                            <strong class="text-slate-800">{{ $data['neuro_motor_tone'] ?? $data['neuro_tone'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <!-- Reflex & Pattern -->
                    <div class="p-3.5 bg-purple-50/40 rounded-xl border border-purple-100 grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <span class="text-purple-950 font-bold uppercase text-[10px] block">DTR (Deep Tendon Reflexes):</span>
                            <strong class="text-purple-900 text-xs">{{ $data['neuro_reflex_dtr'] ?? $data['deep_tendon_reflexes'] ?? '-' }}</strong>
                        </div>
                        <div>
                            <span class="text-purple-950 font-bold uppercase text-[10px] block">Synergic Pattern:</span>
                            <div class="flex flex-wrap gap-1.5 mt-0.5">
                                @foreach((array)($data['neuro_synergic_pattern'] ?? []) as $sp)
                                    <span class="px-2 py-0.5 rounded bg-purple-700 text-white font-bold text-[10px]">{{ $sp }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Cortical Level Reflex -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Balance (संतुलन):</span>
                            <strong class="text-slate-800">{{ $data['neuro_cortical_balance'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Equilibrium:</span>
                            <strong class="text-slate-800">{{ $data['neuro_cortical_equilibrium'] ?? '-' }}</strong>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] block">Coordination:</span>
                            <strong class="text-slate-800">{{ $data['neuro_cortical_coordination'] ?? '-' }}</strong>
                        </div>
                    </div>

                    <!-- Investigations (CT, MRI, EMG, NCV) -->
                    <div class="p-3.5 bg-indigo-50/40 rounded-xl border border-indigo-100 space-y-1.5">
                        <span class="font-bold text-indigo-950 uppercase text-[10px] block">Investigation (जाँच - CT, MRI, EMG, NCV):</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach((array)($data['neuro_investigation'] ?? $data['investigations'] ?? []) as $inv)
                                <span class="px-2.5 py-0.5 rounded bg-indigo-700 text-white font-bold text-[10px]">{{ $inv }}</span>
                            @endforeach
                        </div>
                        @if(!empty($data['neuro_investigation_notes']) || !empty($data['investigation_notes']))
                            <p class="text-slate-700 text-xs">{{ $data['neuro_investigation_notes'] ?? $data['investigation_notes'] }}</p>
                        @endif
                    </div>

                    <!-- Treatment Plan -->
                    <div class="space-y-1">
                        <span class="font-bold text-slate-800 uppercase text-[10px] block">Treatment Plan (पुनर्वास व उपचार योजना - Image 3):</span>
                        <p class="text-slate-800 bg-emerald-50/50 p-3 rounded-xl border border-emerald-200 font-medium">
                            {{ $data['neuro_treatment_plan'] ?? $prescription->treatment_plan ?? 'Comprehensive Neuro Rehabilitation Protocol' }}
                        </p>
                    </div>
                </div>

            </div>
        @endif

        <!-- ============================================================== -->
        <!-- PRESCRIBED REHABILITATION & EXERCISES                          -->
        <!-- ============================================================== -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between">
                <h3 class="font-black text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                    <i data-lucide="dumbbell" class="w-4 h-4 text-emerald-600"></i> Prescribed Rehabilitation Exercises & Modalities
                </h3>
                @if($prescription->treatment_days)
                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 rounded-lg font-bold text-xs border border-emerald-200">
                        {{ $prescription->treatment_days }} Days Program
                    </span>
                @endif
            </div>

            <!-- Modalities -->
            @if($prescription->modalities)
                <div class="p-3 bg-emerald-50/50 rounded-xl border border-emerald-200 text-xs flex flex-wrap items-center gap-2">
                    <span class="font-bold text-emerald-950 text-[11px] uppercase">Modalities:</span>
                    @foreach(explode(',', $prescription->modalities) as $mod)
                        <span class="px-2.5 py-0.5 bg-white text-emerald-800 rounded-lg border border-emerald-200 font-bold text-xs shadow-2xs">
                            ⚡ {{ trim($mod) }}
                        </span>
                    @endforeach
                </div>
            @endif

            <!-- Exercises Table -->
            @if(!empty($prescription->prescribed_exercises) && count($prescription->prescribed_exercises) > 0)
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Exercise Name</th>
                                <th class="p-3">Target Area</th>
                                <th class="p-3">Sets</th>
                                <th class="p-3">Reps</th>
                                <th class="p-3">Hold / Duration</th>
                                <th class="p-3">Instructions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($prescription->prescribed_exercises as $idx => $ex)
                                <tr>
                                    <td class="p-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $ex['name'] ?? 'Exercise' }}</td>
                                    <td class="p-3 text-slate-600">{{ $ex['target'] ?? '-' }}</td>
                                    <td class="p-3 font-bold text-emerald-700">{{ $ex['sets'] ?? '-' }}</td>
                                    <td class="p-3 font-bold text-emerald-700">{{ $ex['reps'] ?? '-' }}</td>
                                    <td class="p-3 text-slate-700">{{ $ex['duration'] ?? '-' }}</td>
                                    <td class="p-3 text-slate-600 text-[11px]">{{ $ex['instructions'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- ============================================================== -->
        <!-- MEDICATIONS (℞)                                                -->
        <!-- ============================================================== -->
        @if($prescription->items->count() > 0)
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="font-serif italic font-black text-xl text-blue-900">℞</span>
                    <span class="font-black text-slate-900 uppercase tracking-wider text-xs">Prescribed Medications</span>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-3">#</th>
                                <th class="p-3">Medicine</th>
                                <th class="p-3">Dosage</th>
                                <th class="p-3">Frequency</th>
                                <th class="p-3">Duration</th>
                                <th class="p-3">Timing & Directions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($prescription->items as $idx => $item)
                                <tr>
                                    <td class="p-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                                    <td class="p-3 font-bold text-slate-900">{{ $item->medicine_name }}</td>
                                    <td class="p-3 text-slate-600">{{ $item->dosage }}</td>
                                    <td class="p-3 font-bold text-blue-700">{{ $item->frequency }}</td>
                                    <td class="p-3 text-slate-700">{{ $item->duration }}</td>
                                    <td class="p-3 text-slate-600">{{ $item->timing }} {{ $item->instructions ? '• ' . $item->instructions : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- ADVICE & FOLLOW-UP -->
        <div class="p-4 bg-slate-50 rounded-2xl grid grid-cols-1 md:grid-cols-2 gap-4 text-xs border border-slate-200">
            <div>
                <strong class="text-slate-800 block mb-1 uppercase text-[10px]">Ergonomic Advice:</strong>
                <p class="text-slate-700 leading-relaxed">{{ $prescription->advice ?? 'Perform prescribed exercises twice daily. Maintain ergonomic spine posture while sitting.' }}</p>
            </div>
            <div>
                <strong class="text-slate-800 block mb-1 uppercase text-[10px]">Next Scheduled Review:</strong>
                <span class="font-black text-blue-800 text-sm block">
                    {{ $prescription->follow_up_date ? $prescription->follow_up_date->format('d M Y') : 'SOS / Revisit on discomfort' }}
                </span>
                <p class="text-[11px] text-slate-400 mt-1">Please bring this assessment record on your revisit.</p>
            </div>
        </div>

    </div>

</div>
@endsection
