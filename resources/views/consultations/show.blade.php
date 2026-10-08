@extends('layouts.app')

@section('title', 'Consultation Encounter #' . $visit->visit_no)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('consultations.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <div class="flex items-center gap-2.5">
                    <h1 class="text-2xl font-bold text-slate-900">Encounter #{{ $visit->visit_no }}</h1>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full
                        {{ $visit->visit_type == 'New' ? 'bg-blue-50 text-blue-700 border border-blue-200' : '' }}
                        {{ $visit->visit_type == 'Follow-up' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                        {{ $visit->visit_type == 'Revisit' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                        {{ $visit->visit_type == 'Emergency' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}">
                        {{ $visit->visit_type }} Encounter
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Recorded on {{ \Carbon\Carbon::parse($visit->visit_date)->format('F d, Y') }} • Consulting Doctor: {{ $visit->doctor->name }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if($visit->prescriptions && $visit->prescriptions->count() > 0)
                <a href="{{ route('prescriptions.print', $visit->prescriptions->first()->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 transition shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4 text-primary"></i> Print Prescription
                </a>
            @endif
            <a href="{{ route('patients.show', ['patient' => $visit->patient_id, 'tab' => 'visits']) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl bg-primary text-white hover:bg-slate-800 transition shadow-sm">
                <i data-lucide="user" class="w-4 h-4"></i> Patient CRM Profile
            </a>
        </div>
    </div>

    <!-- Patient Banner Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-lg">
                {{ substr($visit->patient->first_name, 0, 1) }}{{ substr($visit->patient->last_name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">{{ $visit->patient->full_name }}</h3>
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 mt-1">
                    <span class="font-mono bg-slate-100 px-2 py-0.5 rounded">{{ $visit->patient->patient_id }}</span>
                    <span>{{ $visit->patient->gender }}, {{ $visit->patient->age }} years</span>
                    <span>Blood: <strong class="text-rose-600">{{ $visit->patient->blood_group ?? 'N/A' }}</strong></span>
                    <span>Mobile: {{ $visit->patient->mobile }}</span>
                </div>
            </div>
        </div>

        <div class="flex sm:flex-col items-end gap-1 text-right border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
            <div class="text-xs text-slate-400">Total Outstanding Due</div>
            <div class="text-base font-bold {{ $visit->patient->outstanding_balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                ₹{{ number_format($visit->patient->outstanding_balance, 2) }}
            </div>
        </div>
    </div>

    <!-- Vitals Matrix -->
    @php $vitals = $visit->vitals_json ?? []; @endphp
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
            <i data-lucide="activity" class="w-4 h-4 text-primary"></i> Vitals Recorded During Visit
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3">
            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Blood Pressure</span>
                <span class="text-sm font-bold text-slate-800">
                    {{ (!empty($vitals['bp_sys']) && !empty($vitals['bp_dia'])) ? "{$vitals['bp_sys']}/{$vitals['bp_dia']}" : '--' }}
                </span>
                <span class="text-[10px] text-slate-400 block">mmHg</span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Pulse</span>
                <span class="text-sm font-bold text-slate-800">{{ $vitals['pulse'] ?? '--' }}</span>
                <span class="text-[10px] text-slate-400 block">bpm</span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Temperature</span>
                <span class="text-sm font-bold text-slate-800">{{ $vitals['temp'] ?? '--' }}</span>
                <span class="text-[10px] text-slate-400 block">°F</span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Weight</span>
                <span class="text-sm font-bold text-slate-800">{{ $vitals['weight'] ?? '--' }}</span>
                <span class="text-[10px] text-slate-400 block">kg</span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Height</span>
                <span class="text-sm font-bold text-slate-800">{{ $vitals['height'] ?? '--' }}</span>
                <span class="text-[10px] text-slate-400 block">cm</span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">BMI</span>
                <span class="text-sm font-bold text-slate-800">{{ $vitals['bmi'] ?? '--' }}</span>
                <span class="text-[10px] text-slate-400 block">kg/m²</span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">SpO2</span>
                <span class="text-sm font-bold text-slate-800">{{ $vitals['spo2'] ?? '--' }}</span>
                <span class="text-[10px] text-slate-400 block">%</span>
            </div>
            <div class="bg-slate-50 rounded-xl p-3 text-center border border-slate-100">
                <span class="text-[10px] uppercase font-bold text-slate-400 block">Pain Score</span>
                <span class="text-sm font-bold text-amber-600">{{ $vitals['pain_level'] ?? 0 }} / 10</span>
                <span class="text-[10px] text-slate-400 block">VAS</span>
            </div>
        </div>
    </div>

    <!-- Clinical Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Chief Complaint & Symptoms -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-primary"></i> Complaints & Symptoms
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-slate-500 block">Chief Complaint</label>
                    <p class="text-sm font-semibold text-slate-800 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100">
                        {{ $visit->chief_complaint }}
                    </p>
                </div>
                @if($visit->symptoms)
                    <div>
                        <label class="text-xs font-bold text-slate-500 block">Reported Symptoms</label>
                        <p class="text-xs text-slate-700 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed">
                            {{ $visit->symptoms }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Diagnoses & Treatment Plan -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i data-lucide="shield-alert" class="w-4 h-4 text-primary"></i> Diagnosis & Plan
            </h3>
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-bold text-slate-500 block">Diagnosis Summary</label>
                    <p class="text-sm font-bold text-slate-900 mt-1 bg-blue-50/50 p-3 rounded-xl border border-blue-100">
                        {{ $visit->diagnosis_summary ?? 'Clinical diagnosis recorded in details' }}
                    </p>
                </div>
                @if($visit->treatment_plan)
                    <div>
                        <label class="text-xs font-bold text-slate-500 block">Treatment Plan</label>
                        <p class="text-xs text-slate-700 mt-1 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed">
                            {{ $visit->treatment_plan }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Doctor Notes -->
    @if($visit->clinical_notes)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-primary"></i> Confidential Clinical Notes
            </h3>
            <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">
                {{ $visit->clinical_notes }}
            </p>
        </div>
    @endif

    <!-- Linked Prescription -->
    @if($visit->prescriptions && $visit->prescriptions->count() > 0)
        @foreach($visit->prescriptions as $rx)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2">
                        <i data-lucide="pill" class="w-5 h-5 text-emerald-600"></i>
                        <h3 class="text-sm font-bold text-slate-900">Prescription #{{ $rx->prescription_no }}</h3>
                    </div>
                    <a href="{{ route('prescriptions.print', $rx->id) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i> Print Rx
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-100">
                                <th class="py-2.5 px-3">#</th>
                                <th class="py-2.5 px-3">Medicine Name</th>
                                <th class="py-2.5 px-3">Dosage</th>
                                <th class="py-2.5 px-3">Frequency</th>
                                <th class="py-2.5 px-3">Duration</th>
                                <th class="py-2.5 px-3">Timing & Route</th>
                                <th class="py-2.5 px-3">Instructions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rx->items as $idx => $item)
                                <tr>
                                    <td class="py-2.5 px-3 font-mono text-slate-400">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-3 font-bold text-slate-900">{{ $item->medicine_name }}</td>
                                    <td class="py-2.5 px-3 text-slate-700">{{ $item->dosage ?: '-' }}</td>
                                    <td class="py-2.5 px-3 font-semibold text-primary">{{ $item->frequency ?: '-' }}</td>
                                    <td class="py-2.5 px-3 text-slate-700">{{ $item->duration ?: '-' }}</td>
                                    <td class="py-2.5 px-3 text-slate-600">{{ $item->timing }} ({{ $item->route }})</td>
                                    <td class="py-2.5 px-3 text-slate-500">{{ $item->instructions ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($rx->advice)
                    <div class="bg-amber-50/60 border border-amber-200/60 p-3.5 rounded-xl text-xs text-amber-900">
                        <strong class="font-bold">General Doctor Advice:</strong> {{ $rx->advice }}
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
@endsection
