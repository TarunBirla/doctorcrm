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
                <i data-lucide="calendar" class="w-4 h-4 text-primary"></i> 2. Doctor & Slot Availability
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Doctor <span class="text-rose-500">*</span></label>
                    <select name="doctor_id" id="pageDoctorSelect" required onchange="fetchPageDoctorSlots()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="">-- Choose Doctor --</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ (old('doctor_id') == $doctor->id) ? 'selected' : '' }}>
                                {{ $doctor->name }} ({{ $doctor->specialization }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="appointment_date" id="pageDateSelect" value="{{ old('appointment_date', request('date', date('Y-m-d'))) }}" required onchange="fetchPageDoctorSlots()"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Time Slot <span class="text-rose-500">*</span> <span id="pageSelectedSlotBadge" class="text-blue-600 font-bold ml-1"></span></label>
                    <input type="time" name="appointment_time" id="pageTimeInput" value="{{ old('appointment_time', request('time', date('H:i'))) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                </div>
            </div>

            <!-- DYNAMIC SLOTS BOX -->
            <div class="p-4 bg-slate-50/70 rounded-xl border border-slate-200">
                <div class="flex items-center justify-between mb-2.5">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-4 h-4 text-primary"></i> Doctor OPD Time Slots:
                    </span>
                    <span id="pageSlotStatusText" class="text-xs text-slate-500">Pick doctor & date to see available timings</span>
                </div>
                <div id="pageSlotsGrid" class="flex flex-wrap gap-2">
                    <span class="text-xs text-slate-400 italic">Select a doctor and date above to load available time slots.</span>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchPageDoctorSlots();
    });

    function fetchPageDoctorSlots() {
        const docSelect = document.getElementById('pageDoctorSelect');
        const dateInput = document.getElementById('pageDateSelect');
        const slotsGrid = document.getElementById('pageSlotsGrid');
        const statusText = document.getElementById('pageSlotStatusText');
        const timeInput = document.getElementById('pageTimeInput');

        if (!docSelect || !dateInput || !slotsGrid) return;

        const docId = docSelect.value;
        const apptDate = dateInput.value;

        if (!docId || !apptDate) {
            slotsGrid.innerHTML = '<span class="text-xs text-slate-400 italic">Select doctor and date above to load available time slots.</span>';
            if (statusText) statusText.innerText = 'Pick doctor & date to see available timings';
            return;
        }

        slotsGrid.innerHTML = '<span class="text-xs text-blue-600 animate-pulse font-semibold">Calculating live schedule slots...</span>';
        if (statusText) statusText.innerText = 'Loading...';

        fetch(`/api/doctor-slots?doctor_id=${encodeURIComponent(docId)}&date=${encodeURIComponent(apptDate)}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    slotsGrid.innerHTML = `<span class="text-xs text-rose-500">${res.message || 'Error loading slots'}</span>`;
                    if (statusText) statusText.innerText = 'Unavailable';
                    return;
                }

                if (statusText) {
                    statusText.innerHTML = `<span class="text-emerald-700 font-bold">${res.available_count} Available</span> • <span class="text-rose-600 font-bold">${res.booked_count} Booked</span>`;
                }

                if (!res.slots || res.slots.length === 0) {
                    slotsGrid.innerHTML = '<span class="text-xs text-slate-400 italic">No schedule configured for this day.</span>';
                    return;
                }

                let html = '';
                res.slots.forEach(slot => {
                    if (slot.is_booked) {
                        html += `<button type="button" disabled title="Slot already booked by another appointment" 
                            class="px-3 py-1.5 text-xs font-bold rounded-xl bg-rose-50 border border-rose-200 text-rose-400 cursor-not-allowed line-through opacity-70">
                            ${slot.time} (Booked)
                        </button>`;
                    } else {
                        const isSelected = (timeInput && timeInput.value === slot.time);
                        html += `<button type="button" onclick="selectPageSlot('${slot.time}')" 
                            id="page_slot_${slot.time.replace(':', '_')}"
                            class="page-slot-btn px-3 py-1.5 text-xs font-bold rounded-xl border transition ${isSelected ? 'bg-primary text-white border-primary shadow-sm ring-2 ring-primary/20' : 'bg-white hover:bg-emerald-50 text-emerald-800 border-emerald-300 hover:border-emerald-500'}">
                            ${slot.time}
                        </button>`;
                    }
                });

                slotsGrid.innerHTML = html;
            })
            .catch(err => {
                slotsGrid.innerHTML = '<span class="text-xs text-rose-500">Failed to load doctor slots</span>';
                console.error(err);
            });
    }

    function selectPageSlot(slotTime) {
        const timeInput = document.getElementById('pageTimeInput');
        const badge = document.getElementById('pageSelectedSlotBadge');
        if (timeInput) timeInput.value = slotTime;
        if (badge) badge.innerText = `Selected: ${slotTime}`;

        document.querySelectorAll('#pageSlotsGrid .page-slot-btn').forEach(btn => {
            btn.className = 'page-slot-btn px-3 py-1.5 text-xs font-bold rounded-xl border transition bg-white hover:bg-emerald-50 text-emerald-800 border-emerald-300 hover:border-emerald-500';
        });
        const selectedBtn = document.getElementById(`page_slot_${slotTime.replace(':', '_')}`);
        if (selectedBtn) {
            selectedBtn.className = 'page-slot-btn px-3 py-1.5 text-xs font-bold rounded-xl border transition bg-primary text-white border-primary shadow-sm ring-2 ring-primary/20';
        }
    }
</script>
@endpush
