@extends('layouts.app')

@section('title', 'Add New Clinic')
@section('page_title', 'Register New Clinic')
@section('breadcrumb', 'Clinics / Add')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Register Practice Clinic</h1>
            <p class="text-sm text-slate-500">Configure clinic details, consulting charges, and standard appointment slots</p>
        </div>
        <a href="{{ route('clinics.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Clinics
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('clinics.store') }}" class="space-y-6">
        @csrf

        <!-- 1. Basic Details -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="building" class="w-4 h-4 text-blue-600"></i> Clinic Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Clinic / Hospital Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. CarePoint Super Speciality Clinic" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Tagline / Subheading</label>
                    <input type="text" name="tagline" value="{{ old('tagline') }}" placeholder="e.g. Center for Orthopaedic & Physiotherapy Excellence"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                @if($currentRole === 'super_admin')
                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Managing / Practicing Doctor <span class="text-rose-500">*</span></label>
                    <select name="doctor_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required>
                        <option value="">-- Select Doctor --</option>
                        @foreach($allDoctors as $doc)
                            <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                                {{ $doc->name }} ({{ $doc->specialization }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Official Phone / Mobile <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 98100 00000" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Official Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="clinic@carepoint.com" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- 2. Location & Address -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-blue-600"></i> Location & Address
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-1.5 md:col-span-3">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Full Clinic Address <span class="text-rose-500">*</span></label>
                    <textarea name="address" rows="2" placeholder="Suite / SCO number, Street, Landmark..." required
                              class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('address') }}</textarea>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">City <span class="text-rose-500">*</span></label>
                    <input type="text" name="city" value="{{ old('city') }}" placeholder="e.g. Gurugram" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">State <span class="text-rose-500">*</span></label>
                    <input type="text" name="state" value="{{ old('state', 'Haryana') }}" placeholder="e.g. Haryana" required
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Pincode</label>
                    <input type="text" name="pincode" value="{{ old('pincode') }}" placeholder="122001"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- 3. Consultation Fee & Slot Configuration -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i> Clinic Operations & Slot Timings
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Consultation Fee (₹) <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">₹</span>
                        <input type="number" step="0.01" name="consultation_fee" value="{{ old('consultation_fee', '800') }}" required
                               class="w-full pl-8 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Appointment Slot Duration <span class="text-rose-500">*</span></label>
                    <select name="appointment_duration" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-semibold">
                        <option value="10" {{ old('appointment_duration') == 10 ? 'selected' : '' }}>10 Minutes</option>
                        <option value="15" {{ old('appointment_duration', 15) == 15 ? 'selected' : '' }}>15 Minutes (Default)</option>
                        <option value="20" {{ old('appointment_duration') == 20 ? 'selected' : '' }}>20 Minutes</option>
                        <option value="30" {{ old('appointment_duration') == 30 ? 'selected' : '' }}>30 Minutes</option>
                        <option value="45" {{ old('appointment_duration') == 45 ? 'selected' : '' }}>45 Minutes</option>
                        <option value="60" {{ old('appointment_duration') == 60 ? 'selected' : '' }}>60 Minutes</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Working Days</label>
                    <input type="text" name="working_days" value="{{ old('working_days', 'Mon - Sat') }}" placeholder="e.g. Mon - Sat"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Working Hours</label>
                    <input type="text" name="working_hours" value="{{ old('working_hours', '09:00 AM - 05:00 PM') }}" placeholder="e.g. 09:00 AM - 05:00 PM"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="space-y-1.5 md:col-span-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Lunch / Break Hours</label>
                    <input type="text" name="break_hours" value="{{ old('break_hours', '01:00 PM - 02:00 PM') }}" placeholder="e.g. 01:00 PM - 02:00 PM"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('clinics.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold shadow-sm hover:bg-blue-700 transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Save & Initialize Clinic
            </button>
        </div>
    </form>
</div>
@endsection
