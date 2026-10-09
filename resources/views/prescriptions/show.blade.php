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

        <!-- PATIENT INFO CARD -->
        <div class="p-4 bg-slate-50 rounded-2xl grid grid-cols-2 md:grid-cols-4 gap-4 text-xs border border-slate-200">
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Patient Name:</span>
                <a href="{{ route('patients.show', $prescription->patient_id) }}" class="font-black text-slate-900 hover:text-blue-700 text-sm">
                    {{ $prescription->patient->full_name }}
                </a>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Patient ID:</span>
                <span class="font-mono font-bold text-blue-700">{{ $prescription->patient->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Age / Gender:</span>
                <span class="font-bold text-slate-800">{{ $prescription->patient->age }} Yrs / {{ $prescription->patient->gender }}</span>
                @if($prescription->patient->blood_group)
                    <span class="text-rose-600 font-bold">({{ $prescription->patient->blood_group }})</span>
                @endif
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Registered Clinic Branch:</span>
                <span class="font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 block truncate">
                    {{ $prescribedClinic->name }}
                </span>
            </div>
        </div>

        <!-- DIAGNOSIS BANNER -->
        <div class="p-4 bg-blue-50/70 rounded-2xl border border-blue-200 flex items-start gap-3">
            <div class="p-2 rounded-xl bg-blue-600 text-white shadow-sm mt-0.5">
                <i data-lucide="activity" class="w-4 h-4"></i>
            </div>
            <div>
                <span class="text-[10px] font-black uppercase tracking-wider text-blue-900 block">Primary Clinical Physical Therapy Diagnosis</span>
                <p class="font-black text-slate-900 text-base mt-0.5">{{ $prescription->diagnosis_summary }}</p>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- ASSESSMENT DETAILS ACCORDION/BLOCKS                            -->
        <!-- ============================================================== -->
        @if(!$isNeuro)
            <!-- Musculoskeletal Assessment Details -->
            <div class="space-y-5 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h3 class="font-black text-slate-900 uppercase tracking-wider text-xs flex items-center gap-2">
                        <i data-lucide="bone" class="w-4 h-4 text-blue-600"></i> Musculo Skeletal Clinical Assessment Data
                    </h3>
                    <span class="text-[11px] font-mono text-slate-400 font-bold">Evaluation Chart</span>
                </div>

                <!-- Complaints & Vitals -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2">
                        <p><strong>C/O:</strong> <span class="text-slate-700">{{ $data['chief_complaints'] ?? '-' }}</span></p>
                        <p><strong>H/O:</strong> 
                            <span class="text-slate-700">
                                @if(!empty($data['ho_conditions']))
                                    {{ implode(', ', (array)$data['ho_conditions']) }}
                                @else
                                    None
                                @endif
                            </span>
                        </p>
                        <p><strong>Family History:</strong> <span class="text-slate-700">{{ $data['family_history'] ?? '-ve' }}</span></p>
                        <p><strong>Personal History:</strong> <span class="text-slate-700">{{ $data['personal_history'] ?? '-' }}</span></p>
                        <p><strong>Present History:</strong> <span class="text-slate-700">{{ $data['present_history'] ?? '-' }}</span></p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-3">
                        <div class="grid grid-cols-4 gap-2 text-center text-[11px]">
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[9px] text-slate-400 block font-bold">HR (/min)</span>
                                <strong class="text-slate-800">{{ $data['vitals_hr'] ?? '72' }}</strong>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[9px] text-slate-400 block font-bold">RR (/min)</span>
                                <strong class="text-slate-800">{{ $data['vitals_rr'] ?? '18' }}</strong>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[9px] text-slate-400 block font-bold">Temp</span>
                                <strong class="text-slate-800">{{ $data['vitals_temp'] ?? '98.4°F' }}</strong>
                            </div>
                            <div class="bg-white p-2 rounded border border-slate-200">
                                <span class="text-[9px] text-slate-400 block font-bold">BP</span>
                                <strong class="text-slate-800">{{ $data['vitals_bp'] ?? '120/80' }}</strong>
                            </div>
                        </div>

                        <!-- VAS Pain Scale -->
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 space-y-1">
                            <div class="flex items-center justify-between text-xs font-bold text-amber-900">
                                <span>Pain Rating (VAS Scale):</span>
                                <span class="px-2 py-0.5 bg-amber-600 text-white rounded text-xs font-black">{{ $data['vas_pain_score'] ?? '40' }} / 70</span>
                            </div>
                            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-full" style="width: {{ min(100, (($data['vas_pain_score'] ?? 40) / 70) * 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- On look & On Palpation -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5 text-[11px]">
                        <strong class="text-slate-800 font-bold block mb-1 uppercase text-[10px]">On look (Observation):</strong>
                        <div><strong>Gait:</strong> {{ $data['gait'] ?? '-' }}</div>
                        <div><strong>Body Built:</strong> {{ $data['body_built'] ?? '-' }}</div>
                        <div><strong>Posture:</strong> {{ $data['posture'] ?? '-' }}</div>
                        <div><strong>Facial Exp.:</strong> {{ $data['facial_expression'] ?? '-' }}</div>
                        <div><strong>Deformity:</strong> {{ $data['deformity'] ?? '-' }}</div>
                        <div><strong>Muscle:</strong> {{ $data['muscle_state'] ?? '-' }}</div>
                        <div><strong>Foot Deformity:</strong> {{ $data['foot_deformity'] ?? '-' }}</div>
                        <div><strong>Skin color:</strong> {{ $data['skin_color'] ?? '-' }}</div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5 text-[11px]">
                        <strong class="text-slate-800 font-bold block mb-1 uppercase text-[10px]">On Palpation:</strong>
                        <div><strong>Crepitus:</strong> {{ $data['crepitus'] ?? '-' }}</div>
                        <div><strong>Local Temp:</strong> {{ $data['palpation_temp'] ?? '-' }}</div>
                        <div><strong>Tenderness:</strong> {{ $data['tenderness'] ?? '-' }}</div>
                        <div><strong>Bony Contour:</strong> {{ $data['bony_contour'] ?? '-' }}</div>
                        <div><strong>Musspasm:</strong> <span class="text-rose-700 font-bold">{{ $data['muscle_spasm'] ?? '-' }}</span></div>
                        <div><strong>Rigidity:</strong> {{ $data['rigidity'] ?? '-' }}</div>
                        <div><strong>Balance / Proprioception:</strong> {{ $data['balance_proprioception'] ?? '-' }}</div>
                        <div><strong>Vestibular Exam:</strong> {{ $data['vestibular_exam'] ?? '-' }}</div>
                    </div>
                </div>

                <!-- Examination Table -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-slate-100 font-bold text-slate-700 uppercase text-[10px]">
                            <tr>
                                <th class="p-2.5 pl-4">Examination</th>
                                <th class="p-2.5 w-48 text-blue-900">Right (R)</th>
                                <th class="p-2.5 w-48 text-indigo-900">Left (L)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="p-2 pl-4 font-bold">ROM (Range of Motion)</td>
                                <td class="p-2">{{ $data['rom_right'] ?? '-' }}</td>
                                <td class="p-2">{{ $data['rom_left'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold">MMT Grade</td>
                                <td class="p-2 font-bold text-blue-800">{{ $data['mmt_right'] ?? '-' }}</td>
                                <td class="p-2 font-bold text-indigo-800">{{ $data['mmt_left'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold">Co-ordination (UL / LL)</td>
                                <td class="p-2">{{ $data['coord_ul_right'] ?? '-' }}</td>
                                <td class="p-2">{{ $data['coord_ul_left'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold">Special Tests</td>
                                <td class="p-2">{{ $data['special_tests_right'] ?? '-' }}</td>
                                <td class="p-2">{{ $data['special_tests_left'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold">Neural Tension (ULTT / LLTT)</td>
                                <td class="p-2">ULTT: {{ $data['ultt_right'] ?? '-' }} | LLTT: {{ $data['lltt_right'] ?? '-' }}</td>
                                <td class="p-2">ULTT: {{ $data['ultt_left'] ?? '-' }} | LLTT: {{ $data['lltt_left'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-4 font-bold">For VBI / C-Spine TOS</td>
                                <td class="p-2">{{ $data['vbi_right'] ?? '-' }}</td>
                                <td class="p-2">{{ $data['vbi_left'] ?? '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 grid grid-cols-2 md:grid-cols-4 gap-2 text-[11px]">
                    <div><strong>LLD:</strong> {{ $data['lld'] ?? 'Equal' }}</div>
                    <div><strong>Muscle Girth:</strong> {{ $data['muscle_girth'] ?? '-' }}</div>
                    <div><strong>TOS Deficits:</strong> {{ $data['tos_deficits'] ?? '-' }}</div>
                    <div><strong>Osteopathic Findings:</strong> {{ $data['osteopathic_terms'] ?? '-' }}</div>
                </div>
            </div>
        @else
            <!-- Neurological Assessment Details -->
            <div class="space-y-5 text-xs">
                <div class="flex items-center justify-between border-b border-purple-100 pb-2">
                    <h3 class="font-black text-purple-900 uppercase tracking-wider text-xs flex items-center gap-2">
                        <i data-lucide="brain" class="w-4 h-4 text-purple-600"></i> Neurological Clinical Assessment Data
                    </h3>
                    <span class="text-[11px] font-mono text-purple-500 font-bold">Neuro Chart</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Dominant Side</span>
                        <strong class="text-purple-950 font-black">{{ $data['dominant_side'] ?? 'Right (R)' }}</strong>
                    </div>
                    <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">Consciousness</span>
                        <strong class="text-slate-800 font-bold">{{ $data['level_of_consciousness'] ?? 'Alert' }}</strong>
                    </div>
                    <div class="p-3 bg-purple-50/60 rounded-xl border border-purple-100">
                        <span class="text-slate-400 block text-[10px] uppercase font-bold">GCS Scale</span>
                        <strong class="text-purple-900 font-mono font-black">{{ $data['gcs_scale'] ?? '15/15' }}</strong>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5 text-[11px]">
                        <strong class="text-purple-900 font-bold block mb-1 uppercase text-[10px]">Sensory Exam:</strong>
                        <div><strong>Deficit:</strong> {{ $data['sensory_deficit'] ?? '-' }}</div>
                        <div><strong>Superficial:</strong> {{ $data['superficial_sensations'] ?? '-' }}</div>
                        <div><strong>Deep:</strong> {{ $data['deep_sensations'] ?? '-' }}</div>
                        <div><strong>Cortical:</strong> {{ $data['cortical_sensations'] ?? '-' }}</div>
                        <div><strong>Dermatome:</strong> {{ $data['dermatome_involved'] ?? '-' }}</div>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5 text-[11px]">
                        <strong class="text-purple-900 font-bold block mb-1 uppercase text-[10px]">Motor & Reflexes:</strong>
                        <div><strong>MMT:</strong> {{ $data['neuro_mmt'] ?? '-' }}</div>
                        <div><strong>Tone:</strong> {{ $data['neuro_tone'] ?? '-' }}</div>
                        <div><strong>Girth / Fasciculations:</strong> {{ $data['neuro_girth'] ?? '-' }}, {{ $data['neuro_fasciculations'] ?? '-' }}</div>
                        <div><strong>DTR Reflexes:</strong> {{ $data['deep_tendon_reflexes'] ?? '-' }}</div>
                        <div><strong>Superficial Reflexes:</strong> {{ $data['superficial_reflexes'] ?? '-' }}</div>
                    </div>
                </div>

                <!-- Cranial Nerve Table -->
                <div class="border border-purple-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-purple-50 text-purple-900 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-2.5 pl-4">Cranial Nerve (I - XII)</th>
                                <th class="p-2.5 w-40">Right (R)</th>
                                <th class="p-2.5 w-40">Left (L)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-100">
                            <tr><td class="p-1.5 pl-4 font-bold">I - Olfactory</td><td class="p-1.5">{{ $data['cn_1_r'] ?? 'Intact' }}</td><td class="p-1.5">{{ $data['cn_1_l'] ?? 'Intact' }}</td></tr>
                            <tr><td class="p-1.5 pl-4 font-bold">II - Optic</td><td class="p-1.5">{{ $data['cn_2_r'] ?? 'Normal' }}</td><td class="p-1.5">{{ $data['cn_2_l'] ?? 'Normal' }}</td></tr>
                            <tr><td class="p-1.5 pl-4 font-bold">III, IV, VI - Oculomotor, Trochlear, Abducens</td><td class="p-1.5">{{ $data['cn_346_r'] ?? 'Normal' }}</td><td class="p-1.5">{{ $data['cn_346_l'] ?? 'Normal' }}</td></tr>
                            <tr><td class="p-1.5 pl-4 font-bold">V - Trigeminal</td><td class="p-1.5">{{ $data['cn_5_r'] ?? 'Normal' }}</td><td class="p-1.5">{{ $data['cn_5_l'] ?? 'Normal' }}</td></tr>
                            <tr><td class="p-1.5 pl-4 font-bold">VII - Facial</td><td class="p-1.5">{{ $data['cn_7_r'] ?? 'Normal' }}</td><td class="p-1.5">{{ $data['cn_7_l'] ?? 'Normal' }}</td></tr>
                            <tr><td class="p-1.5 pl-4 font-bold">VIII - Vestibulocochlear</td><td class="p-1.5">{{ $data['cn_8_r'] ?? 'Normal' }}</td><td class="p-1.5">{{ $data['cn_8_l'] ?? 'Normal' }}</td></tr>
                            <tr><td class="p-1.5 pl-4 font-bold">IX, X - Glossopharyngeal, Vagus</td><td class="p-1.5">{{ $data['cn_910_r'] ?? 'Normal' }}</td><td class="p-1.5">{{ $data['cn_910_l'] ?? 'Normal' }}</td></tr>
                            <tr><td class="p-1.5 pl-4 font-bold">XI - Accessory</td><td class="p-1.5">{{ $data['cn_11_r'] ?? 'Normal' }}</td><td class="p-1.5">{{ $data['cn_11_l'] ?? 'Normal' }}</td></tr>
                            <tr><td class="p-1.5 pl-4 font-bold">XII - Hypoglossal</td><td class="p-1.5">{{ $data['cn_12_r'] ?? 'Normal' }}</td><td class="p-1.5">{{ $data['cn_12_l'] ?? 'Normal' }}</td></tr>
                        </tbody>
                    </table>
                </div>

                @if(!empty($data['investigations']))
                    <div class="p-3 bg-purple-50 rounded-xl border border-purple-200 text-xs">
                        <strong>Advised Investigations:</strong>
                        <span class="font-bold text-purple-900">{{ implode(', ', (array)$data['investigations']) }}</span>
                        @if(!empty($data['investigation_notes']))
                            <p class="text-slate-600 mt-1">Notes: {{ $data['investigation_notes'] }}</p>
                        @endif
                    </div>
                @endif
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
