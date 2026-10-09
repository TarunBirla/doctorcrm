@extends('layouts.app')

@section('title', 'Clinical Assessment & Prescription')

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('prescriptions.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Clinical Assessment & Prescription</h1>
                <p class="text-xs text-slate-500 font-medium">Physiotherapy Evaluation • Rehabilitation Protocol • Digital Rx</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Official Clinic Assessment Form
            </span>
        </div>
    </div>

    <!-- MAIN FORM -->
    <form action="{{ route('prescriptions.store') }}" method="POST" class="space-y-6" id="assessmentForm">
        @csrf

        <!-- 1. CLINIC BRANCH & PATIENT META CARD -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="building-2" class="w-4 h-4 text-blue-600"></i> Clinic Branch & Patient Profile
                    </h3>
                    <p class="text-xs text-slate-500">Select which clinic branch this patient belongs to and doctor details</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Clinic Branch Selection -->
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Clinic Branch <span class="text-rose-500">*</span>
                    </label>
                    <select name="clinic_id" id="clinicSelect" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                        @foreach($clinics as $c)
                            <option value="{{ $c->id }}" {{ (old('clinic_id', optional($selectedPatient)->clinic_id) == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->city }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-blue-600 font-semibold mt-1 flex items-center gap-1">
                        <i data-lucide="map-pin" class="w-3 h-3"></i> Appears in Prescription letterhead header
                    </p>
                </div>

                <!-- Patient Selection -->
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Select Patient <span class="text-rose-500">*</span>
                    </label>
                    <select name="patient_id" id="patientSelect" required onchange="handlePatientChange(this)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                        <option value="">-- Choose Patient --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" 
                                    data-age="{{ $p->age }}" 
                                    data-gender="{{ $p->gender }}"
                                    data-clinic-id="{{ $p->clinic_id }}"
                                    data-occupation="{{ $p->occupation }}"
                                    data-address="{{ $p->address }}"
                                    {{ (old('patient_id', optional($selectedPatient)->id) == $p->id) ? 'selected' : '' }}>
                                {{ $p->patient_id }} - {{ $p->full_name }} ({{ $p->age }}y, {{ $p->gender }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Doctor & Date -->
                <div class="md:col-span-1 grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Doctor <span class="text-rose-500">*</span></label>
                        <select name="doctor_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                            @foreach($doctors as $d)
                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Date <span class="text-rose-500">*</span></label>
                        <input type="date" name="prescription_date" value="{{ date('Y-m-d') }}" required
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Patient Quick Info preview -->
            <div id="patientQuickInfo" class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex flex-wrap items-center justify-between text-xs gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Demographics:</span>
                    <span id="previewDemographics" class="font-bold text-slate-800">
                        {{ $selectedPatient ? "{$selectedPatient->age} yrs, {$selectedPatient->gender}" : "Select a patient to preview" }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Occupation:</span>
                    <span id="previewOccupation" class="font-semibold text-slate-700">
                        {{ $selectedPatient?->occupation ?? '-' }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-slate-400 font-bold uppercase text-[10px]">Address:</span>
                    <span id="previewAddress" class="text-slate-600 truncate max-w-xs">
                        {{ $selectedPatient?->address ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. ASSESSMENT TYPE SELECTOR (2 TYPES) -->
        <div class="bg-gradient-to-r from-blue-900 to-indigo-900 rounded-2xl p-6 text-white shadow-md space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-200">Clinical Evaluation Type</span>
                    <h2 class="text-xl font-black tracking-tight text-white">Select Assessment Protocol</h2>
                </div>
                <div class="inline-flex p-1.5 bg-white/10 backdrop-blur rounded-xl border border-white/20 gap-2">
                    <button type="button" onclick="setAssessmentType('musculoskeletal')" id="tabBtnMusculo"
                            class="px-4 py-2 rounded-lg text-xs font-black transition flex items-center gap-2 bg-white text-blue-900 shadow-sm">
                        <i data-lucide="activity" class="w-4 h-4"></i>
                        <span>1. Musculo Skeletal Assessment</span>
                    </button>
                    <button type="button" onclick="setAssessmentType('neurological')" id="tabBtnNeuro"
                            class="px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 text-white hover:bg-white/10">
                        <i data-lucide="brain" class="w-4 h-4"></i>
                        <span>2. Neurological Assessment</span>
                    </button>
                </div>
            </div>
            <input type="hidden" name="assessment_type" id="assessmentTypeInput" value="musculoskeletal">
            <p id="assessmentTypeDesc" class="text-xs text-blue-100">
                Evaluating Orthopedic spine, joint ROM, MMT grades, VAS pain score, palpation, osteopathic dysfunctions & biomechanics.
            </p>
        </div>

        <!-- 3. PRIMARY CLINICAL DIAGNOSIS -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-black text-slate-800 uppercase tracking-wider">
                    Clinical Diagnosis / Provisional Impression <span class="text-rose-500">*</span>
                </label>
                <span class="text-[11px] text-slate-400 font-medium">Clear physical therapy diagnosis</span>
            </div>
            <input type="text" name="diagnosis_summary" required placeholder="e.g., Cervical Radiculopathy (C5-C6) with Muscle Spasm, Chronic L4-L5 Lumbar Disc Herniation, Hemiplegia Post-Stroke..."
                   value="{{ old('diagnosis_summary') }}"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-blue-50/20">
        </div>

        <!-- ============================================================== -->
        <!-- SECTION A: MUSCULO SKELETAL ASSESSMENT CHART (PHOTO 2 & 4)      -->
        <!-- ============================================================== -->
        <div id="musculoSkeletalSection" class="space-y-6">

            <!-- Card A1: Patient Complaints & Vitals -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="stethoscope" class="w-4 h-4 text-blue-600"></i> Chief Complaints & Vitalsigns
                    </h3>
                    <span class="text-xs font-mono font-bold text-slate-400">Page 1</span>
                </div>

                <!-- C/O -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">C/O (Chief Complaints)</label>
                    <textarea name="assessment_data[chief_complaints]" rows="2" placeholder="e.g. Pain and stiffness in lower back radiating down to right leg since 2 weeks..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                </div>

                <!-- Vitalsigns -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">HR (/min)</label>
                        <input type="text" name="assessment_data[vitals_hr]" placeholder="76 /min"
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">RR (/min)</label>
                        <input type="text" name="assessment_data[vitals_rr]" placeholder="18 /min"
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">Temp (°C / °F)</label>
                        <input type="text" name="assessment_data[vitals_temp]" placeholder="98.4 °F / 37 °C"
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 mb-1">BP (mmHg)</label>
                        <input type="text" name="assessment_data[vitals_bp]" placeholder="120/80"
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    </div>
                </div>

                <!-- H/O (History of) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">H/O (Medical History)</label>
                    <div class="flex flex-wrap gap-4 text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[ho_conditions][]" value="DM" class="rounded text-blue-600"> DM
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[ho_conditions][]" value="HT" class="rounded text-blue-600"> HT
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[ho_conditions][]" value="COPD" class="rounded text-blue-600"> COPD
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[ho_conditions][]" value="Fracture" class="rounded text-blue-600"> Fracture (#)
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[ho_conditions][]" value="Surgery" class="rounded text-blue-600"> Surgery
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[ho_conditions][]" value="Thyroid" class="rounded text-blue-600"> Thyroid
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[ho_conditions][]" value="OT" class="rounded text-blue-600"> OT
                        </label>
                    </div>
                </div>

                <!-- Family, Present, Personal History -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Family History</label>
                        <select name="assessment_data[family_history]" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                            <option value="-ve">-ve (Negative)</option>
                            <option value="+ve">+ve (Positive)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Personal History</label>
                        <input type="text" name="assessment_data[personal_history]" placeholder="Smoking / Alcohol / Sedentary / etc."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Present Medical History (Date/Time)</label>
                        <input type="text" name="assessment_data[present_history]" placeholder="Onset date, gradual / acute..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                </div>

                <!-- VAS PAIN SCALE (0 - 70) -->
                <div class="bg-amber-50/60 p-4 rounded-xl border border-amber-200/60 space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-black text-amber-900 uppercase tracking-wide">Pain Assessment: VAS Scale (0 - 70)</span>
                            <p class="text-[11px] text-amber-700">Visual Analogue Scale rating from no pain (0) to unbearable pain (70)</p>
                        </div>
                        <span id="vasDisplay" class="px-3 py-1 rounded-full bg-amber-600 text-white font-black text-sm shadow-sm">
                            Score: 40
                        </span>
                    </div>
                    <input type="range" name="assessment_data[vas_pain_score]" id="vasSlider" min="0" max="70" step="5" value="40"
                           oninput="document.getElementById('vasDisplay').innerText = 'Score: ' + this.value"
                           class="w-full accent-amber-600 cursor-pointer">
                    <div class="flex justify-between text-[10px] font-bold text-amber-800">
                        <span>0 (No Pain)</span>
                        <span>10</span>
                        <span>20 (Mild)</span>
                        <span>30</span>
                        <span>40 (Moderate)</span>
                        <span>50</span>
                        <span>60 (Severe)</span>
                        <span>70 (Excruciating)</span>
                    </div>
                </div>
            </div>

            <!-- Card A2: On Look (Observation) & On Palpation -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- On Look (Observation) -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-2">
                        <i data-lucide="eye" class="w-4 h-4 text-indigo-600"></i> On Look (Observation)
                    </h3>
                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Gait:</span>
                            <input type="text" name="assessment_data[gait]" placeholder="Normal / Antalgic / Lurching" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Body Built:</span>
                            <input type="text" name="assessment_data[body_built]" placeholder="Ectomorph / Mesomorph / Obese" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Posture:</span>
                            <input type="text" name="assessment_data[posture]" placeholder="Front / Side / Back" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Facial Exp.:</span>
                            <input type="text" name="assessment_data[facial_expression]" placeholder="Relaxed / Painful / Grimacing" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Deformity:</span>
                            <input type="text" name="assessment_data[deformity]" placeholder="Kyphosis / Scoliosis / Valgus" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Muscle:</span>
                            <input type="text" name="assessment_data[muscle_state]" placeholder="Normal / Atrophy / Hypertrophy" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Foot Deformity:</span>
                            <input type="text" name="assessment_data[foot_deformity]" placeholder="Flat Foot / High Arch / Normal" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Skin Color:</span>
                            <input type="text" name="assessment_data[skin_color]" placeholder="Normal / Pallor / Redness" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                    </div>
                </div>

                <!-- On Palpation -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-2">
                        <i data-lucide="hand" class="w-4 h-4 text-emerald-600"></i> On Palpation
                    </h3>
                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Crepitus:</span>
                            <input type="text" name="assessment_data[crepitus]" placeholder="Present / Absent / Patellar" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Local Temp:</span>
                            <input type="text" name="assessment_data[palpation_temp]" placeholder="Normal / Raised / Warm" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Tenderness:</span>
                            <input type="text" name="assessment_data[tenderness]" placeholder="Grade I / II / III / Point tenderness" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Bony Contour:</span>
                            <input type="text" name="assessment_data[bony_contour]" placeholder="Regular / Intact / Prominent" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Musspasm:</span>
                            <input type="text" name="assessment_data[muscle_spasm]" placeholder="Spasm in Paraspinal / Trapezius" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Rigidity (Type):</span>
                            <input type="text" name="assessment_data[rigidity]" placeholder="Cogwheel / Leadpipe / Nil" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Balance / Prop.:</span>
                            <input type="text" name="assessment_data[balance_proprioception]" placeholder="Intact / Impaired" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                        <div class="grid grid-cols-3 items-center gap-2">
                            <span class="font-bold text-slate-600">Vestibular Exam:</span>
                            <input type="text" name="assessment_data[vestibular_exam]" placeholder="Normal / Nystagmus" class="col-span-2 px-3 py-1.5 rounded-lg border border-slate-200 text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card A3: Physical Exam R vs L Table (ROM, MMT, Special Tests, Neural Tests) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="git-branch" class="w-4 h-4 text-blue-600"></i> Clinical Examination (Right vs Left Comparison)
                        </h3>
                        <p class="text-xs text-slate-500">Record bilateral findings matching physical clinical chart</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400">Page 2</span>
                </div>

                <div class="overflow-x-auto text-xs">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 uppercase text-[10px]">
                                <th class="py-2.5 px-3">Examination Parameter</th>
                                <th class="py-2.5 px-3 w-48 text-blue-700">Right (R)</th>
                                <th class="py-2.5 px-3 w-48 text-indigo-700">Left (L)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- ROM -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">ROM (Range of Motion)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[rom_right]" placeholder="e.g. Full / Restricted 40°" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[rom_left]" placeholder="e.g. Full / Pain at end-range" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <!-- MMT Grade -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">MMT Grade (0 - 5)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[mmt_right]" placeholder="e.g. Grade 4/5" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[mmt_left]" placeholder="e.g. Grade 5/5" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <!-- Co-ordination UL -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">Co-ordination - UL (Upper Limb)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[coord_ul_right]" placeholder="Finger-to-nose: Normal" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[coord_ul_left]" placeholder="Normal" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <!-- Co-ordination LL -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">Co-ordination - LL (Lower Limb)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[coord_ll_right]" placeholder="Heel-to-shin: Normal" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[coord_ll_left]" placeholder="Normal" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <!-- Synergy -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">Synergy (UL & LL)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[synergy_right]" placeholder="Flexor/Extensor synergy" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[synergy_left]" placeholder="Normal" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <!-- Special Tests -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">Special Tests (SLR, Faber, McMurray etc.)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[special_tests_right]" placeholder="e.g. SLR +ve at 45°" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[special_tests_left]" placeholder="e.g. SLR -ve at 80°" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <!-- Neural Tension ULTT -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">Neural Tension - ULTT</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[ultt_right]" placeholder="Positive / Negative" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[ultt_left]" placeholder="Negative" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <!-- Neural Tension LLTT -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">Neural Tension - LLTT</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[lltt_right]" placeholder="Positive / Negative" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[lltt_left]" placeholder="Negative" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <!-- For VBI -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">For VBI (Vertebrobasilar Insufficiency)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[vbi_right]" placeholder="Negative / Dizziness" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[vbi_left]" placeholder="Negative" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- LLD, Girth, Spinal Deformity, Osteopathic terms, C-Spine TOS -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">LLD (Limb Length Discrepancy)</label>
                        <input type="text" name="assessment_data[lld]" placeholder="Apparent / True: Equal" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Muscle Girth Measurement</label>
                        <input type="text" name="assessment_data[muscle_girth]" placeholder="R Thigh: 48cm | L Thigh: 47.5cm" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">C - Spine T.O.S. / Deficits</label>
                        <input type="text" name="assessment_data[tos_deficits]" placeholder="TOS -ve, Sensory/Motor intact" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block font-bold text-slate-700 mb-1">Osteopathic Terms / Findings (AS / PI / BA / BP / PL / PR etc.)</label>
                        <input type="text" name="assessment_data[osteopathic_terms]" placeholder="e.g. Sacral torsion, PI Ilium right, C2-C3 posterior rotation" class="w-full px-3 py-2 rounded-lg border border-slate-200">
                    </div>
                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- SECTION B: NEUROLOGICAL ASSESSMENT CHART (PHOTO 1 & 3)          -->
        <!-- ============================================================== -->
        <div id="neurologicalSection" class="space-y-6 hidden">

            <!-- Card B1: Neuro History & Dominance -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="brain" class="w-4 h-4 text-purple-600"></i> Neurological Demographics & C/O
                    </h3>
                    <span class="text-xs font-mono font-bold text-purple-600">Neuro Page 1</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Dominant Side</label>
                        <select name="assessment_data[dominant_side]" class="w-full px-3 py-2.5 text-xs rounded-xl border border-slate-200 font-bold text-slate-800">
                            <option value="Right (R)">Right (R)</option>
                            <option value="Left (L)">Left (L)</option>
                            <option value="Ambidextrous">Ambidextrous</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">C/o (Chief Complaint)</label>
                        <input type="text" name="assessment_data[neuro_complaints]" placeholder="e.g. Sudden weakness in left upper and lower extremity, slurred speech..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">H/o Conditions</label>
                        <input type="text" name="assessment_data[neuro_ho]" placeholder="HT / DM / Thyroidism / CVA..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Level of Consciousness</label>
                        <select name="assessment_data[level_of_consciousness]" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold">
                            <option value="Conscious & Alert">Conscious & Alert</option>
                            <option value="Drowsy / Lethargic">Drowsy / Lethargic</option>
                            <option value="Stuporous">Stuporous</option>
                            <option value="Comatose">Comatose</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">GCS Scale (E / V / M)</label>
                        <input type="text" name="assessment_data[gcs_scale]" placeholder="E4 V5 M6 (Total: 15/15)"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-mono font-bold text-purple-700">
                    </div>
                </div>
            </div>

            <!-- Card B2: Sensory & Motor Examination -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sensory Examination -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-2">
                        <i data-lucide="zap" class="w-4 h-4 text-purple-600"></i> Sensory Examination
                    </h3>
                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Sensory Deficit:</label>
                            <input type="text" name="assessment_data[sensory_deficit]" placeholder="Present / Absent / Hemisensory loss" class="w-full px-3 py-1.5 rounded-lg border border-slate-200">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Superficial Sensations:</label>
                            <input type="text" name="assessment_data[superficial_sensations]" placeholder="Pain, Touch, Temp intact" class="w-full px-3 py-1.5 rounded-lg border border-slate-200">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Deep Sensations:</label>
                            <input type="text" name="assessment_data[deep_sensations]" placeholder="Proprioception, Vibration, Deep pain" class="w-full px-3 py-1.5 rounded-lg border border-slate-200">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Cortical Sensations:</label>
                            <input type="text" name="assessment_data[cortical_sensations]" placeholder="Stereognosis, 2-point discrimination" class="w-full px-3 py-1.5 rounded-lg border border-slate-200">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Dermatome Involved:</label>
                            <input type="text" name="assessment_data[dermatome_involved]" placeholder="e.g. C5-C6 / L4-L5 / None" class="w-full px-3 py-1.5 rounded-lg border border-slate-200">
                        </div>
                    </div>
                </div>

                <!-- Motor Examination & Reflexes -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-2">
                        <i data-lucide="shield" class="w-4 h-4 text-purple-600"></i> Motor Examination & Reflexes
                    </h3>
                    <div class="space-y-3 text-xs">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-bold text-slate-600 mb-1">MMT (R / L):</label>
                                <input type="text" name="assessment_data[neuro_mmt]" placeholder="R: 5/5 | L: 3/5" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 mb-1">Tone (R / L):</label>
                                <input type="text" name="assessment_data[neuro_tone]" placeholder="Normal / Spastic (Grade 1+)" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-bold text-slate-600 mb-1">Muscle Girth:</label>
                                <input type="text" name="assessment_data[neuro_girth]" placeholder="Symmetrical / Wasting" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-600 mb-1">Fasciculations:</label>
                                <input type="text" name="assessment_data[neuro_fasciculations]" placeholder="Absent / Present in calf" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200">
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Myotome Involved:</label>
                            <input type="text" name="assessment_data[myotome_involved]" placeholder="e.g. C7 wrist extensors, L5 extensor hallucis" class="w-full px-3 py-1.5 rounded-lg border border-slate-200">
                        </div>
                        <div class="grid grid-cols-3 gap-2 pt-1 border-t border-slate-100">
                            <div>
                                <label class="block font-bold text-[10px] text-slate-500 uppercase">Superficial Ref.:</label>
                                <input type="text" name="assessment_data[superficial_reflexes]" placeholder="Plantar flexor" class="w-full px-2 py-1 rounded border border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-bold text-[10px] text-slate-500 uppercase">Deep Tendon (DTR):</label>
                                <input type="text" name="assessment_data[deep_tendon_reflexes]" placeholder="Biceps ++, Knee ++" class="w-full px-2 py-1 rounded border border-slate-200 text-xs">
                            </div>
                            <div>
                                <label class="block font-bold text-[10px] text-slate-500 uppercase">Neonatal Ref.:</label>
                                <input type="text" name="assessment_data[neonatal_reflexes]" placeholder="N/A" class="w-full px-2 py-1 rounded border border-slate-200 text-xs">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card B3: Higher Mental Functions & Cranial Nerve Examination I - XII -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4 text-purple-600"></i> Higher Mental & Cranial Nerve Examination (I - XII)
                        </h3>
                        <p class="text-xs text-slate-500">Record functional status and bilateral cranial nerve responses</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-purple-600">Neuro Page 2</span>
                </div>

                <!-- Higher mental functional -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 grid grid-cols-2 md:grid-cols-5 gap-3 text-xs">
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Orientation:</label>
                        <input type="text" name="assessment_data[orientation]" placeholder="Time / Place / Person: Intact" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Alteration / Attn:</label>
                        <input type="text" name="assessment_data[alteration]" placeholder="Attentive" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Calculation:</label>
                        <input type="text" name="assessment_data[calculation]" placeholder="Serial 7s normal" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Speech:</label>
                        <input type="text" name="assessment_data[speech]" placeholder="Clear / Dysarthria" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-600 mb-1">Memory:</label>
                        <input type="text" name="assessment_data[memory]" placeholder="Immediate / Recent intact" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white">
                    </div>
                </div>

                <!-- Cranial Nerve Examination I - XII Table -->
                <div class="overflow-x-auto text-xs">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-purple-50 text-purple-900 font-bold border-b border-purple-200 uppercase text-[10px]">
                                <th class="py-2.5 px-3">Cranial Nerve</th>
                                <th class="py-2.5 px-3 w-48 text-purple-800">Right (R)</th>
                                <th class="py-2.5 px-3 w-48 text-purple-800">Left (L)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">I - Olfactory (Smell)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_1_r]" placeholder="Intact" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_1_l]" placeholder="Intact" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">II - Optic (Visual acuity & fields)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_2_r]" placeholder="Normal" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_2_l]" placeholder="Normal" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">III, IV, VI - Oculomotor, Trochlear, Abducens (Eye movements)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_346_r]" placeholder="Full movements, PERLA" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_346_l]" placeholder="Full movements, PERLA" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">V - Trigeminal (Facial sensation, jaw)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_5_r]" placeholder="Intact" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_5_l]" placeholder="Intact" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">VII - Facial (Facial expressions, taste)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_7_r]" placeholder="Normal / Deviation" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_7_l]" placeholder="Normal / Bell's palsy" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">VIII - Vestibulocochlear (Hearing & balance)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_8_r]" placeholder="Normal" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_8_l]" placeholder="Normal" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">IX, X - Glossopharyngeal, Vagus (Palate, gag, swallow)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_910_r]" placeholder="Normal" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_910_l]" placeholder="Normal" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">XI - Accessory (Sternocleidomastoid & trapezius)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_11_r]" placeholder="Normal shoulder shrug" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_11_l]" placeholder="Normal" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800">XII - Hypoglossal (Tongue movements)</td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_12_r]" placeholder="Central, no deviation" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                                <td class="py-2 px-3"><input type="text" name="assessment_data[cn_12_l]" placeholder="Central" class="w-full px-2 py-1 text-xs rounded border border-slate-200"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Advised Investigations -->
                <div class="pt-4 border-t border-slate-100 space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Advised Investigations</label>
                    <div class="flex flex-wrap gap-4 text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[investigations][]" value="X-ray" class="rounded text-purple-600"> X-ray
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[investigations][]" value="CT Scan" class="rounded text-purple-600"> CT Scan
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[investigations][]" value="MRI" class="rounded text-purple-600"> MRI
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[investigations][]" value="NCV" class="rounded text-purple-600"> NCV
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[investigations][]" value="EMG" class="rounded text-purple-600"> EMG
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[investigations][]" value="USG" class="rounded text-purple-600"> USG
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                            <input type="checkbox" name="assessment_data[investigations][]" value="Colour Doppler" class="rounded text-purple-600"> Colour Doppler
                        </label>
                    </div>
                    <input type="text" name="assessment_data[investigation_notes]" placeholder="Specific region / MRI Cervical Spine / NCV bilateral lower limbs findings..."
                           class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 mt-2">
                </div>

            </div>

        </div>

        <!-- ============================================================== -->
        <!-- 4. PRESCRIBED PHYSIOTHERAPY REHABILITATION & MODALITIES        -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
            <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="dumbbell" class="w-4 h-4 text-emerald-600"></i> Prescribed Physiotherapy Exercises & Modalities
                    </h3>
                    <p class="text-xs text-slate-500">Pick exercises from library or add custom protocols for the patient</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-600">Treatment Plan Duration:</span>
                    <div class="flex items-center gap-1">
                        <input type="number" name="treatment_days" value="7" min="1" max="90"
                               class="w-16 px-2.5 py-1 text-xs font-black text-center rounded-lg border border-slate-200 bg-slate-50">
                        <span class="text-xs font-bold text-slate-500">Days</span>
                    </div>
                </div>
            </div>

            <!-- Modalities Checkbox Grid -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Prescribed Modalities / Electrotherapy</label>
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-5 gap-2.5 text-xs">
                    @php
                        $modalityList = [
                            'IFT (Interferential)', 'TENS Therapy', 'Ultrasound (US)', 'Spinal Traction', 
                            'SWD (Shortwave)', 'Moist Heat Pack', 'Cryo / Ice Pack', 'Cupping Therapy', 
                            'Dry Needling', 'Joint Mobilization', 'Chiropractic Adjustment', 'Laser Therapy'
                        ];
                    @endphp
                    @foreach($modalityList as $mod)
                        <label class="p-2.5 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center gap-2 transition bg-slate-50/50 hover:bg-emerald-50/30">
                            <input type="checkbox" name="modalities_selected[]" value="{{ $mod }}" onchange="updateModalitiesInput()" class="rounded text-emerald-600">
                            <span class="font-semibold text-slate-700 text-[11px]">{{ $mod }}</span>
                        </label>
                    @endforeach
                </div>
                <input type="hidden" name="modalities" id="modalitiesHiddenInput" value="">
            </div>

            <!-- Exercises Quick Selector from Library -->
            <div class="p-4 bg-emerald-50/50 rounded-xl border border-emerald-100 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-xs font-black text-emerald-900 uppercase tracking-wide flex items-center gap-1.5">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Insert from Exercise Library
                    </span>
                    <div class="flex items-center gap-2">
                        <select id="exerciseLibrarySelect" class="px-3 py-1.5 rounded-lg border border-emerald-200 bg-white text-xs font-semibold focus:outline-none">
                            <option value="">-- Choose Exercise from Library --</option>
                            @foreach($exercises as $ex)
                                <option value="{{ $ex->id }}" 
                                        data-name="{{ $ex->name }}"
                                        data-target="{{ $ex->target_body_part }}"
                                        data-sets="{{ $ex->sets ?? '3 Sets' }}"
                                        data-reps="{{ $ex->reps ?? '10 Reps' }}"
                                        data-duration="{{ $ex->duration ?? '5 sec hold' }}"
                                        data-instructions="{{ $ex->instructions }}">
                                    {{ $ex->name }} ({{ $ex->target_body_part ?? 'General' }})
                                </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="insertSelectedExercise()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition">
                            + Add to Protocol
                        </button>
                    </div>
                </div>
            </div>

            <!-- Prescribed Exercises Table -->
            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left" id="exerciseTable">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px]">
                            <th class="py-2.5 px-3">Exercise Name</th>
                            <th class="py-2.5 px-2 w-32">Target Area</th>
                            <th class="py-2.5 px-2 w-24">Sets</th>
                            <th class="py-2.5 px-2 w-24">Reps</th>
                            <th class="py-2.5 px-2 w-28">Hold / Duration</th>
                            <th class="py-2.5 px-3">Instructions / Directions</th>
                            <th class="py-2.5 px-2 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="exerciseRowsContainer">
                        <!-- Initial Default Row -->
                        <tr class="exercise-row">
                            <td class="py-2 px-3">
                                <input type="text" name="prescribed_exercises[0][name]" value="Chin Tucks & Deep Cervical Retraction" placeholder="Exercise Name"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 font-bold focus:outline-none focus:border-blue-500">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="prescribed_exercises[0][target]" value="Cervical Spine" placeholder="Body Part"
                                       class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="prescribed_exercises[0][sets]" value="3 Sets" placeholder="3 Sets"
                                       class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="prescribed_exercises[0][reps]" value="10 Reps" placeholder="10 Reps"
                                       class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="prescribed_exercises[0][duration]" value="5 sec hold" placeholder="5 sec hold"
                                       class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
                            </td>
                            <td class="py-2 px-3">
                                <input type="text" name="prescribed_exercises[0][instructions]" value="Maintain upright posture, gently tuck chin without bending head." placeholder="Specific cues"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
                            </td>
                            <td class="py-2 px-2 text-center">
                                <button type="button" onclick="removeExerciseRow(this)" class="text-slate-400 hover:text-rose-600 transition p-1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" onclick="addCustomExerciseRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> + Add Custom Exercise Row
            </button>
        </div>

        <!-- ============================================================== -->
        <!-- 5. MEDICINES / SUPPLEMENTS (℞ - OPTIONAL)                      -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <span class="font-serif italic font-black text-lg text-blue-900">℞</span> Prescribed Medications & Supplements (Optional)
                    </h3>
                    <p class="text-xs text-slate-500">Add pain relief ointments, muscle relaxants, vitamins, or leave blank if therapy only</p>
                </div>
                <button type="button" onclick="addMedRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs transition border border-blue-200">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Medicine Row
                </button>
            </div>

            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left" id="medTable">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200 text-[10px]">
                            <th class="py-2.5 px-3">Medicine / Gel / Supplement</th>
                            <th class="py-2.5 px-2 w-28">Dosage</th>
                            <th class="py-2.5 px-2 w-28">Frequency</th>
                            <th class="py-2.5 px-2 w-24">Duration</th>
                            <th class="py-2.5 px-2 w-28">Timing</th>
                            <th class="py-2.5 px-3">Instructions</th>
                            <th class="py-2.5 px-2 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="medRowsContainer">
                        <tr class="med-row">
                            <td class="py-2 px-3">
                                <input type="text" name="items[0][medicine_name]" placeholder="e.g. Aceclofenac + Paracetamol, Diclofenac Gel"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-blue-500">
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="items[0][dosage]" placeholder="1 Tab / Local"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
                            </td>
                            <td class="py-2 px-2">
                                <select name="items[0][frequency]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
                                    <option value="1-0-1">1-0-1 (BD)</option>
                                    <option value="1-1-1">1-1-1 (TDS)</option>
                                    <option value="1-0-0">1-0-0 (OD)</option>
                                    <option value="0-0-1">0-0-1 (Night)</option>
                                    <option value="SOS">SOS (As needed)</option>
                                </select>
                            </td>
                            <td class="py-2 px-2">
                                <input type="text" name="items[0][duration]" value="5 Days" placeholder="5 Days"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
                            </td>
                            <td class="py-2 px-2">
                                <select name="items[0][timing]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
                                    <option value="After Food">After Food</option>
                                    <option value="Before Food">Before Food</option>
                                    <option value="External Use">External Use</option>
                                </select>
                            </td>
                            <td class="py-2 px-3">
                                <input type="text" name="items[0][instructions]" placeholder="e.g. Apply gently without hard massage"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
                            </td>
                            <td class="py-2 px-2 text-center">
                                <button type="button" onclick="removeMedRow(this)" class="text-slate-400 hover:text-rose-600 transition p-1">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. ADVICE, PRECAUTIONS & FOLLOW-UP -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2 border-b border-slate-100 pb-2">
                <i data-lucide="clipboard-check" class="w-4 h-4 text-blue-600"></i> Ergonomic Advice & Scheduled Review
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Doctor Advice, Postural & Home Care Guidelines</label>
                    <textarea name="advice" rows="3" placeholder="e.g. Avoid forward bending, use lumbar roll while sitting, apply moist heat for 15 mins twice daily, perform prescribed exercises regularly..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Scheduled Next Review / Follow-up Date</label>
                    <input type="date" name="follow_up_date" value="{{ date('Y-m-d', strtotime('+7 days')) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <p class="text-[11px] text-slate-400 mt-1">Automatically logs in patient follow-up review schedule.</p>
                </div>
            </div>
        </div>

        <!-- 7. ACTIONS -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-xs text-slate-500">
                <span>Please verify clinical examination findings before generating the official prescription.</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('prescriptions.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 transition">
                    Cancel
                </a>
                <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-blue-700 to-indigo-800 hover:from-blue-800 hover:to-indigo-900 text-white text-xs font-black shadow-md hover:shadow-lg transition flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Save & Generate Official Prescription
                </button>
            </div>
        </div>

    </form>
</div>

<script>
// Assessment Type Switcher
function setAssessmentType(type) {
    const input = document.getElementById('assessmentTypeInput');
    const tabMusculo = document.getElementById('tabBtnMusculo');
    const tabNeuro = document.getElementById('tabBtnNeuro');
    const secMusculo = document.getElementById('musculoSkeletalSection');
    const secNeuro = document.getElementById('neurologicalSection');
    const desc = document.getElementById('assessmentTypeDesc');

    input.value = type;

    if (type === 'musculoskeletal') {
        tabMusculo.className = "px-4 py-2 rounded-lg text-xs font-black transition flex items-center gap-2 bg-white text-blue-900 shadow-sm";
        tabNeuro.className = "px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 text-white hover:bg-white/10";
        secMusculo.classList.remove('hidden');
        secNeuro.classList.add('hidden');
        desc.innerText = "Evaluating Orthopedic spine, joint ROM, MMT grades, VAS pain score, palpation, osteopathic dysfunctions & biomechanics.";
    } else {
        tabNeuro.className = "px-4 py-2 rounded-lg text-xs font-black transition flex items-center gap-2 bg-white text-purple-900 shadow-sm";
        tabMusculo.className = "px-4 py-2 rounded-lg text-xs font-bold transition flex items-center gap-2 text-white hover:bg-white/10";
        secNeuro.classList.remove('hidden');
        secMusculo.classList.add('hidden');
        desc.innerText = "Evaluating Neurological deficits, Cranial Nerves (I-XII), Sensory/Motor myotomes/dermatomes, reflexes, GCS & higher mental functions.";
    }
}

// Patient change handler
function handlePatientChange(select) {
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
        const age = opt.getAttribute('data-age');
        const gender = opt.getAttribute('data-gender');
        const clinicId = opt.getAttribute('data-clinic-id');
        const occupation = opt.getAttribute('data-occupation') || '-';
        const address = opt.getAttribute('data-address') || '-';

        document.getElementById('previewDemographics').innerText = `${age} yrs, ${gender}`;
        document.getElementById('previewOccupation').innerText = occupation;
        document.getElementById('previewAddress').innerText = address;

        if (clinicId) {
            const clinicSelect = document.getElementById('clinicSelect');
            if (clinicSelect) {
                clinicSelect.value = clinicId;
            }
        }
    }
}

// Modalities aggregator
function updateModalitiesInput() {
    const checked = Array.from(document.querySelectorAll('input[name="modalities_selected[]"]:checked')).map(cb => cb.value);
    document.getElementById('modalitiesHiddenInput').value = checked.join(', ');
}

// Exercises management
let exIndex = 1;
function insertSelectedExercise() {
    const select = document.getElementById('exerciseLibrarySelect');
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) {
        alert('Please select an exercise from the dropdown library first.');
        return;
    }

    const name = opt.getAttribute('data-name');
    const target = opt.getAttribute('data-target') || 'General';
    const sets = opt.getAttribute('data-sets') || '3 Sets';
    const reps = opt.getAttribute('data-reps') || '10 Reps';
    const duration = opt.getAttribute('data-duration') || '5 sec hold';
    const instructions = opt.getAttribute('data-instructions') || '';

    const container = document.getElementById('exerciseRowsContainer');
    const tr = document.createElement('tr');
    tr.className = 'exercise-row';
    tr.innerHTML = `
        <td class="py-2 px-3">
            <input type="text" name="prescribed_exercises[${exIndex}][name]" value="${name}" required
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 font-bold focus:outline-none focus:border-blue-500">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="prescribed_exercises[${exIndex}][target]" value="${target}"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="prescribed_exercises[${exIndex}][sets]" value="${sets}"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="prescribed_exercises[${exIndex}][reps]" value="${reps}"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="prescribed_exercises[${exIndex}][duration]" value="${duration}"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-3">
            <input type="text" name="prescribed_exercises[${exIndex}][instructions]" value="${instructions}"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2 text-center">
            <button type="button" onclick="removeExerciseRow(this)" class="text-slate-400 hover:text-rose-600 transition p-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    `;
    container.appendChild(tr);
    lucide.createIcons();
    exIndex++;
    select.value = '';
}

function addCustomExerciseRow() {
    const container = document.getElementById('exerciseRowsContainer');
    const tr = document.createElement('tr');
    tr.className = 'exercise-row';
    tr.innerHTML = `
        <td class="py-2 px-3">
            <input type="text" name="prescribed_exercises[${exIndex}][name]" required placeholder="Exercise Name"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 font-bold focus:outline-none focus:border-blue-500">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="prescribed_exercises[${exIndex}][target]" placeholder="Target Area"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="prescribed_exercises[${exIndex}][sets]" value="3 Sets" placeholder="3 Sets"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="prescribed_exercises[${exIndex}][reps]" value="10 Reps" placeholder="10 Reps"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="prescribed_exercises[${exIndex}][duration]" value="5 sec hold" placeholder="5 sec hold"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-3">
            <input type="text" name="prescribed_exercises[${exIndex}][instructions]" placeholder="Instructions"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2 text-center">
            <button type="button" onclick="removeExerciseRow(this)" class="text-slate-400 hover:text-rose-600 transition p-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    `;
    container.appendChild(tr);
    lucide.createIcons();
    exIndex++;
}

function removeExerciseRow(btn) {
    btn.closest('tr').remove();
}

// Medicines management
let medIndex = 1;
function addMedRow() {
    const container = document.getElementById('medRowsContainer');
    const tr = document.createElement('tr');
    tr.className = 'med-row';
    tr.innerHTML = `
        <td class="py-2 px-3">
            <input type="text" name="items[${medIndex}][medicine_name]" placeholder="Medicine Name"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 focus:outline-none focus:border-blue-500">
        </td>
        <td class="py-2 px-2">
            <input type="text" name="items[${medIndex}][dosage]" placeholder="Dosage"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2">
            <select name="items[${medIndex}][frequency]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
                <option value="1-0-1">1-0-1 (BD)</option>
                <option value="1-1-1">1-1-1 (TDS)</option>
                <option value="1-0-0">1-0-0 (OD)</option>
                <option value="0-0-1">0-0-1 (Night)</option>
                <option value="SOS">SOS (As needed)</option>
            </select>
        </td>
        <td class="py-2 px-2">
            <input type="text" name="items[${medIndex}][duration]" value="5 Days" placeholder="Duration"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2">
            <select name="items[${medIndex}][timing]" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200">
                <option value="After Food">After Food</option>
                <option value="Before Food">Before Food</option>
                <option value="External Use">External Use</option>
            </select>
        </td>
        <td class="py-2 px-3">
            <input type="text" name="items[${medIndex}][instructions]" placeholder="Instructions"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200">
        </td>
        <td class="py-2 px-2 text-center">
            <button type="button" onclick="removeMedRow(this)" class="text-slate-400 hover:text-rose-600 transition p-1">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    `;
    container.appendChild(tr);
    lucide.createIcons();
    medIndex++;
}

function removeMedRow(btn) {
    btn.closest('tr').remove();
}
</script>
@endsection
