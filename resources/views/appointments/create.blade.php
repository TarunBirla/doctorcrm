@extends('layouts.app')

@section('title', 'Book New Appointment')
@section('breadcrumb', 'Appointments / Book')
@section('page_title', 'Clinic Appointment Booking')

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
                <p class="text-sm text-slate-500">
                    Follow the verified sequence: Select Patient → Select Clinic → Select Date → Choose Available Slot → Confirm
                </p>
            </div>
        </div>
        <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl hover:bg-emerald-100 transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i> New Patient Intake
        </a>
    </div>

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Appointment Form -->
    <form action="{{ route('appointments.store') }}" method="POST" id="appointmentBookingForm" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-8">
        @csrf

        <!-- STEP 1: PATIENT SELECTION (Doctor's Own Patients) -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
                    Patient Selection
                </h3>
                <span class="text-xs text-slate-500">
                    Showing {{ $patients->count() }} patient(s) registered under your care
                </span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Select Patient <span class="text-rose-500">*</span></label>
                <select name="patient_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                    <option value="">-- Choose Patient from your Directory --</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ (old('patient_id', $preselectedPatientId ?? '') == $patient->id) ? 'selected' : '' }}>
                            {{ $patient->patient_id }} - {{ $patient->full_name }} ({{ $patient->gender }}, {{ $patient->age }}y) - {{ $patient->mobile }}
                        </option>
                    @endforeach
                </select>
                @if($patients->isEmpty())
                    <p class="text-xs text-amber-600 mt-1">
                        No patients registered yet. <a href="{{ route('patients.create') }}" class="underline font-bold">Click here to register your first patient.</a>
                    </p>
                @endif
            </div>
        </div>

        <!-- STEP 2: CLINIC SELECTION -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
                    Practice Clinic
                </h3>
                <span class="text-xs text-slate-500">Slots are filtered specifically by clinic</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Select Practice Clinic <span class="text-rose-500">*</span></label>
                    <select name="clinic_id" id="pageClinicSelect" required onchange="handleClinicChange()" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                        @foreach($clinics as $c)
                            <option value="{{ $c->id }}" data-fee="{{ $c->consultation_fee }}" {{ (old('clinic_id', $preselectedClinicId ?? '') == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->city }}) - Fee: ₹{{ number_format($c->consultation_fee, 2) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($currentRole === 'super_admin')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Practicing Doctor <span class="text-rose-500">*</span></label>
                    <select name="doctor_id" id="pageDoctorSelect" required onchange="fetchClinicDoctorSlots()" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-slate-50/50">
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ (old('doctor_id') == $doctor->id) ? 'selected' : '' }}>
                                {{ $doctor->name }} ({{ $doctor->specialization }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="doctor_id" id="pageDoctorSelect" value="{{ $loggedInDoctor ? $loggedInDoctor->id : ($doctors->first()?->id ?? 1) }}">
                <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200">
                    <i data-lucide="user-check" class="w-5 h-5 text-blue-600"></i>
                    <div>
                        <p class="text-xs font-bold text-slate-800">{{ $loggedInDoctor ? $loggedInDoctor->name : 'Consulting Doctor' }}</p>
                        <p class="text-[11px] text-slate-500">{{ $loggedInDoctor ? $loggedInDoctor->specialization : 'OPD' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- STEP 3: DATE & LIVE TIME SLOTS -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">3</span>
                    Appointment Date & Time Slot
                </h3>
                <span id="pageSlotStatusText" class="text-xs font-bold text-slate-500">Pick date to check live availability</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Appointment Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="appointment_date" id="pageDateSelect" 
                           value="{{ old('appointment_date', $preselectedDate ?? date('Y-m-d')) }}" 
                           min="{{ date('Y-m-d') }}" required onchange="fetchClinicDoctorSlots()"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Selected Time Slot <span class="text-rose-500">*</span></label>
                    <input type="text" name="appointment_time" id="pageTimeInput" readonly 
                           value="{{ old('appointment_time', $preselectedTime ?? '') }}" required placeholder="Click a slot below"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-blue-700 bg-blue-50/50 cursor-pointer focus:outline-none">
                </div>
            </div>

            <!-- DYNAMIC SLOTS BOX -->
            <div class="p-4 bg-slate-50/70 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-4 h-4 text-blue-600"></i> Available Slots for Selected Clinic & Date:
                    </span>
                    <span id="slotCountBadge" class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                        Checking...
                    </span>
                </div>

                <div id="pageSlotsGrid" class="flex flex-wrap gap-2 min-h-[50px] items-center">
                    <span class="text-xs text-slate-400 italic">Loading clinic availability slots...</span>
                </div>
            </div>
        </div>

        <!-- STEP 4: CONSULTATION DETAILS & FEE -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">4</span>
                Consultation Details & Fee
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Appointment Type <span class="text-rose-500">*</span></label>
                    <select name="appointment_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="new" {{ old('appointment_type') == 'new' ? 'selected' : '' }}>New Patient Consultation</option>
                        <option value="follow_up" {{ old('appointment_type') == 'follow_up' ? 'selected' : '' }}>Follow-up Visit</option>
                        <option value="revisit" {{ old('appointment_type') == 'revisit' ? 'selected' : '' }}>Revisit</option>
                        <option value="emergency" {{ old('appointment_type') == 'emergency' ? 'selected' : '' }}>Emergency / Walk-in</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Consultation Fee (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="consultation_fee" id="pageFeeInput" value="{{ old('consultation_fee', $clinics->first()?->consultation_fee ?? '800.00') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Chief Complaint / Reason</label>
                    <input type="text" name="reason" placeholder="e.g. Back pain, post-op consultation" value="{{ old('reason') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Internal Remarks (Optional)</label>
                <textarea name="notes" rows="2" placeholder="Special doctor observations or instructions..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-[#0a2540] text-white text-sm font-bold shadow-sm hover:bg-slate-800 transition flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Confirm Appointment & Generate Token
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        handleClinicChange();
    });

    function handleClinicChange() {
        const clinicSelect = document.getElementById('pageClinicSelect');
        const feeInput = document.getElementById('pageFeeInput');
        if (clinicSelect && feeInput) {
            const opt = clinicSelect.options[clinicSelect.selectedIndex];
            if (opt && opt.dataset.fee) {
                feeInput.value = opt.dataset.fee;
            }
        }
        fetchClinicDoctorSlots();
    }

    function fetchClinicDoctorSlots() {
        const clinicSelect = document.getElementById('pageClinicSelect');
        const docSelect = document.getElementById('pageDoctorSelect');
        const dateInput = document.getElementById('pageDateSelect');
        const slotsGrid = document.getElementById('pageSlotsGrid');
        const statusText = document.getElementById('pageSlotStatusText');
        const countBadge = document.getElementById('slotCountBadge');
        const timeInput = document.getElementById('pageTimeInput');

        if (!clinicSelect || !dateInput || !slotsGrid) return;

        const clinicId = clinicSelect.value;
        const docId = docSelect ? docSelect.value : '';
        const apptDate = dateInput.value;

        if (!clinicId || !apptDate) {
            slotsGrid.innerHTML = '<span class="text-xs text-slate-400 italic">Select clinic and date above to load available time slots.</span>';
            if (statusText) statusText.innerText = 'Pick clinic & date to view timings';
            return;
        }

        slotsGrid.innerHTML = '<span class="text-xs text-blue-600 animate-pulse font-semibold">Calculating available slots for this clinic...</span>';
        if (statusText) statusText.innerText = 'Loading...';

        fetch(`/api/doctor-slots?doctor_id=${encodeURIComponent(docId)}&clinic_id=${encodeURIComponent(clinicId)}&date=${encodeURIComponent(apptDate)}`)
            .then(res => res.json())
            .then(res => {
                if (res.is_available === false) {
                    slotsGrid.innerHTML = `<span class="text-xs text-amber-700 font-semibold p-2 bg-amber-50 rounded-lg border border-amber-200">${res.message || 'Clinic is closed / Doctor unavailable on this day.'}</span>`;
                    if (statusText) statusText.innerText = 'Clinic Unavailable';
                    if (countBadge) countBadge.innerText = '0 Available';
                    return;
                }

                if (res.error) {
                    slotsGrid.innerHTML = `<span class="text-xs text-rose-500">${res.error}</span>`;
                    return;
                }

                if (countBadge) {
                    countBadge.innerText = `${res.available_count} Available (${res.booked_count} Booked)`;
                }
                if (statusText) {
                    statusText.innerText = `${res.day}, ${res.date}`;
                }

                if (!res.slots || res.slots.length === 0) {
                    slotsGrid.innerHTML = '<span class="text-xs text-slate-400 italic">No schedule configured for this day.</span>';
                    return;
                }

                let html = '';
                res.slots.forEach(slot => {
                    if (slot.is_booked) {
                        html += `<button type="button" disabled title="Already booked by another patient (Double-booking protected)" 
                            class="px-3 py-1.5 text-xs font-bold rounded-xl bg-rose-50 border border-rose-200 text-rose-400 cursor-not-allowed line-through opacity-75">
                            ${slot.time} (Booked)
                        </button>`;
                    } else if (slot.is_blocked) {
                        html += `<button type="button" disabled title="Blocked: ${slot.reason || 'Unavailable'}" 
                            class="px-3 py-1.5 text-xs font-bold rounded-xl bg-amber-50 border border-amber-200 text-amber-600 cursor-not-allowed opacity-75">
                            ${slot.time} (Blocked)
                        </button>`;
                    } else {
                        const isSelected = (timeInput && timeInput.value === slot.time);
                        html += `<button type="button" onclick="selectPageSlot('${slot.time}', '${slot.label}')" 
                            id="page_slot_${slot.time.replace(':', '_')}"
                            class="page-slot-btn px-3 py-1.5 text-xs font-bold rounded-xl border transition cursor-pointer ${isSelected ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-500/20' : 'bg-white hover:bg-emerald-50 text-emerald-800 border-emerald-300 hover:border-emerald-500'}">
                            ${slot.label || slot.time}
                        </button>`;
                    }
                });

                slotsGrid.innerHTML = html;
            })
            .catch(err => {
                slotsGrid.innerHTML = '<span class="text-xs text-rose-500">Failed to load clinic slots. Please check connection.</span>';
                console.error(err);
            });
    }

    function selectPageSlot(slotTime, slotLabel) {
        const timeInput = document.getElementById('pageTimeInput');
        if (timeInput) timeInput.value = slotTime;

        document.querySelectorAll('#pageSlotsGrid .page-slot-btn').forEach(btn => {
            btn.className = 'page-slot-btn px-3 py-1.5 text-xs font-bold rounded-xl border transition bg-white hover:bg-emerald-50 text-emerald-800 border-emerald-300 hover:border-emerald-500 cursor-pointer';
        });
        const selectedBtn = document.getElementById(`page_slot_${slotTime.replace(':', '_')}`);
        if (selectedBtn) {
            selectedBtn.className = 'page-slot-btn px-3 py-1.5 text-xs font-bold rounded-xl border transition bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-500/20 cursor-pointer';
        }
    }
</script>
@endpush
