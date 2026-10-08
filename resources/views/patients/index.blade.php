@extends('layouts.app')

@section('title', 'Patient Management')
@section('breadcrumb', 'Patients')
@section('page_title', 'Patient Directory & Master Records')

@section('content')
<div class="space-y-6">

    <!-- TOP KPI STATS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Registered</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $totalPatients }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Male Patients</span>
            <div class="text-3xl font-extrabold text-blue-700 tracking-tight mt-1">{{ $malePatients }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Female Patients</span>
            <div class="text-3xl font-extrabold text-purple-700 tracking-tight mt-1">{{ $femalePatients }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Patients with Dues</span>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">{{ $patientsWithDues }}</div>
        </div>
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div class="card-custom p-5 bg-white">
        <form action="{{ route('patients.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-3 text-xs items-end">
            
            <div class="md:col-span-2">
                <label class="block font-semibold text-slate-600 mb-1">Search Patient</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, ID (PAT-000001), phone, city..." 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 outline-none focus:bg-white focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Blood Group</label>
                <select name="blood_group" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500">
                    <option value="">All Blood Groups</option>
                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                        <option value="{{ $bg }}" {{ $bloodGroup === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Gender</label>
                <select name="gender" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500">
                    <option value="">All Genders</option>
                    <option value="Male" {{ $gender === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $gender === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ $gender === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Due Filter</label>
                <select name="has_due" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500">
                    <option value="">All Patients</option>
                    <option value="yes" {{ $hasDue === 'yes' ? 'selected' : '' }}>Has Pending Dues</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-sm text-center">
                    Filter
                </button>
                <a href="{{ route('patients.index') }}" class="px-3 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-semibold text-center">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- PATIENT DIRECTORY TABLE CARD -->
    <div class="card-custom bg-white overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Patient Master List</h3>
                <p class="text-xs text-slate-400">Total of {{ $totalPatients }} patients registered in clinic system</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('patients.export') }}" class="px-3 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold flex items-center gap-1.5">
                    <i data-lucide="download" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('patients.create') }}" class="flex items-center gap-1.5 px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                    <span>Register Patient</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-3.5 pl-5">Patient Details</th>
                        <th class="p-3.5">Gender & Age</th>
                        <th class="p-3.5">Contact</th>
                        <th class="p-3.5">Blood</th>
                        <th class="p-3.5">Medical History</th>
                        <th class="p-3.5">Due Balance</th>
                        <th class="p-3.5">Last Visit</th>
                        <th class="p-3.5 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 pl-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('patients.show', $patient->id) }}" class="font-bold text-slate-900 hover:text-blue-700 block">
                                            {{ $patient->full_name }}
                                        </a>
                                        <span class="text-[11px] font-mono text-slate-400">{{ $patient->patient_id }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="font-semibold text-slate-800">{{ $patient->gender }}</span>
                                <span class="text-slate-400">• {{ $patient->age }} yrs</span>
                            </td>
                            <td class="p-3.5">
                                <div class="font-semibold text-slate-800">{{ $patient->mobile }}</div>
                                <span class="text-[11px] text-slate-400">{{ $patient->city ?? 'Gurugram' }}</span>
                            </td>
                            <td class="p-3.5">
                                @if($patient->blood_group)
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-rose-50 text-rose-700 border border-rose-100">
                                        {{ $patient->blood_group }}
                                    </span>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 max-w-xs truncate text-slate-600">
                                @if($patient->medicalHistory && ($patient->medicalHistory->conditions || $patient->medicalHistory->allergies))
                                    <span class="font-semibold text-slate-800">{{ $patient->medicalHistory->conditions ?? 'None' }}</span>
                                    @if($patient->medicalHistory->allergies)
                                        <span class="text-rose-600 block text-[10px] font-semibold">⚠️ Allergy: {{ $patient->medicalHistory->allergies }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">No conditions logged</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($patient->outstanding_balance > 0)
                                    <span class="px-2 py-0.5 rounded-full font-extrabold text-[10px] bg-rose-100 text-rose-800">
                                        Due: ₹{{ number_format($patient->outstanding_balance, 2) }}
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full font-bold text-[10px] bg-emerald-50 text-emerald-700">
                                        All Clear
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-slate-500">
                                @if($patient->last_visit)
                                    <span class="font-semibold text-slate-800">{{ $patient->last_visit->visit_date->format('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $patient->last_visit->visit_type }}</span>
                                @else
                                    <span class="text-slate-300">No visits yet</span>
                                @endif
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('patients.show', $patient->id) }}" title="Open Patient CRM" class="p-1.5 bg-slate-50 text-blue-700 hover:bg-blue-100 rounded-lg transition">
                                        <i data-lucide="folder-open" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('consultations.create', ['patient_id' => $patient->id]) }}" title="Start Consultation" class="p-1.5 bg-slate-50 text-emerald-700 hover:bg-emerald-100 rounded-lg transition">
                                        <i data-lucide="stethoscope" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('patients.edit', $patient->id) }}" title="Edit Details" class="p-1.5 bg-slate-50 text-slate-600 hover:bg-slate-200 rounded-lg transition">
                                        <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">
                                No patients found matching your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($patients->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                {{ $patients->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
