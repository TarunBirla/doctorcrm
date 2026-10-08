@extends('layouts.app')

@section('title', 'Prescription #' . $prescription->prescription_no)
@section('breadcrumb', 'Prescriptions / ' . $prescription->prescription_no)
@section('page_title', 'Prescription Details')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <div class="flex items-center justify-between">
        <a href="{{ route('prescriptions.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Prescriptions</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('prescriptions.print', $prescription->id) }}" target="_blank" class="px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Print Official Prescription</span>
            </a>
        </div>
    </div>

    <!-- PRESCRIPTION VIEW CARD -->
    <div class="card-custom p-8 bg-white space-y-6">
        
        <!-- HEADER -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-6">
            <div>
                <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $clinic->name }}</h2>
                <p class="text-xs text-blue-700 font-semibold">{{ $clinic->tagline }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $clinic->address }}, {{ $clinic->city }} • Ph: {{ $clinic->phone }}</p>
            </div>
            <div class="text-right">
                <span class="font-mono font-bold text-blue-700 text-sm block">{{ $prescription->prescription_no }}</span>
                <span class="text-xs text-slate-500 block">Date: {{ $prescription->prescription_date->format('d M Y') }}</span>
                <span class="text-xs font-bold text-slate-800 block mt-1">{{ $prescription->doctor->name ?? $clinic->doctor_name }}</span>
            </div>
        </div>

        <!-- PATIENT INFO -->
        <div class="p-4 bg-slate-50 rounded-2xl grid grid-cols-2 md:grid-cols-4 gap-4 text-xs border border-slate-100">
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Patient</span>
                <a href="{{ route('patients.show', $prescription->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700">
                    {{ $prescription->patient->full_name }}
                </a>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Patient ID</span>
                <span class="font-mono font-bold text-slate-700">{{ $prescription->patient->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Age & Gender</span>
                <span class="font-semibold text-slate-800">{{ $prescription->patient->age }}y / {{ $prescription->patient->gender }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Blood Group</span>
                <span class="font-bold text-rose-700">{{ $prescription->patient->blood_group ?? '-' }}</span>
            </div>
        </div>

        <!-- DIAGNOSIS -->
        <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block mb-1">Diagnosis</span>
            <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-100 font-bold text-blue-900 text-xs">
                {{ $prescription->diagnosis_summary }}
            </div>
        </div>

        <!-- MEDICINES -->
        <div>
            <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block mb-2">Rx Medications</span>
            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <tr class="border-b border-slate-100">
                            <th class="p-3">#</th>
                            <th class="p-3">Medicine</th>
                            <th class="p-3">Dosage</th>
                            <th class="p-3">Frequency</th>
                            <th class="p-3">Duration</th>
                            <th class="p-3">Timing & Note</th>
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

        <!-- ADVICE & NEXT FOLLOW-UP -->
        <div class="p-4 bg-slate-50 rounded-2xl grid grid-cols-1 md:grid-cols-2 gap-4 text-xs border border-slate-100">
            <div>
                <strong class="text-slate-700 block mb-1">Clinical Advice:</strong>
                <p class="text-slate-600">{{ $prescription->advice ?? 'Take medications as directed.' }}</p>
            </div>
            <div>
                <strong class="text-slate-700 block mb-1">Next Follow-up Review:</strong>
                <span class="font-bold text-blue-800">{{ $prescription->follow_up_date ? $prescription->follow_up_date->format('d M Y') : 'SOS' }}</span>
            </div>
        </div>

    </div>

</div>
@endsection
