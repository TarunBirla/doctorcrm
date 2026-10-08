@extends('layouts.app')

@section('title', 'Clinics & Branches')
@section('page_title', 'Clinic & Branch Management')
@section('breadcrumb', 'Clinics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                {{ $currentRole === 'doctor' ? 'My Practice Clinics' : 'All Clinics & Branches' }}
            </h1>
            <p class="text-sm text-slate-500">
                Register multiple clinics, set custom consultation fees, slot durations, and working hours per clinic
            </p>
        </div>
        <a href="{{ route('clinics.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#0a2540] text-white text-sm font-bold shadow-sm hover:bg-slate-800 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i> Add New Clinic
        </a>
    </div>

    <!-- Filter / Stats Bar -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <i data-lucide="building-2" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Total Clinics</p>
                <h3 class="text-2xl font-bold text-slate-900">{{ $clinics->total() }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Active Practices</p>
                <h3 class="text-2xl font-bold text-emerald-600">{{ $clinics->where('is_active', true)->count() }}</h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Schedule Control</p>
                <a href="{{ route('slots.manage') }}" class="text-sm font-bold text-purple-700 hover:underline flex items-center gap-1 mt-0.5">
                    Open Slot Manager <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Clinics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($clinics as $clinic)
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-md transition">
            <!-- Card Header -->
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                            <i data-lucide="building" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-base leading-snug">{{ $clinic->name }}</h3>
                            <p class="text-xs text-slate-500">{{ $clinic->tagline ?? 'Primary Healthcare Facility' }}</p>
                        </div>
                    </div>
                    @if($clinic->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                            Inactive
                        </span>
                    @endif
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-5 space-y-3.5 flex-1 text-xs text-slate-600">
                <!-- Doctor Practicing -->
                <div class="flex items-center gap-2 text-slate-700">
                    <i data-lucide="user-check" class="w-4 h-4 text-blue-600 shrink-0"></i>
                    <span><strong>Doctor:</strong> {{ $clinic->doctor ? $clinic->doctor->name : ($clinic->doctor_name ?? 'Not Assigned') }}</span>
                </div>

                <!-- Location -->
                <div class="flex items-start gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-400 shrink-0 mt-0.5"></i>
                    <span>{{ $clinic->address }}, {{ $clinic->city }}, {{ $clinic->state }} {{ $clinic->pincode }}</span>
                </div>

                <!-- Contact -->
                <div class="flex items-center gap-2">
                    <i data-lucide="phone" class="w-4 h-4 text-slate-400 shrink-0"></i>
                    <span>{{ $clinic->phone }} | {{ $clinic->email }}</span>
                </div>

                <!-- Fees & Duration Grid -->
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Consultation Fee</span>
                        <span class="font-bold text-sm text-slate-800">₹{{ number_format($clinic->consultation_fee, 2) }}</span>
                    </div>
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Slot Duration</span>
                        <span class="font-bold text-sm text-slate-800">{{ $clinic->appointment_duration }} mins</span>
                    </div>
                </div>

                <!-- Working Hours -->
                <div class="bg-blue-50/50 p-2.5 rounded-xl border border-blue-100/60 flex items-center justify-between text-[11px]">
                    <span class="text-blue-800 font-semibold flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-600"></i>
                        {{ $clinic->working_days ?? 'Mon - Sat' }}
                    </span>
                    <span class="text-slate-600 font-semibold">
                        {{ $clinic->working_hours ?? '09:00 AM - 05:00 PM' }}
                    </span>
                </div>
            </div>

            <!-- Card Actions -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/30 flex items-center justify-between gap-2">
                <a href="{{ route('slots.manage', ['clinic_id' => $clinic->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition" title="Manage Time Slots for this clinic">
                    <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i> Slots
                </a>

                <div class="flex items-center gap-1.5">
                    <a href="{{ route('clinics.edit', $clinic->id) }}" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition" title="Edit Clinic">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>

                    <form action="{{ route('clinics.toggle-status', $clinic->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition" title="Toggle Active/Inactive">
                            <i data-lucide="power" class="w-4 h-4 {{ $clinic->is_active ? 'text-emerald-600' : 'text-slate-400' }}"></i>
                        </button>
                    </form>

                    <form action="{{ route('clinics.destroy', $clinic->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete clinic \'{{ $clinic->name }}\'?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-1.5 rounded-lg border border-slate-200 text-rose-600 hover:bg-rose-50 transition" title="Delete Clinic">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl border border-dashed border-slate-300 p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-4">
                <i data-lucide="building-2" class="w-8 h-8"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">No Clinics Registered Yet</h3>
            <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                Start by adding your practice clinic. You can configure multiple clinics, customized consultation fees, and separate appointment time slots.
            </p>
            <a href="{{ route('clinics.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-sm shadow-sm hover:bg-blue-700 transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Register First Clinic
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($clinics->hasPages())
    <div class="mt-6">
        {{ $clinics->links() }}
    </div>
    @endif
</div>
@endsection
