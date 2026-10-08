@extends('layouts.app')

@section('title', 'Prescriptions History')
@section('breadcrumb', 'Prescriptions')
@section('page_title', 'Prescription Archives')

@section('content')
<div class="space-y-6">

    <!-- FILTER BAR -->
    <div class="card-custom p-5 bg-white">
        <form action="{{ route('prescriptions.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by patient name, ID, Rx number..." 
                       class="w-72 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500">
                <input type="date" name="date" value="{{ $date }}" 
                       class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500">
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition">Filter</button>
                <a href="{{ route('prescriptions.index') }}" class="px-3 py-2 border rounded-xl text-slate-600 hover:bg-slate-50 font-medium">Reset</a>
            </div>

            <a href="{{ route('prescriptions.create') }}" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Create Prescription</span>
            </a>
        </form>
    </div>

    <!-- PRESCRIPTION LIST TABLE -->
    <div class="card-custom bg-white overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Prescriptions Log</h3>
            <span class="text-xs text-slate-400">Total {{ $prescriptions->total() }} records</span>
        </div>

        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3 pl-5">Prescription No</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Patient</th>
                        <th class="p-3">Diagnosis</th>
                        <th class="p-3">Medications Count</th>
                        <th class="p-3">Doctor</th>
                        <th class="p-3 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($prescriptions as $rx)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 pl-5 font-mono font-bold text-blue-700">
                                {{ $rx->prescription_no }}
                            </td>
                            <td class="p-3 text-slate-600">{{ $rx->prescription_date->format('d M Y') }}</td>
                            <td class="p-3">
                                <a href="{{ route('patients.show', $rx->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700">
                                    {{ $rx->patient->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400 block">{{ $rx->patient->patient_id }}</span>
                            </td>
                            <td class="p-3 font-semibold text-slate-800">{{ $rx->diagnosis_summary }}</td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[10px]">
                                    {{ $rx->items->count() }} Meds
                                </span>
                            </td>
                            <td class="p-3 text-slate-600">{{ $rx->doctor->name ?? 'Doctor' }}</td>
                            <td class="p-3 pr-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('prescriptions.print', $rx->id) }}" target="_blank" title="Print" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">
                                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('prescriptions.show', $rx->id) }}" title="View" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-12 text-center text-slate-400">No prescriptions found.</td></tr>
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
