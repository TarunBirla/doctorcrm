@extends('layouts.app')

@section('title', 'Doctor Consultation Room - ' . $patient->full_name)
@section('breadcrumb', 'Consultation / New Session')
@section('page_title', 'Doctor Clinical Consultation')

@section('content')
<div class="space-y-6">

    <!-- TOP PATIENT QUICK BAR -->
    <div class="card-custom p-4 bg-white border-l-4 border-l-blue-600 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm">
                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="font-bold text-slate-900 text-base">{{ $patient->full_name }}</h3>
                    <span class="text-xs font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md">{{ $patient->patient_id }}</span>
                    @if($patient->blood_group)
                        <span class="text-xs font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md">{{ $patient->blood_group }}</span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ $patient->age }} yrs • {{ $patient->gender }} • Ph: {{ $patient->mobile }}
                    @if($appointment)
                        • <strong class="text-indigo-700">Token #{{ $appointment->token_number }}</strong> ({{ $appointment->appointment_type }})
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
            @if($patient->outstanding_balance > 0)
                <div class="px-3 py-1.5 bg-rose-50 text-rose-700 font-bold rounded-xl border border-rose-200">
                    Pending Due: ₹{{ number_format($patient->outstanding_balance, 2) }}
                </div>
            @endif
            <a href="{{ route('patients.show', $patient->id) }}" target="_blank" class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl font-semibold flex items-center gap-1.5">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                <span>Open Full History</span>
            </a>
        </div>
    </div>

    <!-- MAIN TWO-COLUMN WORKSPACE -->
    <form action="{{ route('consultations.store') }}" method="POST" id="consultationForm">
        @csrf
        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
        <input type="hidden" name="doctor_id" value="{{ $doctor->id ?? 1 }}">
        @if($appointment)
            <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- LEFT PANE: PATIENT CLINICAL HISTORY & PREVIOUS DIAGNOSES (4 COLS) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- CRITICAL ALLERGIES & KNOWN CONDITIONS -->
                <div class="card-custom p-5 bg-white space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-rose-500"></i>
                        <span>Medical Alerts</span>
                    </h4>

                    <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-900">
                        <strong class="block font-bold">Allergies:</strong>
                        {{ $patient->medicalHistory->allergies ?? 'No known allergies reported.' }}
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                        <strong class="block font-bold text-slate-900">Chronic Conditions:</strong>
                        {{ $patient->medicalHistory->conditions ?? 'None logged.' }}
                    </div>

                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800">
                        <strong class="block font-bold text-slate-900">Current Regular Medicines:</strong>
                        {{ $patient->medicalHistory->current_medications ?? 'None.' }}
                    </div>
                </div>

                <!-- PREVIOUS VISIT INTELLIGENT REVISIT CARRY-FORWARD -->
                @if(!empty($previousVisit))
                    <div class="card-custom p-5 bg-white space-y-3 border-blue-200">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-xs uppercase tracking-wider text-blue-800 flex items-center gap-1.5">
                                <i data-lucide="history" class="w-4 h-4 text-blue-600"></i>
                                <span>Previous Visit ({{ $previousVisit->visit_date->format('d M Y') }})</span>
                            </h4>
                            <button type="button" onclick="copyPreviousVisitData()" class="text-[11px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded hover:bg-blue-100">
                                Copy to Current
                            </button>
                        </div>

                        <div class="text-xs space-y-2">
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Diagnosis:</span>
                                <strong class="text-slate-900" id="prevDiagText">{{ $previousVisit->diagnosis_summary }}</strong>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Chief Complaint:</span>
                                <p class="text-slate-600" id="prevComplaintText">{{ $previousVisit->chief_complaint }}</p>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] font-bold uppercase">Treatment Plan:</span>
                                <p class="text-slate-600" id="prevTreatmentText">{{ $previousVisit->treatment_plan }}</p>
                            </div>
                        </div>

                        @if($previousVisit->prescriptions->first())
                            @php $pRx = $previousVisit->prescriptions->first(); @endphp
                            <div class="pt-2 border-t border-slate-100 text-xs">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase mb-1">Previous Rx:</span>
                                <ul class="list-disc list-inside text-slate-700 space-y-0.5">
                                    @foreach($pRx->items as $pItem)
                                        <li>{{ $pItem->medicine_name }} ({{ $pItem->dosage }} - {{ $pItem->frequency }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- PREVIOUS MEDICAL REPORTS -->
                <div class="card-custom p-5 bg-white space-y-3">
                    <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <i data-lucide="file-text" class="w-4 h-4 text-purple-600"></i>
                        <span>Recent Lab Reports ({{ $patient->reports->count() }})</span>
                    </h4>
                    <div class="space-y-2 text-xs">
                        @forelse($patient->reports->take(3) as $rep)
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-100">
                                <span class="font-bold text-slate-900 block">{{ $rep->report_name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $rep->report_type }} • {{ $rep->report_date->format('d M') }}</span>
                            </div>
                        @empty
                            <p class="text-slate-400 text-center py-2">No uploaded reports</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- RIGHT PANE: CONSULTATION & PRESCRIPTION BUILDER (8 COLS) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- VISIT INFO & CHIEF COMPLAINT -->
                <div class="card-custom p-6 bg-white space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="font-bold text-slate-900 text-sm">Consultation Assessment</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400">Visit Type:</span>
                            <select name="visit_type" class="px-2.5 py-1 text-xs border border-slate-200 rounded-lg bg-slate-50 font-bold text-slate-700">
                                <option value="New" {{ $appointment && $appointment->appointment_type === 'new' ? 'selected' : '' }}>New Consultation</option>
                                <option value="Follow-up" {{ $appointment && $appointment->appointment_type === 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                                <option value="Revisit" {{ $appointment && $appointment->appointment_type === 'revisit' ? 'selected' : '' }}>Revisit</option>
                                <option value="Emergency" {{ $appointment && $appointment->appointment_type === 'emergency' ? 'selected' : '' }}>Emergency</option>
                            </select>
                            <input type="date" name="visit_date" value="{{ now()->toDateString() }}" class="px-2 py-1 text-xs border border-slate-200 rounded-lg bg-slate-50 font-semibold">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Chief Complaint *</label>
                            <textarea name="chief_complaint" id="chief_complaint" rows="3" required placeholder="e.g. Fever with chills, persistent dry cough for 4 days..." 
                                      class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">{{ $appointment->reason ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Symptoms & Clinical History</label>
                            <textarea name="symptoms" id="symptoms" rows="3" placeholder="Duration, onset, aggravating/relieving factors..." 
                                      class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none"></textarea>
                        </div>
                    </div>

                    <!-- VITALS SECTION (WITH AUTOMATIC BMI CALCULATION) -->
                    <div>
                        <label class="block font-bold text-xs uppercase tracking-wider text-slate-500 mb-2">Vitals Examination</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3 text-xs">
                            <div>
                                <label class="text-[10px] text-slate-400 block font-semibold">BP Sys</label>
                                <input type="number" name="bp_sys" id="bp_sys" placeholder="120" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-center font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block font-semibold">BP Dia</label>
                                <input type="number" name="bp_dia" id="bp_dia" placeholder="80" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-center font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block font-semibold">Pulse (bpm)</label>
                                <input type="number" name="pulse" id="pulse" placeholder="72" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-center font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block font-semibold">Temp (°F)</label>
                                <input type="number" step="0.1" name="temperature" id="temperature" placeholder="98.6" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-center font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block font-semibold">Weight (kg)</label>
                                <input type="number" step="0.1" name="weight" id="v_weight" oninput="calcBMI()" placeholder="75.0" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-center font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block font-semibold">Height (cm)</label>
                                <input type="number" name="height" id="v_height" oninput="calcBMI()" placeholder="170" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-center font-bold">
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-400 block font-semibold">SpO2 (%)</label>
                                <input type="number" name="spo2" id="spo2" placeholder="99" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-center font-bold">
                            </div>
                        </div>
                        <div class="mt-2 text-[11px] text-slate-500 flex items-center gap-2">
                            <span>Calculated BMI: <strong id="calculatedBmi" class="text-blue-700">-</strong></span>
                        </div>
                    </div>

                    <!-- DIAGNOSIS -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Clinical Diagnosis *</label>
                        <input type="text" name="diagnosis_summary" id="diagnosis_summary" required placeholder="e.g. Acute Viral Bronchitis, Essential Hypertension" 
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 font-bold text-slate-900 outline-none text-xs">
                        <div class="flex flex-wrap items-center gap-1.5 mt-2">
                            <span class="text-[10px] text-slate-400">Quick add:</span>
                            @foreach(($diagnosesCatalog ?? collect())->take(5) as $dc)
                                <button type="button" onclick="setDiagnosis('{{ $dc->name }}')" class="px-2 py-0.5 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-700 text-[10px] text-slate-600 transition">
                                    + {{ $dc->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- CLINICAL NOTES & TREATMENT PLAN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Clinical Observations & Findings</label>
                            <textarea name="clinical_notes" id="clinical_notes" rows="2" placeholder="Chest clear, bilateral air entry equal, no pallor..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none"></textarea>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Treatment Plan & Dietary Advice</label>
                            <textarea name="treatment_plan" id="treatment_plan" rows="2" placeholder="Increase fluid intake, steam inhalation twice daily, low sodium diet..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- PHYSIOTHERAPY REHABILITATION & EXERCISE PROTOCOL BUILDER -->
                <div class="card-custom p-6 bg-white space-y-5 border-l-4 border-l-emerald-600">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="p-1 rounded-lg bg-emerald-100 text-emerald-800">
                                    <i data-lucide="dumbbell" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-bold text-slate-900 text-sm">Physiotherapy Rehabilitation Protocol</h3>
                                <span class="text-[10px] bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                    Physiotherapist Core
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">Prescribe targeted therapeutic exercises, treatment days, and modalities</p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-xl">
                                <span class="font-semibold text-slate-600">Protocol:</span>
                                <select name="assessment_type" class="bg-transparent font-bold text-slate-800 outline-none text-xs">
                                    <option value="musculoskeletal">🦴 Musculoskeletal</option>
                                    <option value="neurological">🧠 Neurological</option>
                                </select>
                            </div>
                            <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 px-2.5 py-1 rounded-xl">
                                <span class="font-semibold text-slate-600">Duration:</span>
                                <input type="number" name="treatment_days" value="7" min="1" max="180" class="w-12 bg-transparent text-center font-bold text-blue-700 outline-none text-xs">
                                <span class="font-bold text-slate-500">Days</span>
                            </div>
                        </div>
                    </div>

                    <!-- Modalities Checkboxes -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Prescribed Modalities / Electrotherapy</label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2 text-xs">
                            @php
                                $cModalities = ['IFT', 'TENS', 'Ultrasound (US)', 'Spinal Traction', 'Moist Heat', 'Cryo Pack', 'Cupping', 'Dry Needling', 'Mobilization', 'Chiropractic', 'SWD', 'Laser'];
                            @endphp
                            @foreach($cModalities as $cMod)
                                <label class="p-2 rounded-xl border border-slate-200 hover:border-emerald-500 cursor-pointer flex items-center gap-1.5 bg-slate-50/50 hover:bg-emerald-50/40 transition">
                                    <input type="checkbox" name="consultation_modalities[]" value="{{ $cMod }}" onchange="updateConsultModalities()" class="rounded text-emerald-600">
                                    <span class="font-semibold text-slate-700 text-[11px]">{{ $cMod }}</span>
                                </label>
                            @endforeach
                        </div>
                        <input type="hidden" name="modalities" id="consultModalitiesHidden" value="">
                    </div>

                    <!-- Prescribed Physiotherapy Exercises Table -->
                    <div class="border-t border-slate-100 pt-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                                    <i data-lucide="activity" class="w-4 h-4 text-emerald-600"></i> Prescribed Rehabilitation Exercises
                                </label>
                                <p class="text-[11px] text-slate-400">Select exercise from dropdown to auto-fill sets, reps, duration & instructions, or type custom</p>
                            </div>
                        </div>

                        <div class="overflow-x-auto text-xs rounded-xl border border-slate-200">
                            <table class="w-full text-left" id="consultExerciseTable">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-600 uppercase font-bold border-b border-slate-200 text-[10px]">
                                        <th class="py-2.5 px-3 min-w-[260px]">Exercise Name (Library / Custom)</th>
                                        <th class="py-2.5 px-2 w-32">Target Area</th>
                                        <th class="py-2.5 px-2 w-24">Sets</th>
                                        <th class="py-2.5 px-2 w-24">Reps</th>
                                        <th class="py-2.5 px-2 w-28">Hold / Duration</th>
                                        <th class="py-2.5 px-3 min-w-[200px]">Instructions</th>
                                        <th class="py-2.5 px-2 w-10 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100" id="consultExerciseRowsContainer">
                                    <tr class="consult-ex-row">
                                        <td class="py-2 px-3">
                                            <div class="space-y-1.5">
                                                <select class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-emerald-300 bg-emerald-50/40 font-bold text-slate-800 focus:outline-none focus:border-emerald-600 ex-dropdown" onchange="onExerciseChange(this)">
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
                                                <input type="text" name="prescribed_exercises[0][name]" value="{{ $exercises->first()?->name ?? 'Chin Tucks & Deep Cervical Retraction' }}" placeholder="Exercise Name" class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-200 font-bold outline-none focus:border-emerald-500 bg-white ex-name-input">
                                            </div>
                                        </td>
                                        <td class="py-2 px-2 align-top pt-2.5">
                                            <input type="text" name="prescribed_exercises[0][target]" value="{{ $exercises->first()?->target_body_part ?? 'Cervical Spine' }}" placeholder="Area" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-target-input">
                                        </td>
                                        <td class="py-2 px-2 align-top pt-2.5">
                                            <input type="text" name="prescribed_exercises[0][sets]" value="{{ $exercises->first()?->sets ?? '3 Sets' }}" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-sets-input">
                                        </td>
                                        <td class="py-2 px-2 align-top pt-2.5">
                                            <input type="text" name="prescribed_exercises[0][reps]" value="{{ $exercises->first()?->reps ?? '10 Reps' }}" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-reps-input">
                                        </td>
                                        <td class="py-2 px-2 align-top pt-2.5">
                                            <input type="text" name="prescribed_exercises[0][duration]" value="{{ $exercises->first()?->duration ?? '5 sec hold' }}" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-duration-input">
                                        </td>
                                        <td class="py-2 px-3 align-top pt-2.5">
                                            <input type="text" name="prescribed_exercises[0][instructions]" value="{{ $exercises->first()?->instructions ?? 'Maintain upright posture, gently tuck chin straight back.' }}" placeholder="Directions" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-instructions-input">
                                        </td>
                                        <td class="py-2 px-2 text-center align-top pt-2.5">
                                            <button type="button" onclick="this.closest('.consult-ex-row').remove()" title="Delete Row" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <button type="button" onclick="addConsultExerciseRow()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition">
                                <i data-lucide="plus" class="w-4 h-4"></i> + Add Another Exercise Row
                            </button>
                            <span class="text-[11px] text-slate-400 font-medium">Select exercise from dropdown to auto-fill sets, reps & instructions</span>
                        </div>
                    </div>

                <!-- PRESCRIPTION MEDICATION BUILDER (OPTIONAL) -->
                <div class="card-custom p-6 bg-white space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Rx Medication Prescription (Optional)</h3>
                            <p class="text-xs text-slate-400">Add pain relief gels, muscle relaxants, or vitamins if prescribed</p>
                        </div>
                        <button type="button" onclick="addMedicineRow()" class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-xl text-xs font-bold hover:bg-blue-100 flex items-center gap-1">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Add Medicine</span>
                        </button>
                    </div>

                    <div id="medicinesContainer" class="space-y-3">
                        <!-- Medicine Item 1 -->
                        <div class="p-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl space-y-2.5 medicine-row">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 text-xs">
                                <div class="md:col-span-4">
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Medicine Name</label>
                                    <input type="text" name="medicines[0][name]" placeholder="e.g. Tab Paracetamol 650mg / Gel (Optional)" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-white outline-none font-bold">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Dosage</label>
                                    <input type="text" name="medicines[0][dosage]" placeholder="650 mg" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-white outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Frequency</label>
                                    <select name="medicines[0][frequency]" class="w-full px-2 py-1.5 border border-slate-200 rounded-xl bg-white outline-none">
                                        <option value="1-0-1">1-0-1 (Twice)</option>
                                        <option value="1-0-0">1-0-0 (Morning)</option>
                                        <option value="0-0-1">0-0-1 (Night)</option>
                                        <option value="1-1-1">1-1-1 (Thrice)</option>
                                        <option value="SOS">SOS (When needed)</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Duration</label>
                                    <input type="text" name="medicines[0][duration]" value="5 Days" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-white outline-none">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Timing</label>
                                    <select name="medicines[0][timing]" class="w-full px-2 py-1.5 border border-slate-200 rounded-xl bg-white outline-none">
                                        <option value="After Food">After Food</option>
                                        <option value="Before Food">Before Food</option>
                                        <option value="With Food">With Food</option>
                                        <option value="Empty Stomach">Empty Stomach</option>
                                        <option value="At Bedtime">At Bedtime</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <input type="text" name="medicines[0][instructions]" placeholder="Special instructions e.g. Take with warm water" class="flex-1 px-2.5 py-1 border border-slate-200 rounded-lg bg-white outline-none text-[11px]">
                                <button type="button" onclick="this.closest('.medicine-row').remove()" class="text-rose-500 hover:text-rose-700 p-1">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- General Advice & Follow-up Date -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-100 text-xs">
                        <div class="md:col-span-2">
                            <label class="block font-semibold text-slate-700 mb-1">General Advice / Instructions for Patient</label>
                            <input type="text" name="advice" value="Complete the full course. Review if fever persists beyond 3 days." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Next Follow-up Date</label>
                            <input type="date" name="follow_up_date" value="{{ now()->addDays(7)->toDateString() }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        </div>
                    </div>
                </div>

                <!-- SAVE CONSULTATION CTA BAR -->
                <div class="card-custom p-5 bg-white flex flex-wrap items-center justify-between gap-4">
                    <div class="text-xs text-slate-500">
                        Saving will create permanent Visit & Prescription records and mark the queue token completed.
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('queue.index') }}" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Complete Consultation & Print Rx</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    let medIndex = 1;
    function addMedicineRow() {
        const container = document.getElementById('medicinesContainer');
        const div = document.createElement('div');
        div.className = 'p-3.5 bg-slate-50/70 border border-slate-200 rounded-2xl space-y-2.5 medicine-row';
        div.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 text-xs">
                <div class="md:col-span-4">
                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Medicine Name *</label>
                    <input type="text" name="medicines[${medIndex}][name]" placeholder="e.g. Cap Pantoprazole 40mg" required class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-white outline-none font-bold">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Dosage</label>
                    <input type="text" name="medicines[${medIndex}][dosage]" placeholder="40 mg" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-white outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Frequency</label>
                    <select name="medicines[${medIndex}][frequency]" class="w-full px-2 py-1.5 border border-slate-200 rounded-xl bg-white outline-none">
                        <option value="1-0-1">1-0-1 (Twice)</option>
                        <option value="1-0-0">1-0-0 (Morning)</option>
                        <option value="0-0-1">0-0-1 (Night)</option>
                        <option value="1-1-1">1-1-1 (Thrice)</option>
                        <option value="SOS">SOS (When needed)</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Duration</label>
                    <input type="text" name="medicines[${medIndex}][duration]" value="5 Days" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-xl bg-white outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="text-[10px] font-semibold text-slate-500 block mb-1">Timing</label>
                    <select name="medicines[${medIndex}][timing]" class="w-full px-2 py-1.5 border border-slate-200 rounded-xl bg-white outline-none">
                        <option value="After Food">After Food</option>
                        <option value="Before Food">Before Food</option>
                        <option value="With Food">With Food</option>
                        <option value="Empty Stomach">Empty Stomach</option>
                        <option value="At Bedtime">At Bedtime</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-2 text-xs">
                <input type="text" name="medicines[${medIndex}][instructions]" placeholder="Instructions" class="flex-1 px-2.5 py-1 border border-slate-200 rounded-lg bg-white outline-none text-[11px]">
                <button type="button" onclick="this.closest('.medicine-row').remove()" class="text-rose-500 hover:text-rose-700 p-1">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        `;
        container.appendChild(div);
        lucide.createIcons();
        medIndex++;
    }

    const exerciseCatalog = @json($exercises);

    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function getExerciseOptionsHtml(selectedIndex = -1) {
        let html = '<option value="">-- Choose from Exercise Library --</option>';
        exerciseCatalog.forEach((ex, idx) => {
            const isSel = (selectedIndex === idx) ? 'selected' : '';
            html += `<option value="${ex.id}" 
                data-name="${escapeHtml(ex.name)}" 
                data-target="${escapeHtml(ex.target_body_part || 'General')}" 
                data-sets="${escapeHtml(ex.sets || '3 Sets')}" 
                data-reps="${escapeHtml(ex.reps || '10 Reps')}" 
                data-duration="${escapeHtml(ex.duration || '5 sec hold')}" 
                data-instructions="${escapeHtml(ex.instructions || '')}" ${isSel}>
                ${escapeHtml(ex.name)} (${escapeHtml(ex.target_body_part || 'General')})
            </option>`;
        });
        html += '<option value="custom">✏️ Custom / Other Exercise (Type Manual)</option>';
        return html;
    }

    function onExerciseChange(selectEl) {
        const row = selectEl.closest('.consult-ex-row');
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

    let consultExIndex = 1;
    function addConsultExerciseRow() {
        const tbody = document.getElementById('consultExerciseRowsContainer');
        const tr = document.createElement('tr');
        tr.className = 'consult-ex-row border-b border-slate-100 hover:bg-slate-50/50';
        tr.innerHTML = `
            <td class="py-2 px-3">
                <div class="space-y-1.5">
                    <select class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-emerald-300 bg-emerald-50/40 font-bold text-slate-800 focus:outline-none focus:border-emerald-600 ex-dropdown" onchange="onExerciseChange(this)">
                        ${getExerciseOptionsHtml(-1)}
                    </select>
                    <input type="text" name="prescribed_exercises[${consultExIndex}][name]" value="" placeholder="Exercise Name" class="w-full px-2.5 py-1 text-xs rounded-lg border border-slate-200 font-bold outline-none focus:border-emerald-500 bg-white ex-name-input">
                </div>
            </td>
            <td class="py-2 px-2 align-top pt-2.5">
                <input type="text" name="prescribed_exercises[${consultExIndex}][target]" value="General" placeholder="Area" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-target-input">
            </td>
            <td class="py-2 px-2 align-top pt-2.5">
                <input type="text" name="prescribed_exercises[${consultExIndex}][sets]" value="3 Sets" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-sets-input">
            </td>
            <td class="py-2 px-2 align-top pt-2.5">
                <input type="text" name="prescribed_exercises[${consultExIndex}][reps]" value="10 Reps" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-reps-input">
            </td>
            <td class="py-2 px-2 align-top pt-2.5">
                <input type="text" name="prescribed_exercises[${consultExIndex}][duration]" value="5 sec hold" class="w-full px-2 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-duration-input">
            </td>
            <td class="py-2 px-3 align-top pt-2.5">
                <input type="text" name="prescribed_exercises[${consultExIndex}][instructions]" value="" placeholder="Directions" class="w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 bg-white ex-instructions-input">
            </td>
            <td class="py-2 px-2 text-center align-top pt-2.5">
                <button type="button" onclick="this.closest('.consult-ex-row').remove()" title="Delete Row" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        lucide.createIcons();
        consultExIndex++;
    }

    function updateConsultModalities() {
        const checked = [];
        document.querySelectorAll('input[name="consultation_modalities[]"]:checked').forEach(el => {
            checked.push(el.value);
        });
        const hidden = document.getElementById('consultModalitiesHidden');
        if (hidden) hidden.value = checked.join(', ');
    }

    function setDiagnosis(name) {
        const diagInput = document.getElementById('diagnosis_summary');
        if (diagInput.value) {
            diagInput.value += ', ' + name;
        } else {
            diagInput.value = name;
        }
    }

    function calcBMI() {
        const w = parseFloat(document.getElementById('v_weight').value);
        const h = parseFloat(document.getElementById('v_height').value);
        const bmiEl = document.getElementById('calculatedBmi');
        if (w > 0 && h > 0) {
            const hM = h / 100;
            const bmi = (w / (hM * hM)).toFixed(1);
            bmiEl.innerText = bmi + (bmi < 18.5 ? ' (Underweight)' : (bmi > 25 ? ' (Overweight)' : ' (Normal)'));
        } else {
            bmiEl.innerText = '-';
        }
    }

    function copyPreviousVisitData() {
        const pDiag = document.getElementById('prevDiagText');
        const pComp = document.getElementById('prevComplaintText');
        const pTreat = document.getElementById('prevTreatmentText');
        if (pDiag && pDiag.innerText) {
            document.getElementById('diagnosis_summary').value = pDiag.innerText;
        }
        if (pComp && pComp.innerText) {
            document.getElementById('chief_complaint').value = 'Revisit review: ' + pComp.innerText;
        }
        if (pTreat && pTreat.innerText) {
            document.getElementById('treatment_plan').value = pTreat.innerText;
        }
        alert('Previous visit clinical data copied into current consultation!');
    }
</script>
@endpush
