@extends('layouts.app')

@section('title', $patient->full_name . ' - Medical CRM Profile')
@section('breadcrumb', 'Patients / ' . $patient->patient_id)
@section('page_title', $patient->full_name)

@section('content')
<div class="space-y-6">

    <!-- CRM HEADER SECTION (MATCHING SPECIFICATION #38) -->
    <div class="card-custom p-6 bg-white shadow-sm border-slate-200">
        <div class="flex flex-wrap items-start justify-between gap-6">
            
            <!-- PATIENT IDENTITY & AVATAR -->
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white flex items-center justify-center font-black text-xl shadow-md shadow-blue-200 shrink-0">
                    {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $patient->full_name }}</h2>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $patient->patient_id }}
                        </span>
                        @if($patient->blood_group)
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                {{ $patient->blood_group }}
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 mt-2">
                        <span class="flex items-center gap-1">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-slate-400"></i>
                            <strong>{{ $patient->gender }}</strong>, {{ $patient->age }} years
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                            <a href="tel:{{ $patient->mobile }}" class="hover:text-blue-600 font-semibold">{{ $patient->mobile }}</a>
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-slate-400"></i>
                            {{ $patient->city ?? 'Gurugram' }}, {{ $patient->state ?? 'Haryana' }}
                        </span>
                    </div>

                    @if($patient->medicalHistory && $patient->medicalHistory->allergies)
                        <div class="mt-2 text-xs font-semibold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200 inline-flex items-center gap-1.5">
                            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i>
                            <span>Allergies: {{ $patient->medicalHistory->allergies }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- KEY STATS CAPSULES -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-left min-w-[120px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Last Visit</span>
                    <span class="font-bold text-xs text-slate-800 block mt-0.5">
                        {{ $patient->last_visit ? $patient->last_visit->visit_date->format('d M Y') : 'None' }}
                    </span>
                </div>

                <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-left min-w-[120px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Next Appointment</span>
                    <span class="font-bold text-xs text-blue-700 block mt-0.5">
                        {{ $patient->next_appointment ? $patient->next_appointment->appointment_date->format('d M Y') : 'None scheduled' }}
                    </span>
                </div>

                <div class="px-4 py-2.5 rounded-2xl {{ $patient->outstanding_balance > 0 ? 'bg-rose-50 border-rose-200' : 'bg-emerald-50 border-emerald-200' }} border text-left min-w-[130px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $patient->outstanding_balance > 0 ? 'text-rose-700' : 'text-emerald-700' }} block">Outstanding Due</span>
                    <span class="font-extrabold text-sm {{ $patient->outstanding_balance > 0 ? 'text-rose-700' : 'text-emerald-700' }} block mt-0.5">
                        ₹{{ number_format($patient->outstanding_balance, 2) }}
                    </span>
                </div>
            </div>

        </div>

        <!-- ACTION BUTTONS ROW (CRM ACTIONS) -->
        <div class="mt-6 pt-5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 text-xs">
                
                <a href="{{ route('consultations.create', ['patient_id' => $patient->id]) }}" 
                   class="flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition shadow-xs">
                    <i data-lucide="stethoscope" class="w-3.5 h-3.5"></i>
                    <span>Start Consultation</span>
                </a>

                <button onclick="openModal('quickAppointmentModal')" 
                        class="flex items-center gap-1.5 px-3.5 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-xs">
                    <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                    <span>New Appointment</span>
                </button>

                @if($patient->outstanding_balance > 0)
                    <button onclick="openModal('patientCollectDueModal')" 
                            class="flex items-center gap-1.5 px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold transition shadow-xs">
                        <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                        <span>Collect Due (₹{{ number_format($patient->outstanding_balance, 0) }})</span>
                    </button>
                @endif

                <button onclick="openModal('addReportModal')" 
                        class="flex items-center gap-1.5 px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl font-semibold transition">
                    <i data-lucide="upload" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Add Report</span>
                </button>

                <button onclick="openModal('addFollowUpModal')" 
                        class="flex items-center gap-1.5 px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl font-semibold transition">
                    <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Add Follow-up</span>
                </button>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('patients.print-summary', $patient->id) }}" target="_blank" 
                   class="px-3 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Print Summary</span>
                </a>
                <a href="{{ route('patients.edit', $patient->id) }}" 
                   class="p-2 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-semibold">
                    <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 11 MEDICAL CRM TABS NAVIGATION -->
    <div class="border-b border-slate-200 bg-white rounded-2xl p-1 shadow-2xs overflow-x-auto">
        <nav class="flex items-center gap-1 text-xs font-semibold min-w-max">
            @php
                $tabs = [
                    'overview' => ['label' => 'Overview', 'icon' => 'info'],
                    'appointments' => ['label' => 'Appointments (' . $patient->appointments->count() . ')', 'icon' => 'calendar'],
                    'visits' => ['label' => 'Visits (' . $patient->visits->count() . ')', 'icon' => 'stethoscope'],
                    'history' => ['label' => 'Medical History', 'icon' => 'activity'],
                    'prescriptions' => ['label' => 'Prescriptions (' . $patient->prescriptions->count() . ')', 'icon' => 'pill'],
                    'reports' => ['label' => 'Medical Reports (' . $patient->reports->count() . ')', 'icon' => 'file-text'],
                    'progress' => ['label' => 'Progress Tracker (' . $patient->progressRecords->count() . ')', 'icon' => 'trending-up'],
                    'payments' => ['label' => 'Payments & Dues (' . $patient->invoices->count() . ')', 'icon' => 'receipt'],
                    'followups' => ['label' => 'Follow-ups (' . $patient->followUps->count() . ')', 'icon' => 'clock'],
                    'timeline' => ['label' => 'Patient Timeline', 'icon' => 'git-commit'],
                ];
            @endphp

            @foreach($tabs as $tabKey => $tabInfo)
                <a href="?tab={{ $tabKey }}" 
                   class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl transition {{ $activeTab === $tabKey ? 'bg-navy-900 text-white font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                    <i data-lucide="{{ $tabInfo['icon'] }}" class="w-3.5 h-3.5"></i>
                    <span>{{ $tabInfo['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <!-- TAB CONTENTS -->
    <div class="space-y-6">

        <!-- 1. TAB: OVERVIEW -->
        @if($activeTab === 'overview')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Patient Demographics -->
                <div class="card-custom p-6 bg-white space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                        <span>Personal Details</span>
                    </h3>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400">Date of Birth:</span>
                            <span class="font-semibold text-slate-800">{{ $patient->dob ? $patient->dob->format('d M Y') : 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400">Occupation:</span>
                            <span class="font-semibold text-slate-800">{{ $patient->occupation ?? 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400">Marital Status:</span>
                            <span class="font-semibold text-slate-800">{{ $patient->marital_status ?? 'Single' }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400">Emergency Contact:</span>
                            <span class="font-semibold text-slate-800">{{ $patient->emergency_contact ?? 'None' }} ({{ $patient->emergency_phone ?? '-' }})</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-50">
                            <span class="text-slate-400">Referral Source:</span>
                            <span class="font-semibold text-slate-800">{{ $patient->referral_source ?? 'Direct Walk-in' }}</span>
                        </div>
                        <div class="pt-2">
                            <span class="text-slate-400 block mb-1">Residential Address:</span>
                            <p class="font-medium text-slate-700 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                {{ $patient->address ?? 'No address provided' }}, {{ $patient->city }}, {{ $patient->state }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Medical Baseline Summary -->
                <div class="card-custom p-6 bg-white space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="heart-pulse" class="w-4 h-4 text-rose-600"></i>
                        <span>Medical Profile</span>
                    </h3>
                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Known Conditions</span>
                            <p class="font-semibold text-slate-800 bg-slate-50 p-2 rounded-lg border border-slate-100 mt-1">
                                {{ $patient->medicalHistory->conditions ?? 'None logged' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-rose-500 tracking-wider block">Allergies (Critical)</span>
                            <p class="font-semibold text-rose-800 bg-rose-50 p-2 rounded-lg border border-rose-100 mt-1">
                                {{ $patient->medicalHistory->allergies ?? 'No known drug allergies' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Current Regular Medications</span>
                            <p class="font-medium text-slate-700 bg-slate-50 p-2 rounded-lg border border-slate-100 mt-1">
                                {{ $patient->medicalHistory->current_medications ?? 'None' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Past Surgeries / Family History</span>
                            <p class="font-medium text-slate-600 text-[11px] mt-1">
                                Surgeries: {{ $patient->medicalHistory->surgeries ?? 'None' }} • Family: {{ $patient->medicalHistory->family_history ?? 'None' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Vitals & Notes -->
                <div class="card-custom p-6 bg-white space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i data-lucide="file-edit" class="w-4 h-4 text-amber-600"></i>
                        <span>Doctor Clinical Notes</span>
                    </h3>
                    <div class="text-xs">
                        <p class="text-slate-600 leading-relaxed bg-amber-50/50 p-3.5 rounded-xl border border-amber-100">
                            {{ $patient->notes ?? 'No special notes recorded. Patient in active outpatient follow-up.' }}
                        </p>
                    </div>

                    @if($patient->last_visit && $patient->last_visit->vitals_json)
                        @php $v = $patient->last_visit->vitals_json; @endphp
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2">Latest Recorded Vitals ({{ $patient->last_visit->visit_date->format('d M') }})</span>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                <div class="p-2 rounded-xl bg-slate-50">
                                    <span class="text-[10px] text-slate-400 block">BP</span>
                                    <strong class="text-slate-800">{{ $v['bp_sys'] ?? '-' }}/{{ $v['bp_dia'] ?? '-' }}</strong>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-50">
                                    <span class="text-[10px] text-slate-400 block">Weight</span>
                                    <strong class="text-slate-800">{{ $v['weight'] ?? '-' }} kg</strong>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-50">
                                    <span class="text-[10px] text-slate-400 block">Pulse</span>
                                    <strong class="text-slate-800">{{ $v['pulse'] ?? '-' }} bpm</strong>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- 2. TAB: APPOINTMENTS -->
        @if($activeTab === 'appointments')
            <div class="card-custom bg-white overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Appointment History</h3>
                    <button onclick="openModal('quickAppointmentModal')" class="px-3.5 py-1.5 bg-navy-900 text-white rounded-xl text-xs font-bold">
                        + New Appointment
                    </button>
                </div>
                <div class="overflow-x-auto text-xs">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <tr class="border-b border-slate-100">
                                <th class="p-3 pl-5">Appt No</th>
                                <th class="p-3">Date & Time</th>
                                <th class="p-3">Doctor</th>
                                <th class="p-3">Type</th>
                                <th class="p-3">Reason</th>
                                <th class="p-3">Payment</th>
                                <th class="p-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($patient->appointments as $apt)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 pl-5 font-bold text-slate-900">{{ $apt->appointment_no }} (Token #{{ $apt->token_number }})</td>
                                    <td class="p-3 font-semibold">{{ $apt->appointment_date->format('d M Y') }} at {{ $apt->appointment_time }}</td>
                                    <td class="p-3 text-slate-700">{{ $apt->doctor ? $apt->doctor->name : '-' }}</td>
                                    <td class="p-3 uppercase text-[10px] font-bold text-slate-500">{{ $apt->appointment_type }}</td>
                                    <td class="p-3 text-slate-600">{{ $apt->reason ?? '-' }}</td>
                                    <td class="p-3 font-bold text-slate-800">₹{{ number_format($apt->consultation_fee, 0) }} ({{ ucfirst($apt->payment_status) }})</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize 
                                            {{ $apt->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($apt->status === 'in_consultation' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700') }}">
                                            {{ $apt->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="p-8 text-center text-slate-400">No appointment records found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 3. TAB: VISITS -->
        @if($activeTab === 'visits')
            <div class="space-y-4">
                @forelse($patient->visits as $vst)
                    <div class="card-custom p-6 bg-white space-y-4 border-slate-200">
                        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-sm">Visit #{{ $vst->visit_no }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 uppercase">
                                        {{ $vst->visit_type }}
                                    </span>
                                </div>
                                <span class="text-xs text-slate-400">Date: {{ $vst->visit_date->format('d M Y') }} • Consultant: {{ $vst->doctor->name ?? 'Dr. Rajiv' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($vst->prescriptions->first())
                                    <a href="{{ route('prescriptions.print', $vst->prescriptions->first()->id) }}" target="_blank" 
                                       class="px-3 py-1.5 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-1.5">
                                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                        <span>Print Rx</span>
                                    </a>
                                @endif
                                <a href="{{ route('consultations.show', $vst->id) }}" class="px-3 py-1.5 bg-navy-900 text-white rounded-xl text-xs font-bold">
                                    Full Visit Details
                                </a>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Chief Complaint & Symptoms</span>
                                <p class="font-semibold text-slate-800 mt-1">{{ $vst->chief_complaint }}</p>
                                @if($vst->symptoms)
                                    <p class="text-slate-500 mt-1">{{ $vst->symptoms }}</p>
                                @endif
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block">Diagnosis Summary</span>
                                <p class="font-bold text-blue-800 mt-1">{{ $vst->diagnosis_summary ?? 'Routine Check' }}</p>
                                @if($vst->treatment_plan)
                                    <p class="text-slate-600 mt-1 text-[11px]">{{ $vst->treatment_plan }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Vitals Grid -->
                        @if($vst->vitals_json)
                            @php $vit = $vst->vitals_json; @endphp
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex flex-wrap items-center gap-6 text-xs">
                                <div><span class="text-slate-400">BP:</span> <strong>{{ $vit['bp_sys'] ?? '-' }}/{{ $vit['bp_dia'] ?? '-' }} mmHg</strong></div>
                                <div><span class="text-slate-400">Pulse:</span> <strong>{{ $vit['pulse'] ?? '-' }} bpm</strong></div>
                                <div><span class="text-slate-400">Temp:</span> <strong>{{ $vit['temp'] ?? '-' }} °F</strong></div>
                                <div><span class="text-slate-400">Weight:</span> <strong>{{ $vit['weight'] ?? '-' }} kg</strong></div>
                                <div><span class="text-slate-400">SpO2:</span> <strong>{{ $vit['spo2'] ?? '-' }}%</strong></div>
                                <div><span class="text-slate-400">BMI:</span> <strong>{{ $vit['bmi'] ?? '-' }}</strong></div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="card-custom p-12 text-center text-slate-400 bg-white">
                        No clinical visits recorded yet for this patient.
                    </div>
                @endforelse
            </div>
        @endif

        <!-- 4. TAB: MEDICAL HISTORY (EDITABLE INLINE!) -->
        @if($activeTab === 'history')
            <div class="card-custom p-6 bg-white space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Longitudinal Medical History</h3>
                        <p class="text-xs text-slate-400">Permanent medical background linked to {{ $patient->full_name }}</p>
                    </div>
                </div>

                <form action="{{ route('patients.medical-history', $patient->id) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Pre-existing Medical Conditions & Diseases</label>
                        <textarea name="conditions" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">{{ $patient->medicalHistory->conditions ?? '' }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-rose-700 mb-1">Drug & Food Allergies (Critical Alert)</label>
                            <textarea name="allergies" rows="2" class="w-full px-3 py-2 border border-rose-200 rounded-xl bg-rose-50/40 focus:bg-white focus:border-rose-500 outline-none">{{ $patient->medicalHistory->allergies ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Current Long-term Medications</label>
                            <textarea name="current_medications" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">{{ $patient->medicalHistory->current_medications ?? '' }}</textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Past Surgical Interventions</label>
                            <input type="text" name="surgeries" value="{{ $patient->medicalHistory->surgeries ?? '' }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block font-semibold text-slate-700 mb-1">Family Medical History</label>
                            <input type="text" name="family_history" value="{{ $patient->medicalHistory->family_history ?? '' }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="pt-3 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 bg-navy-900 text-white rounded-xl font-bold hover:bg-navy-800 transition">
                            Save Medical History
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- 5. TAB: PRESCRIPTIONS -->
        @if($activeTab === 'prescriptions')
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 text-sm">Prescriptions History</h3>
                    <a href="{{ route('prescriptions.create', ['patient_id' => $patient->id]) }}" class="px-3.5 py-1.5 bg-navy-900 text-white rounded-xl text-xs font-bold">
                        + New Prescription
                    </a>
                </div>

                @forelse($patient->prescriptions as $rx)
                    <div class="card-custom p-6 bg-white space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <span class="font-bold text-slate-900 text-sm">Prescription #{{ $rx->prescription_no }}</span>
                                <span class="text-xs text-slate-400 block">Date: {{ $rx->prescription_date->format('d M Y') }} • Prescribed by {{ $rx->doctor->name ?? 'Dr. Rajiv' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('prescriptions.print', $rx->id) }}" target="_blank" 
                                   class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 rounded-xl text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    <span>Print Prescription</span>
                                </a>
                                <a href="{{ route('prescriptions.show', $rx->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-semibold">
                                    View Rx
                                </a>
                            </div>
                        </div>

                        <!-- Medication Items Table -->
                        <div class="overflow-x-auto text-xs">
                            <table class="w-full text-left">
                                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    <tr class="border-b border-slate-100">
                                        <th class="p-2.5">Medicine Name</th>
                                        <th class="p-2.5">Dosage</th>
                                        <th class="p-2.5">Frequency</th>
                                        <th class="p-2.5">Duration</th>
                                        <th class="p-2.5">Timing & Instructions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($rx->items as $item)
                                        <tr>
                                            <td class="p-2.5 font-bold text-slate-900">{{ $item->medicine_name }}</td>
                                            <td class="p-2.5 text-slate-600">{{ $item->dosage }}</td>
                                            <td class="p-2.5 font-semibold text-blue-700">{{ $item->frequency }}</td>
                                            <td class="p-2.5 text-slate-700">{{ $item->duration }}</td>
                                            <td class="p-2.5 text-slate-600">{{ $item->timing }} {{ $item->instructions ? '• ' . $item->instructions : '' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($rx->advice)
                            <div class="p-3 bg-blue-50/50 rounded-xl border border-blue-100 text-xs text-blue-900">
                                <strong>Doctor Advice:</strong> {{ $rx->advice }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="card-custom p-12 text-center text-slate-400 bg-white">
                        No prescriptions generated yet for this patient.
                    </div>
                @endforelse
            </div>
        @endif

        <!-- 6. TAB: MEDICAL REPORTS -->
        @if($activeTab === 'reports')
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 text-sm">Diagnostic & Laboratory Reports</h3>
                    <button onclick="openModal('addReportModal')" class="px-3.5 py-1.5 bg-navy-900 text-white rounded-xl text-xs font-bold">
                        + Upload Report
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($patient->reports as $rep)
                        <div class="card-custom p-5 bg-white space-y-3">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                                        <i data-lucide="file-check-2" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-900 text-xs">{{ $rep->report_name }}</h4>
                                        <span class="text-[10px] text-slate-400">{{ $rep->report_no }} • {{ $rep->report_date->format('d M Y') }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $rep->report_type }}
                                </span>
                            </div>

                            <p class="text-xs text-slate-600 leading-snug">
                                {{ $rep->description ?? 'No diagnostic interpretation provided.' }}
                            </p>

                            @if($rep->doctor_notes)
                                <div class="p-2.5 bg-slate-50 rounded-lg text-[11px] text-slate-700 border border-slate-100">
                                    <strong>Doctor Notes:</strong> {{ $rep->doctor_notes }}
                                </div>
                            @endif

                            <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                                <span class="text-slate-400 text-[11px] truncate max-w-[150px]">Lab: {{ $rep->laboratory ?? 'CarePoint Diagnostic' }}</span>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('medical-reports.preview', $rep->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 text-[11px] font-bold transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i> Preview
                                    </a>
                                    <a href="{{ route('medical-reports.download', $rep->id) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-[11px] font-bold transition">
                                        <i data-lucide="download" class="w-3.5 h-3.5"></i> Download
                                    </a>
                                    <form action="{{ route('medical-reports.destroy', $rep->id) }}" method="POST" onsubmit="return confirm('Delete this report?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-rose-600 transition" title="Delete Report">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 card-custom p-12 text-center text-slate-400 bg-white">
                            No medical reports uploaded yet.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        <!-- 7. TAB: PROGRESS TRACKER (WITH LIVE CHART.JS) -->
        @if($activeTab === 'progress')
            <div class="space-y-6">
                <!-- CHART CONTAINER -->
                <div class="card-custom p-6 bg-white space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Longitudinal Clinical Trend</h3>
                            <p class="text-xs text-slate-400">Weight, Blood Pressure & Pulse trend over visits</p>
                        </div>
                        <button onclick="openModal('addProgressModal')" class="px-3.5 py-1.5 bg-navy-900 text-white rounded-xl text-xs font-bold">
                            + Log Current Vitals
                        </button>
                    </div>

                    <div class="h-64">
                        <canvas id="patientProgressChart"></canvas>
                    </div>
                </div>

                <!-- PROGRESS RECORDS TABLE -->
                <div class="card-custom bg-white overflow-hidden">
                    <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-xs">
                        Progress Entries Log
                    </div>
                    <div class="overflow-x-auto text-xs">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                <tr class="border-b border-slate-100">
                                    <th class="p-3 pl-5">Date</th>
                                    <th class="p-3">Weight (kg)</th>
                                    <th class="p-3">BP (mmHg)</th>
                                    <th class="p-3">Pulse</th>
                                    <th class="p-3">BMI</th>
                                    <th class="p-3">Pain Score</th>
                                    <th class="p-3">Assessment & Response</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($patient->progressRecords as $pr)
                                    <tr>
                                        <td class="p-3 pl-5 font-bold text-slate-900">{{ $pr->recorded_date->format('d M Y') }}</td>
                                        <td class="p-3 font-semibold">{{ $pr->weight ?? '-' }}</td>
                                        <td class="p-3 font-semibold">{{ $pr->bp_systolic ?? '-' }}/{{ $pr->bp_diastolic ?? '-' }}</td>
                                        <td class="p-3">{{ $pr->pulse ?? '-' }} bpm</td>
                                        <td class="p-3">{{ $pr->bmi ?? '-' }}</td>
                                        <td class="p-3 font-bold text-amber-700">{{ $pr->pain_level }}/10</td>
                                        <td class="p-3 text-slate-600">{{ $pr->treatment_response ?? $pr->symptoms_assessment }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="p-8 text-center text-slate-400">No progress history recorded yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- 8. TAB: PAYMENTS & INVOICES -->
        @if($activeTab === 'payments')
            <div class="space-y-6">
                <!-- FINANCIAL SUMMARY -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="card-custom p-5 bg-white">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Billed</span>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1">
                            ₹{{ number_format($patient->invoices->sum('total_amount'), 2) }}
                        </div>
                    </div>
                    <div class="card-custom p-5 bg-white">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Paid</span>
                        <div class="text-2xl font-extrabold text-emerald-700 mt-1">
                            ₹{{ number_format($patient->invoices->sum('paid_amount'), 2) }}
                        </div>
                    </div>
                    <div class="card-custom p-5 bg-white">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Outstanding Due</span>
                        <div class="text-2xl font-extrabold text-rose-600 mt-1">
                            ₹{{ number_format($patient->outstanding_balance, 2) }}
                        </div>
                    </div>
                </div>

                <!-- INVOICES TABLE -->
                <div class="card-custom bg-white overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 text-sm">Invoices & Billing History</h3>
                        <a href="{{ route('invoices.create', ['patient_id' => $patient->id]) }}" class="px-3.5 py-1.5 bg-navy-900 text-white rounded-xl text-xs font-bold">
                            + Generate Invoice
                        </a>
                    </div>
                    <div class="overflow-x-auto text-xs">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                <tr class="border-b border-slate-100">
                                    <th class="p-3 pl-5">Invoice No</th>
                                    <th class="p-3">Date</th>
                                    <th class="p-3">Total Amount</th>
                                    <th class="p-3">Paid Amount</th>
                                    <th class="p-3">Due Balance</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3 pr-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($patient->invoices as $inv)
                                    <tr class="hover:bg-slate-50">
                                        <td class="p-3 pl-5 font-bold text-slate-900">{{ $inv->invoice_no }}</td>
                                        <td class="p-3 text-slate-600">{{ $inv->invoice_date->format('d M Y') }}</td>
                                        <td class="p-3 font-semibold text-slate-800">₹{{ number_format($inv->total_amount, 2) }}</td>
                                        <td class="p-3 font-semibold text-emerald-700">₹{{ number_format($inv->paid_amount, 2) }}</td>
                                        <td class="p-3 font-bold text-rose-600">₹{{ number_format($inv->due_amount, 2) }}</td>
                                        <td class="p-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase 
                                                {{ $inv->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($inv->payment_status === 'partially_paid' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                                {{ str_replace('_', ' ', $inv->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="p-3 pr-5 text-right">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="p-1 bg-slate-100 rounded hover:bg-slate-200">
                                                    <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-600"></i>
                                                </a>
                                                <a href="{{ route('invoices.show', $inv->id) }}" class="p-1 bg-slate-100 rounded hover:bg-slate-200">
                                                    <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-600"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="p-8 text-center text-slate-400">No invoices recorded.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- 9. TAB: FOLLOW-UPS -->
        @if($activeTab === 'followups')
            <div class="card-custom bg-white overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 text-sm">Scheduled Follow-ups</h3>
                    <button onclick="openModal('addFollowUpModal')" class="px-3.5 py-1.5 bg-navy-900 text-white rounded-xl text-xs font-bold">
                        + Schedule Follow-up
                    </button>
                </div>
                <div class="overflow-x-auto text-xs">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <tr class="border-b border-slate-100">
                                <th class="p-3 pl-5">Date</th>
                                <th class="p-3">Time</th>
                                <th class="p-3">Doctor</th>
                                <th class="p-3">Reason</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 pr-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($patient->followUps as $fu)
                                <tr class="hover:bg-slate-50">
                                    <td class="p-3 pl-5 font-bold text-slate-900">{{ $fu->follow_up_date->format('d M Y') }}</td>
                                    <td class="p-3 text-slate-600">{{ $fu->follow_up_time ?? '10:00 AM' }}</td>
                                    <td class="p-3 text-slate-700">{{ $fu->doctor->name ?? 'Dr. Rajiv' }}</td>
                                    <td class="p-3 font-medium text-slate-800">{{ $fu->reason }}</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold capitalize 
                                            {{ $fu->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($fu->status === 'missed' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                            {{ $fu->status }}
                                        </span>
                                    </td>
                                    <td class="p-3 pr-5 text-right">
                                        @if($fu->status === 'scheduled')
                                            <form action="{{ route('followups.status', $fu->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="px-2 py-1 bg-emerald-50 text-emerald-700 rounded text-[11px] font-bold hover:bg-emerald-100">
                                                    Mark Done
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="p-8 text-center text-slate-400">No scheduled follow-ups.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- 10. TAB: TIMELINE -->
        @if($activeTab === 'timeline')
            <div class="card-custom p-6 bg-white space-y-6">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="font-bold text-slate-900 text-sm">End-to-End Patient Journey Timeline</h3>
                    <p class="text-xs text-slate-400">Chronological history of patient interactions, visits, and payments</p>
                </div>

                <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                    @foreach($sortedTimeline as $event)
                        <div class="relative group">
                            <!-- Bullet -->
                            <div class="absolute -left-[27px] top-1 w-5 h-5 rounded-full bg-white border-2 border-blue-600 flex items-center justify-center shadow-xs">
                                <div class="w-2 h-2 rounded-full bg-blue-600"></div>
                            </div>
                            
                            <div class="p-4 rounded-xl bg-slate-50 hover:bg-slate-100/70 transition border border-slate-100 space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-900">{{ $event['title'] }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $event['date']->format('d M Y, h:i A') }}</span>
                                </div>
                                <p class="text-xs text-slate-600">{{ $event['description'] }}</p>
                                <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold {{ $event['badge_color'] }} uppercase tracking-wider">
                                    {{ $event['badge'] }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

</div>

<!-- MODAL: ADD REPORT -->
<div id="addReportModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Upload Medical Report for {{ $patient->full_name }}</h3>
            <button onclick="closeModal('addReportModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('medical-reports.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 text-xs">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Report Title *</label>
                <input type="text" name="report_name" required placeholder="e.g. Lipid Profile, Chest X-Ray..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Category *</label>
                    <select name="report_type" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        <option value="Blood Test">Blood Test</option>
                        <option value="Urine Test">Urine Test</option>
                        <option value="X-Ray">X-Ray</option>
                        <option value="MRI">MRI</option>
                        <option value="CT Scan">CT Scan</option>
                        <option value="Ultrasound">Ultrasound</option>
                        <option value="ECG">ECG</option>
                        <option value="Pathology">Pathology</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Report Date *</label>
                    <input type="date" name="report_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Diagnostic Findings / Description</label>
                <textarea name="description" rows="3" placeholder="Key report values and observations..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none"></textarea>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Doctor Impression / Notes</label>
                <input type="text" name="doctor_notes" placeholder="Clinical review remarks" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Attach File (PDF, JPG, PNG)</label>
                <input type="file" name="attachment" class="w-full text-slate-500 text-xs">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addReportModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl">Save & Attach Report</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: ADD FOLLOW-UP -->
<div id="addFollowUpModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Schedule Follow-up</h3>
            <button onclick="closeModal('addFollowUpModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('followups.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            <input type="hidden" name="doctor_id" value="{{ $patient->last_visit ? $patient->last_visit->doctor_id : 1 }}">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Follow-up Date *</label>
                <input type="date" name="follow_up_date" value="{{ now()->addDays(7)->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Time Slot</label>
                <input type="time" name="follow_up_time" value="10:30" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Reason for Follow-up *</label>
                <input type="text" name="reason" value="Treatment response evaluation & dosage review" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addFollowUpModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl">Schedule Follow-up</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: ADD PROGRESS ENTRY -->
<div id="addProgressModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Log Vitals & Clinical Progress</h3>
            <button onclick="closeModal('addProgressModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('progress.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <input type="hidden" name="patient_id" value="{{ $patient->id }}">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Date *</label>
                <input type="date" name="recorded_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Weight (kg)</label>
                    <input type="number" step="0.1" name="weight" placeholder="e.g. 78.5" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Height (cm)</label>
                    <input type="number" name="height" placeholder="e.g. 172" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pulse (bpm)</label>
                    <input type="number" name="pulse" placeholder="e.g. 74" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">BP Systolic</label>
                    <input type="number" name="bp_systolic" placeholder="120" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">BP Diastolic</label>
                    <input type="number" name="bp_diastolic" placeholder="80" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Pain Score (0-10)</label>
                    <input type="number" min="0" max="10" name="pain_level" value="0" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Treatment Response / Notes</label>
                <input type="text" name="treatment_response" placeholder="e.g. BP stabilized, sugars in normal range" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addProgressModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl">Save Progress</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: COLLECT DUE PAYMENT -->
@if($patient->outstanding_balance > 0)
@php
    $unpaidInvoice = $patient->invoices()->whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->orderBy('invoice_date', 'asc')->first();
@endphp
<div id="patientCollectDueModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Collect Outstanding Due</h3>
                <p class="text-xs text-rose-600 font-semibold">Total Pending Due: ₹{{ number_format($patient->outstanding_balance, 2) }}</p>
            </div>
            <button onclick="closeModal('patientCollectDueModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        @if($unpaidInvoice)
        <form action="{{ route('invoices.pay', $unpaidInvoice->id) }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Collection Amount (₹) *</label>
                <input type="number" step="0.01" name="amount" value="{{ $unpaidInvoice->due_amount }}" max="{{ $unpaidInvoice->due_amount }}" required 
                       class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white font-bold text-base text-slate-800 outline-none">
                <span class="text-[11px] text-slate-400 mt-0.5 block">Invoice #{{ $unpaidInvoice->invoice_no }} • Outstanding: ₹{{ number_format($unpaidInvoice->due_amount, 2) }}</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Payment Method *</label>
                    <select name="payment_method" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI / GPay / PhonePe</option>
                        <option value="Card">Card Swipe</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Reference / Transaction ID</label>
                <input type="text" name="transaction_reference" placeholder="e.g. UPI Ref 48291038" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('patientCollectDueModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl">
                    Confirm & Print Receipt
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endif

@endsection

@push('scripts')
@if($activeTab === 'progress')
<script>
    const pChartEl = document.getElementById('patientProgressChart');
    if (pChartEl) {
        new Chart(pChartEl, {
            type: 'line',
            data: {
                labels: {!! json_encode($progressDates) !!},
                datasets: [
                    {
                        label: 'Weight (kg)',
                        data: {!! json_encode($progressWeights) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 2,
                        tension: 0.2,
                        yAxisID: 'y'
                    },
                    {
                        label: 'BP Systolic (mmHg)',
                        data: {!! json_encode($progressSystolic) !!},
                        borderColor: '#dc2626',
                        borderWidth: 2,
                        tension: 0.2,
                        yAxisID: 'y1'
                    },
                    {
                        label: 'BP Diastolic (mmHg)',
                        data: {!! json_encode($progressDiastolic) !!},
                        borderColor: '#f97316',
                        borderWidth: 2,
                        tension: 0.2,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Weight (kg)' }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Blood Pressure (mmHg)' }
                    }
                }
            }
        });
    }
</script>
@endif
@endpush
