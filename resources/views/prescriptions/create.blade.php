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

            <!-- Patient Quick Info & Assessment Demographics Preview (Matching Hand-Written Sheets) -->
            <div id="patientQuickInfo" class="p-4 bg-slate-50/80 rounded-xl border border-slate-200/80 grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Name & Demographics:</span>
                    <span id="previewDemographics" class="font-bold text-slate-800 text-sm">
                        {{ $selectedPatient ? "{$selectedPatient->full_name} ({$selectedPatient->age} yrs, {$selectedPatient->gender})" : "Select a patient above" }}
                    </span>
                </div>
                <div>
                    <label class="text-slate-500 font-bold uppercase text-[10px] block mb-1">
                        Affected Side (प्रभावित अंग / साइड) <span class="text-rose-500">*</span>
                    </label>
                    <select name="assessment_data[affected_side]" id="globalAffectedSide" class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white font-black text-slate-800 text-xs focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="Right (R)">Right Side (R)</option>
                        <option value="Left (L)">Left Side (L)</option>
                        <option value="Bilateral (Both)">Bilateral (Both Sides)</option>
                        <option value="Central / Spine">Central / Spine</option>
                    </select>
                </div>
                <div>
                    <label class="text-slate-500 font-bold uppercase text-[10px] block mb-1">
                        Occupation (व्यवसाय)
                    </label>
                    <input type="text" name="assessment_data[occupation]" id="previewOccupationInput" 
                           value="{{ old('assessment_data.occupation', optional($selectedPatient)->occupation) }}" 
                           placeholder="e.g. Desk Job, Teacher, Driver, Heavy Labor..."
                           class="w-full px-3 py-1.5 rounded-lg border border-slate-200 bg-white font-semibold text-slate-700 text-xs focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <span class="text-slate-400 font-bold uppercase text-[10px] block">Address:</span>
                    <span id="previewAddress" class="text-slate-600 block truncate mt-1">
                        {{ $selectedPatient?->address ?? '-' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. ASSESSMENT TYPE SELECTOR (2 TYPES) -->
        <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-indigo-950 rounded-2xl p-6 text-white shadow-lg space-y-4 border border-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-300">Official Clinical Protocol</span>
                    <h2 class="text-xl font-black tracking-tight text-white flex items-center gap-2">
                        <span>Select Clinical Assessment Prescription</span>
                    </h2>
                </div>
                <div class="inline-flex p-1.5 bg-white/10 backdrop-blur rounded-xl border border-white/20 gap-2">
                    <button type="button" onclick="setAssessmentType('musculoskeletal')" id="tabBtnMusculo"
                            class="px-4 py-2.5 rounded-lg text-xs font-black transition flex items-center gap-2 bg-white text-blue-900 shadow-sm">
                        <i data-lucide="bone" class="w-4 h-4 text-blue-600"></i>
                        <span>1. Musculo - Skeletal Assessment (Image 1)</span>
                    </button>
                    <button type="button" onclick="setAssessmentType('neurological')" id="tabBtnNeuro"
                            class="px-4 py-2.5 rounded-lg text-xs font-bold transition flex items-center gap-2 text-white hover:bg-white/10">
                        <i data-lucide="brain" class="w-4 h-4 text-purple-400"></i>
                        <span>2. Neurological Assessment (Images 2 & 3)</span>
                    </button>
                </div>
            </div>
            <input type="hidden" name="assessment_type" id="assessmentTypeInput" value="musculoskeletal">
            <p id="assessmentTypeDesc" class="text-xs text-blue-100 flex items-center gap-2">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-400"></i>
                <span>Evaluation of joint ROM, MMT, Posture, Tenderness, Gait, Aggravating Pain + VAS Scale, Advice & Special Ortho Tests.</span>
            </p>
        </div>

        <!-- 3. PRIMARY CLINICAL DIAGNOSIS -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-3">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="activity" class="w-4 h-4 text-blue-600"></i>
                    <span>Clinical Diagnosis / Provisional Impression <span class="text-rose-500">*</span></span>
                </label>
                <span class="text-[11px] text-slate-400 font-medium">Physical Therapy & Clinical Finding</span>
            </div>
            <input type="text" name="diagnosis_summary" required placeholder="e.g. Frozen Shoulder (Adhesive Capsulitis), Lumbar Radiculopathy L4-L5, Stroke Hemiparesis, Cervical Spondylosis..."
                   value="{{ old('diagnosis_summary') }}"
                   class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-blue-50/20">
        </div>

        <!-- ============================================================== -->
        <!-- SECTION A: MUSCULO - SKELETAL ASSESSMENT (EXACT MATCH IMAGE 1) -->
        <!-- ============================================================== -->
        <div id="musculoSkeletalSection" class="space-y-6">

            <!-- Banner Header -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl p-5 text-white shadow-md flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center font-black text-lg">
                        🦴
                    </div>
                    <div>
                        <h3 class="text-lg font-black tracking-wide uppercase">MUSCULO - SKELETAL ASSESSMENT</h3>
                        <p class="text-xs text-blue-100">Prescription Sheet 1: Joint, Soft Tissue, Biomechanics & Special Orthopedic Tests</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold bg-white/10 px-3 py-1.5 rounded-xl border border-white/20">
                    <span>Clinical Chart: Orthopedic & Spine Evaluation</span>
                </div>
            </div>

            <!-- Card 1: Complaints, Duration, Pain Aggravation + VAS, Associate Factors, Past History -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-600"></i> Chief Complaints & Pain Analysis
                    </h4>
                    <span class="text-[11px] font-mono text-slate-400 font-bold">Parameters: C/O • Duration • Aggravation • VAS</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- CHIEF COMPLAINT -->
                    <div class="md:col-span-2 space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            • CHIEF COMPLAINT <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="assessment_data[chief_complaint]" rows="2" placeholder="e.g. Severe pain and restricted movement in right shoulder radiating to elbow..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium"></textarea>
                    </div>

                    <!-- DURATION -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            • DURATION (अवधि)
                        </label>
                        <input type="text" name="assessment_data[duration]" placeholder="e.g. 5 Days / 3 Weeks / 2 Months"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <div class="flex flex-wrap gap-1.5 pt-1 text-[10px]">
                            <button type="button" onclick="this.closest('.space-y-1.5').querySelector('input').value='3 Days'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">3d</button>
                            <button type="button" onclick="this.closest('.space-y-1.5').querySelector('input').value='1 Week'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">1w</button>
                            <button type="button" onclick="this.closest('.space-y-1.5').querySelector('input').value='2 Weeks'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">2w</button>
                            <button type="button" onclick="this.closest('.space-y-1.5').querySelector('input').value='1 Month'" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">1m</button>
                        </div>
                    </div>
                </div>

                <!-- PAIN AGGRAVATION & VAS SCALE -->
                <div class="p-4 bg-amber-50/60 rounded-xl border border-amber-200/70 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-xs font-black text-amber-950 uppercase tracking-wide flex items-center gap-2">
                                <i data-lucide="flame" class="w-4 h-4 text-amber-600"></i>
                                • PAIN AGGRAVATION (दर्द कब बढ़ता है) + VAS SCALE
                            </span>
                            <p class="text-[11px] text-amber-800">Mark aggravating conditions: Day / Night / Activities and rate pain intensity</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-amber-900">VAS Score:</span>
                            <span id="vasScoreBadge" class="px-3 py-1 rounded-full bg-amber-600 text-white font-black text-sm shadow-sm">
                                4 / 10 (Moderate)
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Aggravating Triggers -->
                        <div class="space-y-2">
                            <span class="text-[11px] font-bold text-slate-700 block">Aggravating factors (as per photo):</span>
                            <div class="flex flex-wrap gap-2.5 text-xs">
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-amber-300 font-bold text-slate-800 cursor-pointer hover:bg-amber-100 transition shadow-xs">
                                    <input type="checkbox" name="assessment_data[pain_aggravation][]" value="Day" class="rounded text-amber-600 focus:ring-amber-500">
                                    <span>☀️ Day (दिन में)</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-amber-300 font-bold text-slate-800 cursor-pointer hover:bg-amber-100 transition shadow-xs">
                                    <input type="checkbox" name="assessment_data[pain_aggravation][]" value="Night" class="rounded text-amber-600 focus:ring-amber-500">
                                    <span>🌙 Night (रात में)</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-amber-300 font-bold text-slate-800 cursor-pointer hover:bg-amber-100 transition shadow-xs">
                                    <input type="checkbox" name="assessment_data[pain_aggravation][]" value="Activities" class="rounded text-amber-600 focus:ring-amber-500">
                                    <span>🏃 Activities (कामकाज / चलने पर)</span>
                                </label>
                            </div>
                            <input type="text" name="assessment_data[pain_aggravation_activities]" placeholder="Specific activities (e.g. forward bending, overhead reach, sitting > 30m)..."
                                   class="w-full px-3 py-2 text-xs rounded-lg border border-amber-200 bg-white placeholder-slate-400 focus:border-amber-500">
                        </div>

                        <!-- Interactive VAS Pain Slider (0 to 10) -->
                        <div class="space-y-2 bg-white p-3 rounded-xl border border-amber-200">
                            <label class="block text-[11px] font-bold text-slate-700">Visual Analogue Scale (0 - 10):</label>
                            <input type="range" name="assessment_data[vas_scale]" id="vasInput" min="0" max="10" step="1" value="4"
                                   oninput="updateVasBadge(this.value)"
                                   class="w-full accent-amber-600 cursor-pointer">
                            <div class="flex justify-between text-[10px] font-bold text-slate-500">
                                <span class="text-emerald-700">0 (No Pain)</span>
                                <span class="text-blue-700">1-3 (Mild)</span>
                                <span class="text-amber-700">4-6 (Moderate)</span>
                                <span class="text-rose-700">7-9 (Severe)</span>
                                <span class="text-red-900 font-black">10 (Worst)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ASSOCIATE FACTORS & PAST HISTORY -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- ASSOCIATE FACTORS -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">
                            • Associate factors (संबद्ध लक्षण)
                        </label>
                        <div class="flex flex-wrap gap-2 text-xs">
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 font-semibold text-slate-700 cursor-pointer hover:bg-blue-50 hover:border-blue-200">
                                <input type="checkbox" name="assessment_data[associate_factors][]" value="Radiating pain" class="rounded text-blue-600">
                                <span>⚡ Radiating pain</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 font-semibold text-slate-700 cursor-pointer hover:bg-blue-50 hover:border-blue-200">
                                <input type="checkbox" name="assessment_data[associate_factors][]" value="Swelling" class="rounded text-blue-600">
                                <span>💧 Swelling</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 font-semibold text-slate-700 cursor-pointer hover:bg-blue-50 hover:border-blue-200">
                                <input type="checkbox" name="assessment_data[associate_factors][]" value="Numbness" class="rounded text-blue-600">
                                <span>🪡 Numbness (सुन्नपन)</span>
                            </label>
                        </div>
                        <input type="text" name="assessment_data[associate_factors_notes]" placeholder="Additional symptoms (tingling, pins & needles, weakness)..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200">
                    </div>

                    <!-- PAST HISTORY -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">
                            • PAST history (पुराना इतिहास / पूर्व बीमारी)
                        </label>
                        <textarea name="assessment_data[past_history]" rows="2" placeholder="e.g. Previous fall / injury 6 months ago, DM, HTN, prior spine surgery..."
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500"></textarea>
                    </div>
                </div>
            </div>

            <!-- Card 2: Observation (Posture, Tenderness, Gait) & Advice -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="eye" class="w-4 h-4 text-indigo-600"></i> Observation (निरीक्षण) & Advice
                    </h4>
                    <span class="text-[11px] font-mono text-slate-400 font-bold">Posture • Tenderness • Gait • Advice</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- Posture -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            • Posture (मुद्रा)
                        </label>
                        <input type="text" name="assessment_data[observation_posture]" id="postureInput" 
                               placeholder="Rounded shoulder / shoulder elevation etc."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold text-slate-800">
                        <div class="flex flex-wrap gap-1 text-[10px]">
                            <button type="button" onclick="appendInputVal('postureInput', 'Rounded shoulder')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600">+ Rounded shoulder</button>
                            <button type="button" onclick="appendInputVal('postureInput', 'Shoulder elevation')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600">+ Shoulder elevation</button>
                            <button type="button" onclick="appendInputVal('postureInput', 'Forward head')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600">+ Forward head</button>
                            <button type="button" onclick="appendInputVal('postureInput', 'Normal alignment')" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600">+ Normal</button>
                        </div>
                    </div>

                    <!-- Tenderness -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            • Tenderness (स्पर्श वेदना / दर्द का स्थान)
                        </label>
                        <input type="text" name="assessment_data[observation_tenderness]" placeholder="Grade I/II localized at supraspinatus / L5-S1..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold text-slate-800">
                        <p class="text-[10px] text-slate-400">Specify site and tenderness grade (I, II, III)</p>
                    </div>

                    <!-- GAIT -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">
                            • GAIT (चाल)
                        </label>
                        <input type="text" name="assessment_data[observation_gait]" id="gaitInput" placeholder="Antalgic / Trendelenburg / Normal..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold text-slate-800">
                        <div class="flex flex-wrap gap-1 text-[10px]">
                            <button type="button" onclick="document.getElementById('gaitInput').value='Normal'" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600">Normal</button>
                            <button type="button" onclick="document.getElementById('gaitInput').value='Antalgic gait'" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600">Antalgic</button>
                            <button type="button" onclick="document.getElementById('gaitInput').value='Trendelenburg gait'" class="px-1.5 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-slate-600">Trendelenburg</button>
                        </div>
                    </div>
                </div>

                <!-- Advice (X-ray, MRI) & MMT -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-3 border-t border-slate-100">
                    <!-- ADVICE -->
                    <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-800 block">• Advice (जाँच सलाह - X-ray, MRI)</span>
                        <div class="flex flex-wrap gap-3 text-xs">
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 font-bold text-slate-800 cursor-pointer hover:bg-blue-50">
                                <input type="checkbox" name="assessment_data[advice_imaging][]" value="X-ray" class="rounded text-blue-600">
                                <span>📷 X-ray</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 font-bold text-slate-800 cursor-pointer hover:bg-blue-50">
                                <input type="checkbox" name="assessment_data[advice_imaging][]" value="MRI" class="rounded text-blue-600">
                                <span>🧲 MRI</span>
                            </label>
                        </div>
                        <input type="text" name="assessment_data[advice_notes]" placeholder="Specify region (e.g. X-ray Right Shoulder AP/Axillary, MRI LS Spine)..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white">
                    </div>

                    <!-- MMT -->
                    <div class="space-y-2 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <span class="text-xs font-bold text-slate-800 block">• MMT (Manual Muscle Testing: Grade 0 - 5)</span>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-blue-700 uppercase">Right Side (R)</label>
                                <input type="text" name="assessment_data[mmt_right]" placeholder="e.g. Grade 4/5"
                                       class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-indigo-700 uppercase">Left Side (L)</label>
                                <input type="text" name="assessment_data[mmt_left]" placeholder="e.g. Grade 5/5"
                                       class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                            </div>
                        </div>
                        <input type="text" name="assessment_data[mmt_notes]" placeholder="Muscle group details (e.g. Deltoid, Rotator cuff, Quadriceps 4/5)..."
                               class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white">
                    </div>
                </div>
            </div>

            <!-- Card 3: RANGE OF MOTION (Flexion, Abduction, Extension, ER, IR) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="border-b border-slate-100 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="compass" class="w-4 h-4 text-blue-600"></i> • RANGE OF MOTION (गति का दायरा - ROM)
                        </h4>
                        <p class="text-[11px] text-slate-500">Record degrees / restriction for Flexion, Abduction, Extension, External Rotation (ER), Internal Rotation (IR)</p>
                    </div>
                    <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded bg-blue-50 text-blue-800 border border-blue-200">
                        Bilateral ROM Degrees
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-700 font-bold border-b border-slate-200 uppercase text-[10px]">
                                <th class="py-2.5 px-3">Movement Parameter</th>
                                <th class="py-2.5 px-3 w-48 text-blue-800">Right (R)</th>
                                <th class="py-2.5 px-3 w-48 text-indigo-800">Left (L)</th>
                                <th class="py-2.5 px-3">Clinical End-Feel / Pain Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Flexion -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Flexion
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_flexion_r]" placeholder="e.g. 120° / Restricted" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_flexion_l]" placeholder="e.g. 180° Full" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_flexion_notes]" placeholder="Pain at end-range" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200">
                                </td>
                            </tr>
                            <!-- Abduction -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Abduction
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_abduction_r]" placeholder="e.g. 90° Painful arc" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_abduction_l]" placeholder="e.g. 180° Full" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_abduction_notes]" placeholder="Painful arc 60°-120°" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200">
                                </td>
                            </tr>
                            <!-- Extension -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> Extension
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_extension_r]" placeholder="e.g. 30° / Full" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_extension_l]" placeholder="e.g. 45° Full" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_extension_notes]" placeholder="Normal" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200">
                                </td>
                            </tr>
                            <!-- ER -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span> ER (External Rotation)
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_er_r]" placeholder="e.g. 40° Restricted" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_er_l]" placeholder="e.g. 70° Full" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_er_notes]" placeholder="Capsular pattern limitation" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200">
                                </td>
                            </tr>
                            <!-- IR -->
                            <tr>
                                <td class="py-2 px-3 font-bold text-slate-800 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span> IR (Internal Rotation)
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_ir_r]" placeholder="e.g. Reach to L5" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_ir_l]" placeholder="e.g. Reach to T8" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200 font-medium">
                                </td>
                                <td class="py-2 px-3">
                                    <input type="text" name="assessment_data[rom_ir_notes]" placeholder="Stiffness noted" class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card 4: SPECIAL TESTS (All tests from Image 1) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i> • SPECIAL TEST (विशेष नैदानिक परीक्षण - Image 1)
                        </h4>
                        <p class="text-[11px] text-slate-500">Shoulder • Knee • Hip • Spine • Cervical (Click to toggle -ve Negative / +ve Positive)</p>
                    </div>
                    <span class="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        Image 1 Standard
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
                    
                    <!-- 1. Shoulder Tests -->
                    <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/30 space-y-3">
                        <div class="flex items-center justify-between border-b border-blue-100 pb-2">
                            <span class="font-black text-blue-900 uppercase text-[11px]">1. Shoulder (कंधा)</span>
                            <span class="text-[10px] font-bold text-blue-600">Rotator / Impingement</span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Drop arm:</label>
                                <select name="assessment_data[st_drop_arm]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative (Normal)</option>
                                    <option value="+ve Positive">+ve Positive (Tear / Rupture)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Impingement:</label>
                                <select name="assessment_data[st_impingement]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative</option>
                                    <option value="+ve Positive (Neer / Hawkins)">+ve Positive (Neer / Hawkins)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Knee Tests -->
                    <div class="p-4 rounded-xl border border-indigo-200 bg-indigo-50/30 space-y-3">
                        <div class="flex items-center justify-between border-b border-indigo-100 pb-2">
                            <span class="font-black text-indigo-900 uppercase text-[11px]">2. Knee (घुटना)</span>
                            <span class="text-[10px] font-bold text-indigo-600">Cruciate / Meniscus</span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Drawer ant. (Anterior Drawer):</label>
                                <select name="assessment_data[st_drawer_ant]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative (ACL Intact)</option>
                                    <option value="+ve Positive">+ve Positive (ACL Laxity)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">McMurray:</label>
                                <select name="assessment_data[st_mcmurray]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative (Meniscus Intact)</option>
                                    <option value="+ve Positive">+ve Positive (Meniscal Click/Pain)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Hip Tests -->
                    <div class="p-4 rounded-xl border border-teal-200 bg-teal-50/30 space-y-3">
                        <div class="flex items-center justify-between border-b border-teal-100 pb-2">
                            <span class="font-black text-teal-900 uppercase text-[11px]">3. Hip (कूल्हा)</span>
                            <span class="text-[10px] font-bold text-teal-600">SI Joint / Abductor</span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">FABER (Patrick's test):</label>
                                <select name="assessment_data[st_faber]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative</option>
                                    <option value="+ve Positive (SI / Hip Pathology)">+ve Positive (SI / Hip Pathology)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Trendelenburg:</label>
                                <select name="assessment_data[st_trendelenburg]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative (Gluteus Medius Normal)</option>
                                    <option value="+ve Positive">+ve Positive (Abductor Weakness)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Spine Tests -->
                    <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/30 space-y-3">
                        <div class="flex items-center justify-between border-b border-amber-100 pb-2">
                            <span class="font-black text-amber-900 uppercase text-[11px]">4. Spine (रीढ़ की हड्डी)</span>
                            <span class="text-[10px] font-bold text-amber-600">Dural Tension</span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">SLR (Straight Leg Raise):</label>
                                <select name="assessment_data[st_slr]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative (80°-90° Normal)">-ve Negative (80°-90° Normal)</option>
                                    <option value="+ve Positive (< 60° Radicular Pain)">+ve Positive (< 60° Radicular Pain)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Slump:</label>
                                <select name="assessment_data[st_slump]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative</option>
                                    <option value="+ve Positive (Neural Tension)">+ve Positive (Neural Tension)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 5. Cervical Tests -->
                    <div class="p-4 rounded-xl border border-rose-200 bg-rose-50/30 space-y-3">
                        <div class="flex items-center justify-between border-b border-rose-100 pb-2">
                            <span class="font-black text-rose-900 uppercase text-[11px]">5. Cervical (गर्दन)</span>
                            <span class="text-[10px] font-bold text-rose-600">Radiculopathy</span>
                        </div>
                        <div class="space-y-2.5">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Compression:</label>
                                <select name="assessment_data[st_cervical_compression]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative</option>
                                    <option value="+ve Positive (Radicular Pain)">+ve Positive (Radicular Pain)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Spurling:</label>
                                <select name="assessment_data[st_spurling]" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                                    <option value="-ve Negative">-ve Negative</option>
                                    <option value="+ve Positive (Foraminal Compression)">+ve Positive (Foraminal Compression)</option>
                                    <option value="Not Tested">Not Tested</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Extra Special Test Notes -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                        <span class="font-black text-slate-800 uppercase text-[11px] block">Additional Ortho Findings</span>
                        <textarea name="assessment_data[special_test_notes]" rows="3" placeholder="Additional special test observations (e.g. Apley scratch test, Lachman test, Finkelstein, Ober test)..."
                                  class="w-full px-2.5 py-2 text-xs rounded-lg border border-slate-200 bg-white"></textarea>
                    </div>

                </div>
            </div>

        </div>

        <!-- ============================================================== -->
        <!-- SECTION B: NEUROLOGICAL ASSESSMENT (EXACT MATCH IMAGES 2 & 3)   -->
        <!-- ============================================================== -->
        <div id="neurologicalSection" class="space-y-6 hidden">

            <!-- Banner Header -->
            <div class="bg-gradient-to-r from-purple-800 via-indigo-900 to-slate-900 rounded-2xl p-5 text-white shadow-md flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center font-black text-lg">
                        🧠
                    </div>
                    <div>
                        <h3 class="text-lg font-black tracking-wide uppercase">NEUROLOGICAL ASSESSMENT</h3>
                        <p class="text-xs text-purple-200">Prescription Sheets 2 & 3: History, Observation, Senses, Motor/Reflexes & Neuro-Rehabilitation Plan</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold bg-white/10 px-3 py-1.5 rounded-xl border border-white/20">
                    <span>Clinical Chart: Neurological & Rehabilitation</span>
                </div>
            </div>

            <!-- Card B1: Chief Complaint & Full History (Image 2) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h4 class="text-xs font-black text-purple-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="clipboard" class="w-4 h-4 text-purple-600"></i> • Chief Complain & History (Image 2)
                    </h4>
                    <span class="text-[11px] font-mono text-purple-600 font-bold">Image 2: Top Section</span>
                </div>

                <!-- Chief Complaint -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        • Chief complain <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="assessment_data[neuro_chief_complaint]" rows="2" placeholder="e.g. Inability to move right arm and leg, deviation of mouth, difficulty walking since 10 days post stroke..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500"></textarea>
                </div>

                <!-- History 4-Grid matching photo: past H/O, Surgical H/O, Family H/O, associate factors -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 bg-purple-50/30 p-4 rounded-xl border border-purple-100">
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-purple-950">• past H/O:</label>
                        <input type="text" name="assessment_data[neuro_past_ho]" placeholder="HTN, DM, CVA/Stroke, TIA, Seizures..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-purple-950">• Surgical H/O:</label>
                        <input type="text" name="assessment_data[neuro_surgical_ho]" placeholder="Craniotomy, Burr hole, Spine surgery..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-purple-950">• Family H/O:</label>
                        <input type="text" name="assessment_data[neuro_family_ho]" placeholder="Hereditary, Stroke, Parkinson's -ve / +ve..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-bold text-purple-950">• associate factors:</label>
                        <input type="text" name="assessment_data[neuro_associate_factors]" placeholder="Dysphagia, Aphasia, Incontinence, Tremors..."
                               class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white font-semibold">
                    </div>
                </div>
            </div>

            <!-- Card B2: Observation & External Appliance (Image 2) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h4 class="text-xs font-black text-purple-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="eye" class="w-4 h-4 text-purple-600"></i> • Observation & External Appliance (Image 2)
                    </h4>
                    <span class="text-[11px] font-mono text-purple-600 font-bold">Posture • Gait • Deformity • Aids</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <!-- posture -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">• posture (मुद्रा):</label>
                        <input type="text" name="assessment_data[neuro_posture]" placeholder="Hemiplegic posture, slumped sitting, asymmetry..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold">
                    </div>

                    <!-- Gait -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">• Gait (चाल):</label>
                        <input type="text" name="assessment_data[neuro_gait]" id="neuroGaitInput" placeholder="Hemiplegic / Circumductory / Ataxic / Scissoring..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold">
                        <div class="flex flex-wrap gap-1 text-[10px]">
                            <button type="button" onclick="document.getElementById('neuroGaitInput').value='Hemiplegic (Circumduction) gait'" class="px-1.5 py-0.5 rounded bg-purple-50 text-purple-800 font-bold">Hemiplegic</button>
                            <button type="button" onclick="document.getElementById('neuroGaitInput').value='Ataxic (Broad-based) gait'" class="px-1.5 py-0.5 rounded bg-purple-50 text-purple-800 font-bold">Ataxic</button>
                            <button type="button" onclick="document.getElementById('neuroGaitInput').value='Non-ambulatory (Wheelchair)'" class="px-1.5 py-0.5 rounded bg-purple-50 text-purple-800 font-bold">Wheelchair</button>
                        </div>
                    </div>

                    <!-- Deformity -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">• Deformity (विकृति):</label>
                        <input type="text" name="assessment_data[neuro_deformity]" placeholder="Claw hand, Foot drop, Wrist flexion contracture..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold">
                    </div>
                </div>

                <!-- External Appliance Sub-tree (Image 2) -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-wide block">
                        • External appliance (बाहरी सहायक उपकरण):
                    </span>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <!-- Functional aids -->
                        <div class="bg-white p-3.5 rounded-lg border border-slate-200 space-y-2">
                            <span class="font-bold text-blue-900 block flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                functional aids - walking aids / catheter
                            </span>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assessment_data[neuro_functional_aids][]" value="Walking Cane / Stick" class="rounded text-blue-600">
                                    <span>Walking Cane / Stick</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assessment_data[neuro_functional_aids][]" value="Walker" class="rounded text-blue-600">
                                    <span>Walker</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assessment_data[neuro_functional_aids][]" value="Wheelchair" class="rounded text-blue-600">
                                    <span>Wheelchair</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assessment_data[neuro_functional_aids][]" value="Catheter" class="rounded text-blue-600">
                                    <span>Catheter (Foley's)</span>
                                </label>
                            </div>
                            <input type="text" name="assessment_data[neuro_functional_aids_notes]" placeholder="Additional functional aid details..."
                                   class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200">
                        </div>

                        <!-- Protective aids -->
                        <div class="bg-white p-3.5 rounded-lg border border-slate-200 space-y-2">
                            <span class="font-bold text-purple-900 block flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                protective aids - brace / prosthetics
                            </span>
                            <div class="flex flex-wrap gap-2 text-xs">
                                <label class="inline-flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assessment_data[neuro_protective_aids][]" value="AFO (Ankle Foot Orthosis)" class="rounded text-purple-600">
                                    <span>AFO (Ankle Foot Orthosis)</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assessment_data[neuro_protective_aids][]" value="Cock-up Splint" class="rounded text-purple-600">
                                    <span>Cock-up / Hand Splint</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assessment_data[neuro_protective_aids][]" value="Cervical Collar" class="rounded text-purple-600">
                                    <span>Cervical Collar</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer font-semibold text-slate-700">
                                    <input type="checkbox" name="assessment_data[neuro_protective_aids][]" value="Prosthetics" class="rounded text-purple-600">
                                    <span>Prosthetics</span>
                                </label>
                            </div>
                            <input type="text" name="assessment_data[neuro_protective_aids_notes]" placeholder="Brace specifications / wearing schedule..."
                                   class="w-full px-2.5 py-1.5 text-xs rounded border border-slate-200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card B3: Examination (Consciousness, Orientation, Behaviour, Memory, Special Senses - Image 2) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h4 class="text-xs font-black text-purple-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="brain-circuit" class="w-4 h-4 text-purple-600"></i> • Examination (Consciousness & Higher Functions - Image 2)
                    </h4>
                    <span class="text-[11px] font-mono text-purple-600 font-bold">Image 2: Bottom Section</span>
                </div>

                <!-- Consciousness Level (Alert, Lethargy, Confusion, Coma) -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">
                        • consciousness level (चेतना का स्तर):
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer font-bold text-slate-800 hover:border-purple-300">
                            <input type="radio" name="assessment_data[neuro_consciousness_level]" value="Alert" checked class="text-purple-600">
                            <span>✅ Alert (जागृत)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer font-bold text-slate-800 hover:border-purple-300">
                            <input type="radio" name="assessment_data[neuro_consciousness_level]" value="Lethargy" class="text-purple-600">
                            <span>💤 Lethargy (सुस्त)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer font-bold text-slate-800 hover:border-purple-300">
                            <input type="radio" name="assessment_data[neuro_consciousness_level]" value="Confusion" class="text-purple-600">
                            <span>🌀 Confusion (भ्रमित)</span>
                        </label>
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer font-bold text-slate-800 hover:border-purple-300">
                            <input type="radio" name="assessment_data[neuro_consciousness_level]" value="Coma" class="text-purple-600">
                            <span>🛑 Coma (अचेत)</span>
                        </label>
                    </div>
                </div>

                <!-- Orientation, Behaviour, Memory -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">• Orientation (समय, स्थान, व्यक्ति):</label>
                        <input type="text" name="assessment_data[neuro_orientation]" placeholder="Oriented to Time, Place & Person (Intact)"
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">• Behaviour (व्यवहार):</label>
                        <input type="text" name="assessment_data[neuro_behaviour]" placeholder="Cooperative, Calm, Irritable, Agitated..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-700">• memory (स्मरण शक्ति):</label>
                        <input type="text" name="assessment_data[neuro_memory]" placeholder="Immediate, Recent & Remote memory intact..."
                               class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 font-semibold">
                    </div>
                </div>

                <!-- special sense (Vision, Hearing, Smell, Taste, Tactile) -->
                <div class="p-4 bg-purple-50/40 rounded-xl border border-purple-200/70 space-y-3">
                    <span class="text-xs font-black text-purple-950 uppercase tracking-wide block">
                        • special sense (विशेष संवेदनाएं - Image 2):
                    </span>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 text-xs">
                        <div class="bg-white p-2.5 rounded-lg border border-purple-100 space-y-1">
                            <span class="font-bold text-slate-700 text-[11px] block">👁️ Vision</span>
                            <select name="assessment_data[neuro_sense_vision]" class="w-full px-2 py-1 text-xs rounded border border-slate-200 font-medium">
                                <option value="Normal">Normal</option>
                                <option value="Blurry / Diplopia">Blurry / Diplopia</option>
                                <option value="Visual Field Deficit">Field Deficit</option>
                                <option value="Impaired">Impaired</option>
                            </select>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-purple-100 space-y-1">
                            <span class="font-bold text-slate-700 text-[11px] block">👂 Hearing</span>
                            <select name="assessment_data[neuro_sense_hearing]" class="w-full px-2 py-1 text-xs rounded border border-slate-200 font-medium">
                                <option value="Normal">Normal</option>
                                <option value="Impaired (Right)">Impaired (R)</option>
                                <option value="Impaired (Left)">Impaired (L)</option>
                                <option value="Tinnitus">Tinnitus</option>
                            </select>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-purple-100 space-y-1">
                            <span class="font-bold text-slate-700 text-[11px] block">👃 Smell</span>
                            <select name="assessment_data[neuro_sense_smell]" class="w-full px-2 py-1 text-xs rounded border border-slate-200 font-medium">
                                <option value="Normal">Normal</option>
                                <option value="Anosmia">Anosmia (Loss)</option>
                                <option value="Impaired">Impaired</option>
                            </select>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-purple-100 space-y-1">
                            <span class="font-bold text-slate-700 text-[11px] block">👅 Taste</span>
                            <select name="assessment_data[neuro_sense_taste]" class="w-full px-2 py-1 text-xs rounded border border-slate-200 font-medium">
                                <option value="Normal">Normal</option>
                                <option value="Impaired">Impaired</option>
                                <option value="Loss of Taste">Loss of Taste</option>
                            </select>
                        </div>
                        <div class="bg-white p-2.5 rounded-lg border border-purple-100 space-y-1">
                            <span class="font-bold text-slate-700 text-[11px] block">✋ Tactile</span>
                            <select name="assessment_data[neuro_sense_tactile]" class="w-full px-2 py-1 text-xs rounded border border-slate-200 font-medium">
                                <option value="Normal">Normal</option>
                                <option value="Hypoesthesia (Reduced)">Hypoesthesia</option>
                                <option value="Hyperesthesia">Hyperesthesia</option>
                                <option value="Numb / Absent">Numb / Absent</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card B4: Sensory, Motor, Reflex, Cortical Reflex, Investigation, Treatment Plan (Image 3) -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                    <h4 class="text-xs font-black text-purple-900 uppercase tracking-wider flex items-center gap-2">
                        <i data-lucide="zap" class="w-4 h-4 text-purple-600"></i> • Sensory, Motor, Reflex & Investigation (Image 3 - Page 2)
                    </h4>
                    <span class="text-[11px] font-mono text-purple-600 font-bold">Image 3 Standard</span>
                </div>

                <!-- Sensory Examination -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        • Sensory examination (संवेदी परीक्षण):
                    </label>
                    <textarea name="assessment_data[neuro_sensory_exam]" rows="2" placeholder="Superficial sensations (Touch, Pain, Temp), Deep sensations (Proprioception, Vibration), Cortical sensations intact / impaired in affected side..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs"></textarea>
                </div>

                <!-- Motor Examination (ROM, MMT, TONE) -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-wide block">
                        • Motor examination:
                    </span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">- ROM (Range of Motion):</label>
                            <input type="text" name="assessment_data[neuro_motor_rom]" placeholder="Full / Restricted / Spastic resistance in flexors..."
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-semibold">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">- MMT (Grade 0 - 5):</label>
                            <input type="text" name="assessment_data[neuro_motor_mmt]" placeholder="R: 5/5 | L: 2/5 (Shoulder 2/5, Hand 1/5)..."
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-semibold">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">- TONE (मांसपेशियों का टोन):</label>
                            <input type="text" name="assessment_data[neuro_motor_tone]" placeholder="Hypertonia / Spasticity Grade 1+ (Modified Ashworth) / Normal / Flaccid..."
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-semibold">
                        </div>
                    </div>
                </div>

                <!-- Reflex (DTR & Synergic pattern) -->
                <div class="p-4 bg-purple-50/40 rounded-xl border border-purple-200 space-y-3">
                    <span class="text-xs font-black text-purple-950 uppercase tracking-wide block">
                        • Reflex & Pattern:
                    </span>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="space-y-1">
                            <label class="block font-bold text-purple-900">- DTR (Deep Tendon Reflexes):</label>
                            <input type="text" name="assessment_data[neuro_reflex_dtr]" placeholder="Biceps (+++ Exaggerated), Knee (+++), Ankle Clonus positive..."
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-semibold">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-purple-900">- pattern (Synergic Pattern):</label>
                            <div class="flex flex-wrap gap-2 pt-0.5">
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-purple-200 font-bold text-purple-950 cursor-pointer">
                                    <input type="checkbox" name="assessment_data[neuro_synergic_pattern][]" value="Flexors synergic pattern" class="rounded text-purple-600">
                                    <span>Flexors synergic pattern</span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-purple-200 font-bold text-purple-950 cursor-pointer">
                                    <input type="checkbox" name="assessment_data[neuro_synergic_pattern][]" value="Extensor synergic pattern" class="rounded text-purple-600">
                                    <span>Extensor synergic pattern</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cortical level reflex (Balance, Equilibrium, Coordination) -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-wide block">
                        • Cortical level reflex:
                    </span>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">- Balance (संतुलन):</label>
                            <input type="text" name="assessment_data[neuro_cortical_balance]" placeholder="Static sitting balance intact, standing impaired, Berg scale..."
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-semibold">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">- Equilibrium:</label>
                            <input type="text" name="assessment_data[neuro_cortical_equilibrium]" placeholder="Equilibrium reactions delayed on affected side..."
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-semibold">
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-700">- Coordination:</label>
                            <input type="text" name="assessment_data[neuro_cortical_coordination]" placeholder="Finger to nose dysmetria, heel to shin impaired..."
                                   class="w-full px-3 py-2 rounded-lg border border-slate-200 bg-white font-semibold">
                        </div>
                    </div>
                </div>

                <!-- Investigation (CT, MRI, EMG, NCV) -->
                <div class="p-4 bg-indigo-50/40 rounded-xl border border-indigo-200 space-y-3">
                    <span class="text-xs font-black text-indigo-950 uppercase tracking-wide block">
                        • Investigation (जाँच - CT, MRI, EMG, NCV):
                    </span>
                    <div class="flex flex-wrap gap-3 text-xs">
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-indigo-200 font-bold text-indigo-950 cursor-pointer shadow-xs">
                            <input type="checkbox" name="assessment_data[neuro_investigation][]" value="CT" class="rounded text-indigo-600">
                            <span>🧠 CT Scan</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-indigo-200 font-bold text-indigo-950 cursor-pointer shadow-xs">
                            <input type="checkbox" name="assessment_data[neuro_investigation][]" value="MRI" class="rounded text-indigo-600">
                            <span>🧲 MRI Brain / Spine</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-indigo-200 font-bold text-indigo-950 cursor-pointer shadow-xs">
                            <input type="checkbox" name="assessment_data[neuro_investigation][]" value="EMG" class="rounded text-indigo-600">
                            <span>⚡ EMG</span>
                        </label>
                        <label class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-indigo-200 font-bold text-indigo-950 cursor-pointer shadow-xs">
                            <input type="checkbox" name="assessment_data[neuro_investigation][]" value="NCV" class="rounded text-indigo-600">
                            <span>📡 NCV (Nerve Conduction)</span>
                        </label>
                    </div>
                    <input type="text" name="assessment_data[neuro_investigation_notes]" placeholder="Key investigation findings (e.g. Infarct in Left MCA territory, NCV axonal neuropathy)..."
                           class="w-full px-3 py-2 text-xs rounded-lg border border-slate-200 bg-white">
                </div>

                <!-- Treatment plan -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800">
                        • Treatment plan (पुनर्वास व उपचार योजना - Image 3):
                    </label>
                    <textarea name="assessment_data[neuro_treatment_plan]" rows="3" placeholder="Neurodevelopmental therapy (NDT), Bobath approach, PNF patterns, gait training, spasticity management, functional electrical stimulation..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium"></textarea>
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

            <!-- Prescribed Exercises Table -->
            <div class="overflow-x-auto text-xs rounded-xl border border-slate-200">
                <table class="w-full text-left" id="exerciseTable">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 uppercase font-bold border-b border-slate-200 text-[10px]">
                            <th class="py-2.5 px-3 min-w-[260px]">Exercise Name (Library / Custom)</th>
                            <th class="py-2.5 px-2 w-32">Target Area</th>
                            <th class="py-2.5 px-2 w-24">Sets</th>
                            <th class="py-2.5 px-2 w-24">Reps</th>
                            <th class="py-2.5 px-2 w-28">Hold / Duration</th>
                            <th class="py-2.5 px-3 min-w-[200px]">Instructions / Directions</th>
                            <th class="py-2.5 px-2 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="exerciseRowsContainer">
                        <!-- Initial Default Row -->
                        <tr class="exercise-row">
                            <td class="py-2 px-3">
                                <div class="space-y-1.5">
                                    <select class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-emerald-300 bg-emerald-50/40 font-bold text-slate-800 focus:outline-none focus:border-emerald-600 ex-dropdown" onchange="onPrescriptionExerciseChange(this)">
                                        <option value="">-- Choose from Exercise Library --</option>
                                        @foreach($exercises as $ex)
                                            <option value="{{ $ex->id }}" 
                                                    data-name="{{ $ex->name }}"
                                                    data-target="{{ $ex->target_body_part ?? 'General' }}"
                                                    data-sets="{{ $ex->sets ?? '3 Sets' }}"
                                                    data-reps="{{ $ex->reps ?? '10 Reps' }}"
                                                    data-duration="{{ $ex->duration ?? '5 sec hold' }}"
                                                    data-instructions="{{ $ex->instructions }}"
                                                    {{ $loop->first ? 'selected' : '' }}>
                                                {{ $ex->name }} ({{ $ex->target_body_part ?? 'General' }})
                                            </option>
                                        @endforeach
                                        <option value="custom">✏️ Custom / Other Exercise (Type Manual)</option>
                                    </select>
                                    <input type="text" name="prescribed_exercises[0][name]" value="{{ $exercises->first()?->name ?? 'Chin Tucks & Deep Cervical Retraction' }}" placeholder="Exercise Name"
                                           class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-200 font-bold focus:outline-none focus:border-emerald-500 bg-white ex-name-input">
                                </div>
                            </td>
                            <td class="py-2 px-2 align-top pt-2.5">
                                <input type="text" name="prescribed_exercises[0][target]" value="{{ $exercises->first()?->target_body_part ?? 'Cervical Spine' }}" placeholder="Body Part"
                                       class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-target-input">
                            </td>
                            <td class="py-2 px-2 align-top pt-2.5">
                                <input type="text" name="prescribed_exercises[0][sets]" value="{{ $exercises->first()?->sets ?? '3 Sets' }}" placeholder="3 Sets"
                                       class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-sets-input">
                            </td>
                            <td class="py-2 px-2 align-top pt-2.5">
                                <input type="text" name="prescribed_exercises[0][reps]" value="{{ $exercises->first()?->reps ?? '10 Reps' }}" placeholder="10 Reps"
                                       class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-reps-input">
                            </td>
                            <td class="py-2 px-2 align-top pt-2.5">
                                <input type="text" name="prescribed_exercises[0][duration]" value="{{ $exercises->first()?->duration ?? '5 sec hold' }}" placeholder="5 sec hold"
                                       class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-duration-input">
                            </td>
                            <td class="py-2 px-3 align-top pt-2.5">
                                <input type="text" name="prescribed_exercises[0][instructions]" value="{{ $exercises->first()?->instructions ?? 'Maintain upright posture, gently tuck chin without bending head.' }}" placeholder="Specific cues"
                                       class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-instructions-input">
                            </td>
                            <td class="py-2 px-2 text-center align-top pt-2.5">
                                <button type="button" onclick="removeExerciseRow(this)" title="Delete Row" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between pt-1">
                <button type="button" onclick="addPrescriptionExerciseRow()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                    <i data-lucide="plus" class="w-4 h-4"></i> + Add Another Exercise Row
                </button>
                <span class="text-[11px] text-slate-400 font-medium">Select exercise from dropdown to auto-fill sets, reps & instructions</span>
            </div>
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

// VAS Score Badge Updater
function updateVasBadge(val) {
    const badge = document.getElementById('vasScoreBadge');
    if (!badge) return;
    let label = 'No Pain';
    let colorClass = 'bg-emerald-600';
    if (val == 0) {
        label = 'No Pain';
        colorClass = 'bg-emerald-600';
    } else if (val <= 3) {
        label = 'Mild Pain';
        colorClass = 'bg-blue-600';
    } else if (val <= 6) {
        label = 'Moderate Pain';
        colorClass = 'bg-amber-600';
    } else if (val <= 9) {
        label = 'Severe Pain';
        colorClass = 'bg-rose-600';
    } else {
        label = 'Worst Possible Pain';
        colorClass = 'bg-red-800';
    }
    badge.className = `px-3 py-1 rounded-full text-white font-black text-sm shadow-sm ${colorClass}`;
    badge.innerText = `${val} / 10 (${label})`;
}

// Quick Chip Appender for Inputs
function appendInputVal(inputId, text) {
    const input = document.getElementById(inputId);
    if (!input) return;
    if (input.value.trim() === '') {
        input.value = text;
    } else if (!input.value.includes(text)) {
        input.value += ', ' + text;
    }
}

// Patient change handler
function handlePatientChange(select) {
    const opt = select.options[select.selectedIndex];
    if (opt && opt.value) {
        const text = opt.text;
        const age = opt.getAttribute('data-age');
        const gender = opt.getAttribute('data-gender');
        const clinicId = opt.getAttribute('data-clinic-id');
        const occupation = opt.getAttribute('data-occupation') || '';
        const address = opt.getAttribute('data-address') || '-';

        const demoElem = document.getElementById('previewDemographics');
        if (demoElem) demoElem.innerText = `${text}`;

        const occInput = document.getElementById('previewOccupationInput');
        if (occInput) occInput.value = occupation;

        const addrElem = document.getElementById('previewAddress');
        if (addrElem) addrElem.innerText = address;

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
const rxExerciseCatalog = @json($exercises);

function escapeRxHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function getRxExerciseOptionsHtml(selectedIndex = -1) {
    let html = '<option value="">-- Choose from Exercise Library --</option>';
    rxExerciseCatalog.forEach((ex, idx) => {
        const isSel = (selectedIndex === idx) ? 'selected' : '';
        html += `<option value="${ex.id}" 
            data-name="${escapeRxHtml(ex.name)}" 
            data-target="${escapeRxHtml(ex.target_body_part || 'General')}" 
            data-sets="${escapeRxHtml(ex.sets || '3 Sets')}" 
            data-reps="${escapeRxHtml(ex.reps || '10 Reps')}" 
            data-duration="${escapeRxHtml(ex.duration || '5 sec hold')}" 
            data-instructions="${escapeRxHtml(ex.instructions || '')}" ${isSel}>
            ${escapeRxHtml(ex.name)} (${escapeRxHtml(ex.target_body_part || 'General')})
        </option>`;
    });
    html += '<option value="custom">✏️ Custom / Other Exercise (Type Manual)</option>';
    return html;
}

function onPrescriptionExerciseChange(selectEl) {
    const row = selectEl.closest('.exercise-row');
    if (!row) return;
    const nameInput = row.querySelector('.ex-name-input');
    const targetInput = row.querySelector('.ex-target-input');
    const setsInput = row.querySelector('.ex-sets-input');
    const repsInput = row.querySelector('.ex-reps-input');
    const durationInput = row.querySelector('.ex-duration-input');
    const instructionsInput = row.querySelector('.ex-instructions-input');

    if (selectEl.value === 'custom') {
        if (nameInput) {
            nameInput.value = '';
            nameInput.placeholder = 'Type custom exercise name...';
            nameInput.focus();
        }
        return;
    }

    const opt = selectEl.options[selectEl.selectedIndex];
    if (!opt || !selectEl.value) {
        return;
    }

    const name = opt.getAttribute('data-name') || '';
    const target = opt.getAttribute('data-target') || 'General';
    const sets = opt.getAttribute('data-sets') || '3 Sets';
    const reps = opt.getAttribute('data-reps') || '10 Reps';
    const duration = opt.getAttribute('data-duration') || '5 sec hold';
    const instructions = opt.getAttribute('data-instructions') || '';

    if (nameInput) nameInput.value = name;
    if (targetInput) targetInput.value = target;
    if (setsInput) setsInput.value = sets;
    if (repsInput) repsInput.value = reps;
    if (durationInput) durationInput.value = duration;
    if (instructionsInput) instructionsInput.value = instructions;
}

let exIndex = 1;
function addPrescriptionExerciseRow() {
    const container = document.getElementById('exerciseRowsContainer');
    const tr = document.createElement('tr');
    tr.className = 'exercise-row border-b border-slate-100 hover:bg-slate-50/50';
    tr.innerHTML = `
        <td class="py-2.5 px-3">
            <div class="space-y-1.5">
                <select class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-emerald-300 bg-emerald-50/40 font-bold text-slate-800 focus:outline-none focus:border-emerald-600 ex-dropdown" onchange="onPrescriptionExerciseChange(this)">
                    ${getRxExerciseOptionsHtml(-1)}
                </select>
                <input type="text" name="prescribed_exercises[${exIndex}][name]" value="" placeholder="Exercise Name"
                       class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-200 font-bold focus:outline-none focus:border-emerald-500 bg-white ex-name-input">
            </div>
        </td>
        <td class="py-2.5 px-2 align-top pt-2.5">
            <input type="text" name="prescribed_exercises[${exIndex}][target]" value="General" placeholder="Target Area"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-target-input">
        </td>
        <td class="py-2.5 px-2 align-top pt-2.5">
            <input type="text" name="prescribed_exercises[${exIndex}][sets]" value="3 Sets" placeholder="3 Sets"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-sets-input">
        </td>
        <td class="py-2.5 px-2 align-top pt-2.5">
            <input type="text" name="prescribed_exercises[${exIndex}][reps]" value="10 Reps" placeholder="10 Reps"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-reps-input">
        </td>
        <td class="py-2.5 px-2 align-top pt-2.5">
            <input type="text" name="prescribed_exercises[${exIndex}][duration]" value="5 sec hold" placeholder="5 sec hold"
                   class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-duration-input">
        </td>
        <td class="py-2.5 px-3 align-top pt-2.5">
            <input type="text" name="prescribed_exercises[${exIndex}][instructions]" placeholder="Instructions"
                   class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-instructions-input">
        </td>
        <td class="py-2.5 px-2 text-center align-top pt-2.5">
            <button type="button" onclick="removeExerciseRow(this)" title="Delete Row" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
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
