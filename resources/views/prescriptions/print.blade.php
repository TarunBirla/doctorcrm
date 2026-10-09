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

        <!-- Patient Demographics Table -->
        <div class="border border-slate-300 rounded-xl overflow-hidden text-xs">
            <div class="bg-slate-50 p-3 grid grid-cols-2 md:grid-cols-4 gap-3 border-b border-slate-200">
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Name (मरीज का नाम):</span>
                    <strong class="text-slate-900 font-bold text-sm">{{ $prescription->patient->full_name }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Age / Sex (उम्र / लिंग):</span>
                    <span class="font-bold text-slate-800">{{ $prescription->patient->age }} Yrs / {{ $prescription->patient->gender }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Occupation (व्यवसाय):</span>
                    <span class="font-semibold text-slate-800">{{ $prescription->patient->occupation ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px] font-bold uppercase">Date (दिनांक):</span>
                    <span class="font-bold text-slate-900 font-mono">{{ $prescription->prescription_date->format('d/m/Y') }}</span>
                </div>
            </div>
            <div class="p-2.5 bg-white flex flex-wrap items-center justify-between text-[11px] gap-2">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Address:</span>
                    <span class="text-slate-700 font-medium">{{ $prescription->patient->address ?? '-' }}, {{ $prescription->patient->city }}</span>
                </div>
                <div class="flex items-center gap-3">
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
            <div class="space-y-4 text-xs">
                
                <!-- Complaints & Vitals -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border border-slate-200 rounded-xl p-4 bg-slate-50/30">
                    <div class="space-y-2">
                        <p><strong>C/O:</strong> <span class="text-slate-700">{{ $data['chief_complaints'] ?? 'General pain and restricted movement' }}</span></p>
                        <p><strong>H/O:</strong> 
                            <span class="text-slate-700">
                                @if(!empty($data['ho_conditions']))
                                    {{ implode(', ', (array)$data['ho_conditions']) }}
                                @else
                                    None reported
                                @endif
                            </span>
                        </p>
                        <p><strong>Family History:</strong> <span class="text-slate-700">{{ $data['family_history'] ?? '-ve' }}</span></p>
                        <p><strong>Personal History:</strong> <span class="text-slate-700">{{ $data['personal_history'] ?? '-' }}</span></p>
                        <p><strong>Present Medical History:</strong> <span class="text-slate-700">{{ $data['present_history'] ?? '-' }}</span></p>
                    </div>

                    <div class="space-y-3">
                        <!-- Vitals Table -->
                        <div class="bg-white p-2.5 rounded-lg border border-slate-200">
                            <span class="font-bold text-[10px] text-slate-400 uppercase block mb-1.5">Vitalsign:</span>
                            <div class="grid grid-cols-4 gap-2 text-center text-[11px]">
                                <div class="bg-slate-50 p-1.5 rounded">
                                    <span class="text-[9px] text-slate-400 block font-bold">HR (/min)</span>
                                    <strong class="text-slate-800">{{ $data['vitals_hr'] ?? '72' }}</strong>
                                </div>
                                <div class="bg-slate-50 p-1.5 rounded">
                                    <span class="text-[9px] text-slate-400 block font-bold">RR (/min)</span>
                                    <strong class="text-slate-800">{{ $data['vitals_rr'] ?? '18' }}</strong>
                                </div>
                                <div class="bg-slate-50 p-1.5 rounded">
                                    <span class="text-[9px] text-slate-400 block font-bold">Temp</span>
                                    <strong class="text-slate-800">{{ $data['vitals_temp'] ?? '98.4°F' }}</strong>
                                </div>
                                <div class="bg-slate-50 p-1.5 rounded">
                                    <span class="text-[9px] text-slate-400 block font-bold">BP</span>
                                    <strong class="text-slate-800">{{ $data['vitals_bp'] ?? '120/80' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- VAS PAIN SCALE -->
                        <div class="bg-amber-50/50 p-2.5 rounded-lg border border-amber-200/60">
                            <div class="flex items-center justify-between text-[11px]">
                                <strong class="text-amber-900 font-bold">Pain (VAS Scale):</strong>
                                <span class="font-black px-2 py-0.5 rounded bg-amber-600 text-white text-xs">
                                    {{ $data['vas_pain_score'] ?? '40' }} / 70
                                </span>
                            </div>
                            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden mt-1.5">
                                <div class="bg-amber-500 h-full" style="width: {{ min(100, (($data['vas_pain_score'] ?? 40) / 70) * 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-[9px] text-slate-400 mt-1">
                                <span>0 (No pain)</span>
                                <span>35 (Moderate)</span>
                                <span>70 (Severe)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- On Look & Palpation 2-Column Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border border-slate-200 rounded-xl p-4">
                    <!-- On look (observation) -->
                    <div class="space-y-1.5 text-[11px]">
                        <strong class="block text-slate-900 text-xs uppercase border-b border-slate-200 pb-1">On look (observation)</strong>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Gait:</span> <span class="col-span-2 font-semibold">{{ $data['gait'] ?? 'Antalgic' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Body Built:</span> <span class="col-span-2 font-semibold">{{ $data['body_built'] ?? 'Normal' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Posture:</span> <span class="col-span-2 font-semibold">{{ $data['posture'] ?? 'Mild kyphosis' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Facial exp.:</span> <span class="col-span-2 font-semibold">{{ $data['facial_expression'] ?? 'Distressed with pain' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Deformity:</span> <span class="col-span-2 font-semibold">{{ $data['deformity'] ?? 'None' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Muscle:</span> <span class="col-span-2 font-semibold">{{ $data['muscle_state'] ?? 'Normal' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Foot Deformity:</span> <span class="col-span-2 font-semibold">{{ $data['foot_deformity'] ?? 'Normal' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Skin color:</span> <span class="col-span-2 font-semibold">{{ $data['skin_color'] ?? 'Normal' }}</span></div>
                    </div>

                    <!-- On Palpation -->
                    <div class="space-y-1.5 text-[11px]">
                        <strong class="block text-slate-900 text-xs uppercase border-b border-slate-200 pb-1">On Palpation</strong>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Crepitus:</span> <span class="col-span-2 font-semibold">{{ $data['crepitus'] ?? 'Absent' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Temp:</span> <span class="col-span-2 font-semibold">{{ $data['palpation_temp'] ?? 'Normal' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Tenderness:</span> <span class="col-span-2 font-semibold">{{ $data['tenderness'] ?? 'Grade II localized' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Bony Contour:</span> <span class="col-span-2 font-semibold">{{ $data['bony_contour'] ?? 'Intact' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Musspasm:</span> <span class="col-span-2 font-semibold text-rose-800">{{ $data['muscle_spasm'] ?? 'Present' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Rigidity:</span> <span class="col-span-2 font-semibold">{{ $data['rigidity'] ?? 'Nil' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Balance/Prop.:</span> <span class="col-span-2 font-semibold">{{ $data['balance_proprioception'] ?? 'Normal' }}</span></div>
                        <div class="grid grid-cols-3"><span class="text-slate-500 font-bold">Vestibular:</span> <span class="col-span-2 font-semibold">{{ $data['vestibular_exam'] ?? 'Normal' }}</span></div>
                    </div>
                </div>

                <!-- Bilateral Examination Table (R vs L) -->
                <div class="border border-slate-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-2 pl-3">On Examination Parameter</th>
                                <th class="p-2 w-48 text-blue-900">Right (R)</th>
                                <th class="p-2 w-48 text-indigo-900">Left (L)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="p-2 pl-3 font-bold">ROM (Range of Motion)</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_right'] ?? 'Full' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['rom_left'] ?? 'Full' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-3 font-bold">MMT Grade (0 - 5)</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['mmt_right'] ?? 'Grade 5/5' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['mmt_left'] ?? 'Grade 5/5' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-3 font-bold">Co-ordination (UL / LL)</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['coord_ul_right'] ?? 'Normal' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['coord_ul_left'] ?? 'Normal' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-3 font-bold">Special Tests (SLR / Faber / etc.)</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['special_tests_right'] ?? 'Negative' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['special_tests_left'] ?? 'Negative' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-3 font-bold">Neural Tension (ULTT / LLTT)</td>
                                <td class="p-2 font-semibold text-slate-800">ULTT: {{ $data['ultt_right'] ?? '-ve' }} | LLTT: {{ $data['lltt_right'] ?? '-ve' }}</td>
                                <td class="p-2 font-semibold text-slate-800">ULTT: {{ $data['ultt_left'] ?? '-ve' }} | LLTT: {{ $data['lltt_left'] ?? '-ve' }}</td>
                            </tr>
                            <tr>
                                <td class="p-2 pl-3 font-bold">For VBI / C-Spine TOS</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['vbi_right'] ?? 'Negative' }}</td>
                                <td class="p-2 font-semibold text-slate-800">{{ $data['vbi_left'] ?? 'Negative' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Osteopathic terms & LLD -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] grid grid-cols-2 md:grid-cols-4 gap-2">
                    <div><strong>LLD (Limb Length):</strong> {{ $data['lld'] ?? 'Equal' }}</div>
                    <div><strong>Muscle Girth:</strong> {{ $data['muscle_girth'] ?? 'Symmetrical' }}</div>
                    <div><strong>TOS Deficits:</strong> {{ $data['tos_deficits'] ?? 'None' }}</div>
                    <div><strong>Osteopathic Findings:</strong> {{ $data['osteopathic_terms'] ?? 'Normal alignment' }}</div>
                </div>

            </div>

        <!-- ============================================================== -->
        <!-- ASSESSMENT DETAILS (TYPE 2: NEUROLOGICAL)                      -->
        <!-- ============================================================== -->
        @else
            <div class="space-y-4 text-xs">
                
                <!-- Neuro Overview -->
                <div class="border border-purple-200 rounded-xl p-4 bg-purple-50/30 grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Dominant Side:</span>
                        <strong class="text-purple-950 font-black text-sm">{{ $data['dominant_side'] ?? 'Right (R)' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">Consciousness Level:</span>
                        <strong class="text-slate-800 font-bold">{{ $data['level_of_consciousness'] ?? 'Alert & Conscious' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-bold uppercase">GCS Scale (E / V / M):</span>
                        <strong class="font-mono text-purple-900 font-black text-sm">{{ $data['gcs_scale'] ?? '15 / 15' }}</strong>
                    </div>
                    <div class="md:col-span-3 pt-1 border-t border-purple-100 flex items-start gap-4">
                        <p><strong>C/o:</strong> <span class="text-slate-700">{{ $data['neuro_complaints'] ?? 'Neurological deficits noted' }}</span></p>
                        <p><strong>H/o:</strong> <span class="text-slate-700">{{ $data['neuro_ho'] ?? 'HT / DM' }}</span></p>
                    </div>
                </div>

                <!-- Sensory & Motor Exam Tables -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Sensory -->
                    <div class="border border-slate-200 rounded-xl p-3.5 space-y-2 text-[11px]">
                        <strong class="block text-purple-900 text-xs uppercase border-b border-slate-200 pb-1 font-black">Sensory Examination</strong>
                        <div><strong>Deficit:</strong> {{ $data['sensory_deficit'] ?? 'No hemisensory loss' }}</div>
                        <div><strong>Superficial (Touch/Pain/Temp):</strong> {{ $data['superficial_sensations'] ?? 'Intact' }}</div>
                        <div><strong>Deep (Proprioception/Vibration):</strong> {{ $data['deep_sensations'] ?? 'Intact' }}</div>
                        <div><strong>Cortical (Stereognosis):</strong> {{ $data['cortical_sensations'] ?? 'Intact' }}</div>
                        <div><strong>Dermatome Involved:</strong> {{ $data['dermatome_involved'] ?? 'None' }}</div>
                    </div>

                    <!-- Motor & Reflexes -->
                    <div class="border border-slate-200 rounded-xl p-3.5 space-y-2 text-[11px]">
                        <strong class="block text-purple-900 text-xs uppercase border-b border-slate-200 pb-1 font-black">Motor Examination & Reflexes</strong>
                        <div><strong>MMT (R / L):</strong> {{ $data['neuro_mmt'] ?? 'R: 5/5 | L: 5/5' }}</div>
                        <div><strong>Tone:</strong> {{ $data['neuro_tone'] ?? 'Normal tone' }}</div>
                        <div><strong>Muscle Girth & Fasciculations:</strong> {{ $data['neuro_girth'] ?? 'Symmetric' }}, {{ $data['neuro_fasciculations'] ?? 'Absent' }}</div>
                        <div><strong>Deep Tendon Reflexes (DTR):</strong> {{ $data['deep_tendon_reflexes'] ?? '++ Normal' }}</div>
                        <div><strong>Superficial & Neonatal Reflexes:</strong> {{ $data['superficial_reflexes'] ?? 'Plantar flexor' }}</div>
                    </div>
                </div>

                <!-- Higher Mental Functions -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] grid grid-cols-2 md:grid-cols-5 gap-2">
                    <div><strong>Orientation:</strong> {{ $data['orientation'] ?? 'Time/Place/Person Intact' }}</div>
                    <div><strong>Attention:</strong> {{ $data['alteration'] ?? 'Normal' }}</div>
                    <div><strong>Calculation:</strong> {{ $data['calculation'] ?? 'Normal' }}</div>
                    <div><strong>Speech:</strong> {{ $data['speech'] ?? 'Clear & Fluent' }}</div>
                    <div><strong>Memory:</strong> {{ $data['memory'] ?? 'Intact' }}</div>
                </div>

                <!-- Cranial Nerve Examination I to XII Table -->
                <div class="border border-purple-200 rounded-xl overflow-hidden">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-purple-100 text-purple-900 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-2 pl-3">Cranial Nerve Examination (I - XII)</th>
                                <th class="p-2 w-40">Right (R)</th>
                                <th class="p-2 w-40">Left (L)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">I - Olfactory (Smell)</td>
                                <td class="p-1.5">{{ $data['cn_1_r'] ?? 'Intact' }}</td>
                                <td class="p-1.5">{{ $data['cn_1_l'] ?? 'Intact' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">II - Optic (Acuity / Fields)</td>
                                <td class="p-1.5">{{ $data['cn_2_r'] ?? 'Normal' }}</td>
                                <td class="p-1.5">{{ $data['cn_2_l'] ?? 'Normal' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">III, IV, VI - Oculomotor, Trochlear, Abducens (Eye Movements)</td>
                                <td class="p-1.5">{{ $data['cn_346_r'] ?? 'Full' }}</td>
                                <td class="p-1.5">{{ $data['cn_346_l'] ?? 'Full' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">V - Trigeminal (Sensation & Jaw)</td>
                                <td class="p-1.5">{{ $data['cn_5_r'] ?? 'Intact' }}</td>
                                <td class="p-1.5">{{ $data['cn_5_l'] ?? 'Intact' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">VII - Facial (Facial Symmetries)</td>
                                <td class="p-1.5">{{ $data['cn_7_r'] ?? 'Normal' }}</td>
                                <td class="p-1.5">{{ $data['cn_7_l'] ?? 'Normal' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">VIII - Vestibulocochlear (Acoustic / Balance)</td>
                                <td class="p-1.5">{{ $data['cn_8_r'] ?? 'Normal' }}</td>
                                <td class="p-1.5">{{ $data['cn_8_l'] ?? 'Normal' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">IX, X - Glossopharyngeal, Vagus (Palate / Swallow)</td>
                                <td class="p-1.5">{{ $data['cn_910_r'] ?? 'Normal' }}</td>
                                <td class="p-1.5">{{ $data['cn_910_l'] ?? 'Normal' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">XI - Spinal Accessory (Trapezius / Shrug)</td>
                                <td class="p-1.5">{{ $data['cn_11_r'] ?? 'Normal' }}</td>
                                <td class="p-1.5">{{ $data['cn_11_l'] ?? 'Normal' }}</td>
                            </tr>
                            <tr>
                                <td class="p-1.5 pl-3 font-bold">XII - Hypoglossal (Tongue Protrusion)</td>
                                <td class="p-1.5">{{ $data['cn_12_r'] ?? 'Central' }}</td>
                                <td class="p-1.5">{{ $data['cn_12_l'] ?? 'Central' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Advised Investigations -->
                <div class="p-3 bg-purple-50/50 rounded-xl border border-purple-200 text-[11px]">
                    <strong>Advised Investigations:</strong>
                    <span class="font-semibold text-purple-900">
                        @if(!empty($data['investigations']))
                            {{ implode(', ', (array)$data['investigations']) }}
                        @else
                            MRI Brain / Spine, NCV as indicated
                        @endif
                    </span>
                    @if(!empty($data['investigation_notes']))
                        <p class="text-slate-600 mt-0.5">Notes: {{ $data['investigation_notes'] }}</p>
                    @endif
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
