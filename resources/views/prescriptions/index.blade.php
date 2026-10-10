@extends('layouts.app')

@section('title', 'Prescriptions & Clinical Assessments')
@section('breadcrumb', 'Prescriptions')
@section('page_title', 'Prescriptions & Assessment Archives')

@section('content')
<div class="space-y-6">

    <!-- FILTER & ACTION BAR -->
    <div class="card-custom p-5 bg-white space-y-4">
        <form action="{{ route('prescriptions.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search patient, ID, Rx number, diagnosis..." 
                       class="w-64 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500 font-semibold">
                
                <!-- Assessment Type Filter -->
                <select name="type" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500 font-semibold text-slate-700">
                    <option value="">-- All Assessment Types --</option>
                    <option value="musculoskeletal" {{ $type === 'musculoskeletal' ? 'selected' : '' }}><i data-lucide="activity" class="w-3.5 h-3.5"></i> Musculoskeletal</option>
                    <option value="neurological" {{ $type === 'neurological' ? 'selected' : '' }}><i data-lucide="brain" class="w-3.5 h-3.5"></i> Neurological</option>
                </select>

                <!-- Clinic Branch Filter -->
                <select name="clinic_id" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500 font-semibold text-slate-700">
                    <option value="">-- All Clinic Branches --</option>
                    @foreach($clinics as $c)
                        <option value="{{ $c->id }}" {{ $clinicId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <input type="date" name="date" value="{{ $date }}" 
                       class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500 font-semibold text-slate-700">

                <button type="submit" class="px-4 py-2 bg-blue-700 text-white font-black rounded-xl hover:bg-blue-800 transition shadow-sm">
                    Filter
                </button>
                <a href="{{ route('prescriptions.index') }}" class="px-3 py-2 border rounded-xl text-slate-600 hover:bg-slate-50 font-bold transition">
                    Reset
                </a>
            </div>

            <!-- <div class="flex items-center gap-2">
                <a href="{{ route('prescriptions.create') }}?type=musculoskeletal" class="px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-200 font-bold rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                    <i data-lucide="activity" class="w-3.5 h-3.5"></i>
                    <span>+ Musculoskeletal</span>
                </a>
                <a href="{{ route('prescriptions.create') }}?type=neurological" class="px-3.5 py-2 bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 font-bold rounded-xl transition flex items-center gap-1.5 shadow-2xs">
                    <i data-lucide="brain" class="w-3.5 h-3.5"></i>
                    <span>+ Neurological</span>
                </a>
            </div> -->
        </form>
    </div>

    <!-- PRESCRIPTION LIST TABLE -->
    <div class="card-custom bg-white overflow-hidden shadow-sm">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="clipboard-list" class="w-4 h-4 text-blue-600"></i>
                <h3 class="font-black text-slate-900 text-sm">Clinical Prescriptions & Assessments Log</h3>
            </div>
            <span class="text-xs font-bold text-slate-400">Total {{ $prescriptions->total() }} records</span>
        </div>

        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-black uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3.5 pl-5">Rx No & Date</th>
                        <th class="p-3.5">Patient Details</th>
                        <th class="p-3.5">Clinic Branch</th>
                        <th class="p-3.5">Assessment Type</th>
                        <th class="p-3.5">Diagnosis</th>
                        <th class="p-3.5">Protocol Details</th>
                        <th class="p-3.5 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($prescriptions as $rx)
                        @php
                            $clinicName = $rx->clinic->name ?? $rx->patient?->clinic?->name ?? 'Main Branch';
                            $isNeuro = ($rx->assessment_type === 'neurological');
                            $exCount = is_array($rx->prescribed_exercises) ? count($rx->prescribed_exercises) : 0;
                            $medCount = $rx->items->count();
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <!-- Rx No & Date -->
                            <td class="p-3.5 pl-5">
                                <span class="font-mono font-black text-blue-700 block">{{ $rx->prescription_no }}</span>
                                <span class="text-[11px] text-slate-500 font-semibold">{{ $rx->prescription_date->format('d M Y') }}</span>
                            </td>

                            <!-- Patient -->
                            <td class="p-3.5">
                                <a href="{{ route('patients.show', $rx->patient_id) }}" class="font-black text-slate-900 hover:text-blue-700 block text-sm">
                                    {{ $rx->patient->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $rx->patient->patient_id }} • {{ $rx->patient->age }}y, {{ $rx->patient->gender }}</span>
                            </td>

                            <!-- Clinic Branch Badge -->
                            <td class="p-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    {{ $clinicName }}
                                </span>
                            </td>

                            <!-- Assessment Type -->
                            <td class="p-3.5">
                                @if($isNeuro)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="brain" class="w-3 h-3"></i> Neurological
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                        <i data-lucide="activity" class="w-3 h-3"></i> Musculoskeletal
                                    </span>
                                @endif
                            </td>

                            <!-- Diagnosis -->
                            <td class="p-3.5 font-bold text-slate-800 max-w-xs truncate" title="{{ $rx->diagnosis_summary }}">
                                {{ $rx->diagnosis_summary }}
                            </td>

                            <!-- Protocol Details -->
                            <td class="p-3.5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($exCount > 0)
                                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-bold text-[10px] border border-emerald-200">
                                             {{ $exCount }} Exercises
                                        </span>
                                    @endif
                                    @if($medCount > 0)
                                        <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px] border border-blue-200">
                                             {{ $medCount }} Meds
                                        </span>
                                    @endif
                                    @if($rx->treatment_days)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-bold text-[10px]">
                                             {{ $rx->treatment_days }} Days
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="p-3.5 pr-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('prescriptions.print', $rx->id) }}" target="_blank" title="Print Letterhead" class="p-2 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl transition">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('prescriptions.show', $rx->id) }}" title="View Chart" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">
                                <i data-lucide="file-x" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                No clinical prescriptions or assessments found matching query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($prescriptions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
