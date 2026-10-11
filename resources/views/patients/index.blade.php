@extends('layouts.app')

@section('title', 'Patient Management')
@section('breadcrumb', 'Patients')
@section('page_title', 'Patient Directory & Master Records')

@section('content')
@php
    $isStaff = \App\Models\User::isStaffRole(session('current_role', auth()->user()->role ?? 'super_admin'));
@endphp
<div class="space-y-6">

    <!-- TOP KPI STATS -->
    <div class="grid grid-cols-1 {{ $isStaff ? 'md:grid-cols-3' : 'md:grid-cols-4' }} gap-4">
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

        @if(!$isStaff)
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Patients with Dues</span>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">{{ $patientsWithDues }}</div>
        </div>
        @endif
    </div>

    <!-- SEARCH & FILTER BAR -->
    <div class="card-custom p-5 bg-white">
        <form action="{{ route('patients.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 text-xs items-end">
            
            <div class="lg:col-span-2">
                <label class="block font-semibold text-slate-600 mb-1">Search Patient</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Name, ID (PAT-000001), phone, city..." 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 outline-none focus:bg-white focus:border-blue-500 font-semibold">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Clinic Branch</label>
                <select name="clinic_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
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
                <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                    <option value="">All Categories</option>
                    @foreach($categories as $cg)
                        <option value="{{ $cg->id }}" {{ (string) request('category_id', $categoryId ?? '') === (string) $cg->id ? 'selected' : '' }}>
                            {{ $cg->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Blood Group</label>
                <select name="blood_group" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                    <option value="">All Blood Groups</option>
                    @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                        <option value="{{ $bg }}" {{ $bloodGroup === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Gender</label>
                <select name="gender" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                    <option value="">All Genders</option>
                    <option value="Male" {{ $gender === 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ $gender === 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ $gender === 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-sm text-center">
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
                        <th class="p-3.5">Sessions & Recovery %</th>
                        @if(!$isStaff)
                        <th class="p-3.5">Package & Due</th>
                        @endif
                        <th class="p-3.5">Last Visit / Session</th>
                        <th class="p-3.5 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($patients as $patient)
                        @php
                            $validApts = $patient->appointments ? $patient->appointments->whereNotIn('status', ['cancelled']) : collect();
                            $totalSess = $validApts->count();
                            $doneSess = $validApts->where('status', 'completed')->count();
                            $recPct = (int) ($patient->recovery_percentage ?? ($validApts->last()?->recovery_percentage ?? 0));
                            $pkgTotal = (float) $validApts->sum('consultation_fee');
                        @endphp
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
                                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                            <span class="text-[11px] font-mono text-slate-400">{{ $patient->patient_id }}</span>
                                            @if($patient->category)
                                                <span class="text-[9px] px-1.5 py-0.2 bg-blue-50 text-blue-700 font-bold rounded border border-blue-200">{{ $patient->category->name }}</span>
                                            @endif
                                            @if($patient->clinic)
                                                <span class="text-[9px] px-1.5 py-0.2 bg-slate-100 text-slate-600 font-bold rounded">{{ $patient->clinic->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="font-semibold text-slate-800">{{ $patient->gender }}</span>
                                <span class="text-slate-400">• {{ $patient->age }} yrs</span>
                                @if($patient->blood_group)
                                    <span class="ml-1 px-1.5 py-0.2 rounded font-bold text-[9px] bg-rose-50 text-rose-700 border border-rose-100">{{ $patient->blood_group }}</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                <div class="font-semibold text-slate-800">{{ $patient->mobile }}</div>
                                <span class="text-[11px] text-slate-400">{{ $patient->city ?? 'Indore' }}</span>
                            </td>
                            <td class="p-3.5">
                                @if($totalSess > 0)
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold {{ $doneSess >= $totalSess ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                            {{ $doneSess }} / {{ $totalSess }} Days Done
                                        </span>
                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-extrabold {{ $recPct >= 100 ? 'bg-emerald-600 text-white' : ($recPct >= 75 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200') }}">
                                            {{ $recPct >= 100 ? '🌟 100% Cured' : $recPct . '% Recovery' }}
                                        </span>
                                    </div>
                                    <div class="w-36 bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="{{ $recPct >= 75 ? 'bg-emerald-500' : 'bg-indigo-500' }} h-1.5 rounded-full" style="width: {{ max(6, $recPct) }}%"></div>
                                    </div>
                                @else
                                    <span class="text-slate-400 text-[11px]">No package booked</span>
                                @endif
                            </td>
                            @if(!$isStaff)
                            <td class="p-3.5">
                                @if($pkgTotal > 0)
                                    <div class="font-bold text-slate-900">₹{{ number_format($pkgTotal, 0) }} <span class="text-[10px] font-semibold text-slate-400">({{ $totalSess }}d)</span></div>
                                @endif
                                @if($patient->outstanding_balance > 0)
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full font-extrabold text-[10px] bg-rose-100 text-rose-800">
                                        Due: ₹{{ number_format($patient->outstanding_balance, 0) }}
                                    </span>
                                @else
                                    <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full font-bold text-[10px] bg-emerald-50 text-emerald-700">
                                        All Clear
                                    </span>
                                @endif
                            </td>
                            @endif
                            <td class="p-3.5 text-slate-500">
                                @if($validApts->last())
                                    <span class="font-semibold text-slate-800">{{ $validApts->last()->appointment_date->format('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 block">Session {{ $totalSess }} ({{ ucfirst($validApts->last()->status) }})</span>
                                @elseif($patient->last_visit)
                                    <span class="font-semibold text-slate-800">{{ $patient->last_visit->visit_date->format('d M Y') }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $patient->last_visit->visit_type }}</span>
                                @else
                                    <span class="text-slate-300">No visits yet</span>
                                @endif
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('patients.edit', $patient->id) }}" title="Edit Patient, Update Recovery % & Extend +Days"
                                       class="px-2.5 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 rounded-xl font-bold text-[11px] transition inline-flex items-center gap-1">
                                        <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                                        <span>Edit</span>
                                    </a>
                                    <a href="{{ route('patients.show', $patient->id) }}" title="Open Patient CRM Profile"
                                       class="p-1.5 bg-slate-50 text-blue-700 hover:bg-blue-100 rounded-xl transition">
                                        <i data-lucide="folder-open" class="w-3.5 h-3.5"></i>
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
