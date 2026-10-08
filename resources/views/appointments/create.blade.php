@extends('layouts.app')

@section('title', 'Book New Appointment')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('appointments.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Book New Appointment</h1>
                <p class="text-sm text-slate-500">Schedule a patient consultation with the doctor</p>
            </div>
        </div>
        <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i> New Patient Registration
        </a>
    </div>

    <!-- Appointment Form -->
    <form action="{{ route('appointments.store') }}" method="POST" class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-8">
        @csrf

        <!-- 1. Patient Selection -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-primary"></i> 1. Patient Details
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Select Patient <span class="text-rose-500">*</span></label>
                    <select name="patient_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="">-- Choose Existing Patient --</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" {{ (old('patient_id', request('patient_id')) == $patient->id) ? 'selected' : '' }}>
                                {{ $patient->patient_id }} - {{ $patient->full_name }} ({{ $patient->gender }}, {{ $patient->age }}y) - {{ $patient->mobile }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- 2. Doctor & Schedule -->
        <div class="space-y-4 pt-6 border-t border-slate-100">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4 text-primary"></i> 2. Doctor & Slot
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Doctor <span class="text-rose-500">*</span></label>
                    <select name="doctor_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ (old('doctor_id') == $doctor->id) ? 'selected' : '' }}>
                                {{ $doctor->name }} ({{ $doctor->specialization }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="appointment_date" value="{{ old('appointment_date', request('date', date('Y-m-d'))) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Time Slot <span class="text-rose-500">*</span></label>
                    <input type="time" name="appointment_time" value="{{ old('appointment_time', request('time', date('H:i'))) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
            </div>
        </div>

        <!-- 3. Type, Reason & Fee -->
        <div class="space-y-4 pt-6 border-t border-slate-100">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                <i data-lucide="activity" class="w-4 h-4 text-primary"></i> 3. Consultation Classification & Fee
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Appointment Type <span class="text-rose-500">*</span></label>
                    <select name="appointment_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="new_consultation" {{ old('appointment_type') == 'new_consultation' ? 'selected' : '' }}>New Consultation</option>
                        <option value="follow_up" {{ old('appointment_type') == 'follow_up' ? 'selected' : '' }}>Follow-up</option>
                        <option value="revisit" {{ old('appointment_type') == 'revisit' ? 'selected' : '' }}>Revisit</option>
                        <option value="emergency" {{ old('appointment_type') == 'emergency' ? 'selected' : '' }}>Emergency</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status <span class="text-rose-500">*</span></label>
                    <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="waiting" {{ old('status') == 'waiting' ? 'selected' : '' }}>Waiting in OPD Queue</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Consultation Fee (₹)</label>
                    <input type="number" step="0.01" name="consultation_fee" value="{{ old('consultation_fee', '800.00') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Payment Status</label>
                    <select name="payment_status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="unpaid">Unpaid (Pay at Desk)</option>
                        <option value="paid">Paid Full (₹800)</option>
                        <option value="partially_paid">Partially Paid</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Appointment Reason / Symptoms</label>
                    <input type="text" name="reason" placeholder="e.g., Persistent dry cough, seasonal allergy checkup" value="{{ old('reason') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Internal Notes (Optional)</label>
                <textarea name="notes" rows="2" placeholder="Any special instructions or reference notes..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white text-sm font-bold shadow-sm hover:bg-slate-800 transition flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i> Confirm & Generate Token
            </button>
        </div>
    </form>
</div>
@endsection
