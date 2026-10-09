@extends('layouts.app')

@section('title', 'Book Physiotherapy Appointment')
@section('breadcrumb', 'Appointments / Book')
@section('page_title', 'Physiotherapy Session & Package Booking')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('appointments.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Book Physiotherapy Appointment</h1>
                <p class="text-xs text-slate-500 font-semibold">
                    Clinic-wise session package booking: Select Clinic → Select Category → Select Patient → Treatment Days & Auto Fee
                </p>
            </div>
        </div>
        <a href="{{ route('patients.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition">
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

    <!-- Main Booking Form -->
    <form action="{{ route('appointments.store') }}" method="POST" id="appointmentBookingForm" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-7">
        @csrf

        <!-- STEP 1: CLINIC SELECTION -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">1</span>
                    Select Practice Clinic
                </h3>
                <span class="text-xs text-slate-500 font-semibold">Appointment is strictly clinic-wise</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Practice Clinic <span class="text-rose-500">*</span></label>
                    <select name="clinic_id" id="pageClinicSelect" required onchange="handleClinicChange()" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-slate-50/50 text-slate-800">
                        <option value="">-- Choose Clinic --</option>
                        @foreach($clinics as $c)
                            <option value="{{ $c->id }}" data-fee="{{ $c->consultation_fee }}" {{ (old('clinic_id', $preselectedClinicId ?? '') == $c->id) ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->city ?? 'Clinic' }}) - Fee: ₹{{ number_format($c->consultation_fee, 0) }}/day
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($currentRole === 'super_admin')
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Practicing Doctor <span class="text-rose-500">*</span></label>
                    <select name="doctor_id" id="pageDoctorSelect" required 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-slate-50/50 text-slate-800">
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
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold">
                        <i data-lucide="stethoscope" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-800">{{ $loggedInDoctor ? $loggedInDoctor->name : 'Consulting Doctor' }}</p>
                        <p class="text-[11px] text-slate-500 font-semibold">{{ $loggedInDoctor ? ($loggedInDoctor->specialization ?? 'Physiotherapist') : 'OPD' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- STEP 2: TREATMENT CATEGORY SELECTION -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">2</span>
                    Treatment Category
                </h3>
                <span class="text-xs text-slate-500 font-semibold">Specialized rehabilitation domain</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Therapy Category <span class="text-rose-500">*</span></label>
                <select name="category_id" id="pageCategorySelect" required 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-slate-50/50 text-slate-800">
                    <option value="">-- Select Physiotherapy Treatment Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ (old('category_id', $preselectedCategoryId ?? '') == $cat->id) ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- STEP 3: PATIENT SELECTION -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">3</span>
                    Patient Selection
                </h3>
                <span class="text-xs text-slate-500 font-semibold">
                    {{ $patients->count() }} patient(s) registered under your care
                </span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Select Patient <span class="text-rose-500">*</span></label>
                <select name="patient_id" id="pagePatientSelect" onchange="handlePagePatientChange()" required 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-slate-50/50 text-slate-800">
                    <option value="">-- Choose Patient from your Directory --</option>
                    @foreach($patients as $patient)
                        @php
                            $lastAppt = $patient->appointments()->latest()->first();
                            $resClinicId = $patient->clinic_id ?? $lastAppt?->clinic_id;
                            $resCatId = $patient->category_id ?? $lastAppt?->category_id;
                            $resRecovery = $patient->recovery_percentage ?? $lastAppt?->recovery_percentage ?? 0;
                            $resDays = $lastAppt?->treatment_days ?? 5;
                        @endphp
                        <option value="{{ $patient->id }}" 
                                data-clinic-id="{{ $resClinicId }}"
                                data-category-id="{{ $resCatId }}"
                                data-recovery="{{ $resRecovery }}"
                                data-days="{{ $resDays }}"
                                {{ (old('patient_id', $preselectedPatientId ?? '') == $patient->id) ? 'selected' : '' }}>
                            {{ $patient->patient_id }} • {{ $patient->full_name }} ({{ $patient->gender }}, {{ $patient->age }}y) - {{ $patient->mobile }}
                        </option>
                    @endforeach
                </select>

                <!-- AUTO-FILL NOTIFICATION BANNER -->
                <div id="pageAutoFillBadge" class="hidden mt-2 p-2.5 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-between text-xs text-blue-800 font-semibold animate-in fade-in duration-200">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        <span id="pageAutoFillText">Auto-filled Clinic & Treatment Category for patient.</span>
                    </div>
                    <span id="pageAutoRecoveryBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 hidden"></span>
                </div>
            </div>
        </div>

        <!-- STEP 4: TREATMENT SCHEDULE & DAYS-BASED FEE CALCULATION -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">4</span>
                    Schedule, Treatment Days & Auto Fee Calculation
                </h3>
                <span class="text-xs text-emerald-700 font-bold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                    Automated Math: Daily Fee × Days
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Start Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="appointment_date" id="pageDateSelect" 
                           value="{{ old('appointment_date', $preselectedDate ?? date('Y-m-d')) }}" 
                           min="{{ date('Y-m-d') }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Treatment Duration (Days) <span class="text-rose-500">*</span></label>
                    <input type="number" name="treatment_days" id="pageTreatmentDays" 
                           value="{{ old('treatment_days', 5) }}" min="1" max="180" required
                           oninput="calculateAutoFee()"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-blue-700 focus:outline-none focus:border-blue-500 bg-white">
                    <div class="flex gap-1.5 mt-2">
                        <button type="button" onclick="setPresetDays(1)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 transition">1 Day</button>
                        <button type="button" onclick="setPresetDays(5)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 transition">5 Days</button>
                        <button type="button" onclick="setPresetDays(10)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 transition">10 Days</button>
                        <button type="button" onclick="setPresetDays(15)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 transition">15 Days</button>
                        <button type="button" onclick="setPresetDays(30)" class="px-2 py-0.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 transition">30 Days</button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Doctor Fee Per Day (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="daily_fee" id="pageDailyFee" 
                           value="{{ old('daily_fee', $clinics->first()?->consultation_fee ?? '800.00') }}" required
                           oninput="calculateAutoFee()"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 bg-white">
                    <p class="text-[10px] text-slate-400 font-semibold mt-1.5">Auto-loaded from selected clinic/doctor fee</p>
                </div>
            </div>

            <!-- LIVE TOTAL AUTO CALCULATION BANNER -->
            <div class="p-5 bg-gradient-to-r from-blue-50 via-indigo-50/50 to-emerald-50/60 rounded-2xl border border-blue-200/90 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-xs">
                        <i data-lucide="calculator" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block tracking-wider">Automated Package Fee Calculation</span>
                        <div class="text-xs text-slate-800 font-semibold mt-0.5">
                            <span id="formula_text" class="font-bold text-blue-900">₹800.00 daily × 5 Days</span>
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-emerald-700 block tracking-wider">Total Package Amount</span>
                    <div class="text-2xl font-black text-emerald-700 tracking-tight" id="total_fee_display">
                        ₹4,000.00
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 5: CLINICAL NOTES & TYPE -->
        <div class="space-y-4 pt-4 border-t border-slate-100">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold">5</span>
                Session Details & Clinical Complaint
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Appointment Type <span class="text-rose-500">*</span></label>
                    <select name="appointment_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-slate-50/50">
                        <option value="new" {{ old('appointment_type') == 'new' ? 'selected' : '' }}>New Patient Assessment & Treatment</option>
                        <option value="follow_up" {{ old('appointment_type') == 'follow_up' ? 'selected' : '' }}>Ongoing Therapy Session</option>
                        <option value="revisit" {{ old('appointment_type') == 'revisit' ? 'selected' : '' }}>Periodic Progress Review</option>
                        <option value="emergency" {{ old('appointment_type') == 'emergency' ? 'selected' : '' }}>Acute Pain Emergency</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Chief Complaint / Condition</label>
                    <input type="text" name="reason" placeholder="e.g. Cervical radiculopathy, low back stiffness" value="{{ old('reason') }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-slate-50/50">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Rehabilitation Goals / Therapy Plan (Optional)</label>
                <textarea name="notes" rows="2" placeholder="Specific modalities planned (e.g. IFT, Ultrasound, Manual Traction, Exercise Therapy)..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-blue-500 bg-slate-50/50">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
            <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-semibold hover:bg-slate-50 transition">
                Cancel
            </a>
            <button type="submit" class="px-7 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-sm transition flex items-center gap-2">
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
        calculateAutoFee();
        if (document.getElementById('pagePatientSelect')?.value) {
            handlePagePatientChange();
        }
    });

    function handlePagePatientChange() {
        const pSelect = document.getElementById('pagePatientSelect');
        if (!pSelect) return;
        const opt = pSelect.options[pSelect.selectedIndex];
        const badgeEl = document.getElementById('pageAutoFillBadge');
        const textEl = document.getElementById('pageAutoFillText');
        const recoveryEl = document.getElementById('pageAutoRecoveryBadge');

        if (!opt || !opt.value) {
            if (badgeEl) badgeEl.classList.add('hidden');
            return;
        }

        const clinicId = opt.dataset.clinicId;
        const categoryId = opt.dataset.categoryId;
        const recovery = parseInt(opt.dataset.recovery) || 0;
        let filled = [];

        if (clinicId) {
            const clinicSelect = document.getElementById('pageClinicSelect');
            if (clinicSelect) {
                clinicSelect.value = clinicId;
                handleClinicChange();
                filled.push('Clinic');
            }
        }

        if (categoryId) {
            const catSelect = document.getElementById('pageCategorySelect');
            if (catSelect) {
                catSelect.value = categoryId;
                filled.push('Category');
            }
        }

        if (badgeEl && textEl) {
            if (filled.length > 0) {
                badgeEl.classList.remove('hidden');
                textEl.innerText = `Auto-filled ${filled.join(' & ')} from patient record.`;
                if (recovery > 0) {
                    recoveryEl.classList.remove('hidden');
                    recoveryEl.innerText = `${recovery}% Recovered`;
                } else {
                    recoveryEl.classList.add('hidden');
                }
            } else {
                badgeEl.classList.add('hidden');
            }
        }
    }

    function handleClinicChange() {
        const clinicSelect = document.getElementById('pageClinicSelect');
        const dailyFeeInput = document.getElementById('pageDailyFee');
        if (clinicSelect && dailyFeeInput) {
            const opt = clinicSelect.options[clinicSelect.selectedIndex];
            if (opt && opt.dataset.fee) {
                dailyFeeInput.value = parseFloat(opt.dataset.fee).toFixed(2);
            }
        }
        calculateAutoFee();
    }

    function setPresetDays(days) {
        document.getElementById('pageTreatmentDays').value = days;
        calculateAutoFee();
    }

    function calculateAutoFee() {
        const days = parseFloat(document.getElementById('pageTreatmentDays').value) || 1;
        const dailyFee = parseFloat(document.getElementById('pageDailyFee').value) || 0;
        const total = days * dailyFee;

        document.getElementById('formula_text').innerText = `₹${dailyFee.toLocaleString('en-IN', {minimumFractionDigits: 2})} daily × ${days} Days`;
        document.getElementById('total_fee_display').innerText = `₹${total.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }
</script>
@endpush
