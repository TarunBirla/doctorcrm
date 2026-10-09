@extends('layouts.app')

@section('title', 'Consultation & Visit Records')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Consultation & Visits</h1>
            <p class="text-sm text-slate-500">Clinical records, patient encounters, vitals & doctor diagnoses</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('queue.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
                <i data-lucide="users" class="w-4 h-4 text-primary"></i> OPD Queue
            </a>
            <a href="{{ route('consultations.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-bold hover:bg-slate-800 transition shadow-sm">
                <i data-lucide="stethoscope" class="w-4 h-4"></i> Start Consultation
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-xs">
        <form method="GET" action="{{ route('consultations.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end text-xs">
            <div class="relative">
                <label class="block font-semibold text-slate-600 mb-1">Search Patient / Visit</label>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Name, PAT ID, visit #..."
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 font-semibold outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Clinic Branch</label>
                <select name="clinic_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 font-semibold outline-none">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $cl)
                        <option value="{{ $cl->id }}" {{ (string) request('clinic_id', $clinicId ?? '') === (string) $cl->id ? 'selected' : '' }}>
                            {{ $cl->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Therapy Category</label>
                <select name="category_id" class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 font-semibold outline-none">
                    <option value="">All Categories</option>
                    @foreach($categories as $cg)
                        <option value="{{ $cg->id }}" {{ (string) request('category_id', $categoryId ?? '') === (string) $cg->id ? 'selected' : '' }}>
                            {{ $cg->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Visit Date</label>
                <input type="date" name="date" value="{{ $date ?? '' }}"
                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-blue-500 font-semibold outline-none text-slate-700">
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-sm text-center">
                    Filter
                </button>
                @if(!empty($search) || !empty($date) || !empty($clinicId) || !empty($categoryId))
                    <a href="{{ route('consultations.index') }}" class="p-2 rounded-xl border border-slate-200 text-slate-500 hover:bg-slate-50 font-semibold text-center" title="Clear Filters">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Visits Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100 text-xs font-bold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Visit #</th>
                        <th class="py-3.5 px-4">Patient Details</th>
                        <th class="py-3.5 px-4">Date & Type</th>
                        <th class="py-3.5 px-4">Chief Complaint & Diagnosis</th>
                        <th class="py-3.5 px-4">Vitals Summary</th>
                        <th class="py-3.5 px-4">Prescription</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($visits as $v)
                        <tr class="hover:bg-slate-50/60 transition group">
                            <td class="py-3.5 px-4 font-mono font-bold text-xs text-slate-700">
                                <a href="{{ route('consultations.show', $v->id) }}" class="text-primary hover:underline">
                                    {{ $v->visit_no }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs">
                                        {{ substr($v->patient->first_name, 0, 1) }}{{ substr($v->patient->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('patients.show', $v->patient_id) }}" class="font-bold text-slate-900 hover:text-primary">
                                            {{ $v->patient->full_name }}
                                        </a>
                                        <p class="text-xs text-slate-400 font-mono">{{ $v->patient->patient_id }} • {{ $v->patient->gender }}, {{ $v->patient->age }}y</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($v->visit_date)->format('M d, Y') }}</div>
                                <span class="inline-block mt-0.5 text-[11px] font-bold px-2 py-0.5 rounded-full
                                    {{ $v->visit_type == 'New' ? 'bg-blue-50 text-blue-700' : '' }}
                                    {{ $v->visit_type == 'Follow-up' ? 'bg-amber-50 text-amber-700' : '' }}
                                    {{ $v->visit_type == 'Revisit' ? 'bg-purple-50 text-purple-700' : '' }}
                                    {{ $v->visit_type == 'Emergency' ? 'bg-rose-50 text-rose-700' : '' }}">
                                    {{ $v->visit_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-xs font-semibold text-slate-800 truncate" title="{{ $v->chief_complaint }}">
                                    {{ $v->chief_complaint }}
                                </p>
                                <p class="text-xs text-slate-500 truncate mt-0.5" title="{{ $v->diagnosis_summary ?? 'No diagnosis recorded' }}">
                                    <span class="font-semibold text-slate-600">Dx:</span> {{ $v->diagnosis_summary ?? 'Clinical review' }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $vitals = $v->vitals_json ?? [];
                                    $bp = (!empty($vitals['bp_sys']) && !empty($vitals['bp_dia'])) ? "{$vitals['bp_sys']}/{$vitals['bp_dia']} mmHg" : null;
                                    $pulse = $vitals['pulse'] ?? null;
                                    $wt = $vitals['weight'] ?? null;
                                @endphp
                                <div class="text-xs text-slate-600 space-y-0.5">
                                    @if($bp) <div><span class="text-slate-400 font-semibold">BP:</span> {{ $bp }}</div> @endif
                                    @if($pulse) <div><span class="text-slate-400 font-semibold">Pulse:</span> {{ $pulse }} bpm</div> @endif
                                    @if($wt) <div><span class="text-slate-400 font-semibold">Wt:</span> {{ $wt }} kg</div> @endif
                                    @if(!$bp && !$pulse && !$wt) <span class="text-slate-400 italic">Not recorded</span> @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($v->prescriptions && $v->prescriptions->count() > 0)
                                    @php $rx = $v->prescriptions->first(); @endphp
                                    <a href="{{ route('prescriptions.show', $rx->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Rx ({{ $rx->items->count() }} meds)
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400 italic">No Rx issued</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('consultations.show', $v->id) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition" title="View Clinical Record">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('patients.show', ['patient' => $v->patient_id, 'tab' => 'visits']) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition" title="Patient Profile">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="stethoscope" class="w-6 h-6"></i>
                                </div>
                                <h3 class="text-sm font-bold text-slate-800">No consultation records found</h3>
                                <p class="text-xs text-slate-500 mt-1">Start a new consultation from today's OPD queue.</p>
                                <a href="{{ route('queue.index') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-slate-800 transition">
                                    Open OPD Queue
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visits->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $visits->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
