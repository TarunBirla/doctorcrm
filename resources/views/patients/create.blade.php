@extends('layouts.app')

@section('title', 'Register New Patient')
@section('breadcrumb', 'Patients / Register')
@section('page_title', 'New Patient Intake & Registration')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card-custom p-8 bg-white space-y-6">
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-base">Patient Registration Form</h3>
                <p class="text-xs text-slate-400">Complete demographic and clinical baseline intake for new outpatient record</p>
            </div>
            <div class="px-3 py-1 bg-blue-50 text-blue-700 font-mono font-bold text-xs rounded-xl border border-blue-200">
                Assigned ID: {{ $nextId }}
            </div>
        </div>

        <form action="{{ route('patients.store') }}" method="POST" id="patientForm" class="space-y-6 text-xs">
            @csrf

            <!-- SECTION 1: PERSONAL INFORMATION -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Personal Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" required placeholder="e.g. Rajesh" value="{{ old('first_name') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" required placeholder="e.g. Sharma" value="{{ old('last_name') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Age (Years) *</label>
                        <input type="number" name="age" required min="0" max="120" placeholder="e.g. 45" value="{{ old('age') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-bold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Date of Birth</label>
                        <input type="date" name="dob" value="{{ old('dob') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Blood Group</label>
                        <select name="blood_group" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                            <option value="">Unknown</option>
                            @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                                <option value="{{ $bg }}" {{ old('blood_group') === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Marital Status</label>
                        <select name="marital_status" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                            <option value="Single" {{ old('marital_status') === 'Single' ? 'selected' : '' }}>Single</option>
                            <option value="Married" {{ old('marital_status', 'Married') === 'Married' ? 'selected' : '' }}>Married</option>
                            <option value="Divorced" {{ old('marital_status') === 'Divorced' ? 'selected' : '' }}>Divorced</option>
                            <option value="Widowed" {{ old('marital_status') === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Occupation</label>
                        <input type="text" name="occupation" placeholder="e.g. Executive, Teacher..." value="{{ old('occupation') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>
            </div>

            <!-- SECTION 2: CONTACT INFORMATION -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Contact & Address</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Primary Mobile *</label>
                        <input type="text" name="mobile" required placeholder="10-digit number" value="{{ old('mobile') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Alternate Phone</label>
                        <input type="text" name="alt_mobile" placeholder="Optional phone" value="{{ old('alt_mobile') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Email Address</label>
                        <input type="email" name="email" placeholder="patient@example.com" value="{{ old('email') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-semibold text-slate-700 mb-1">Address</label>
                        <input type="text" name="address" placeholder="House/Flat, Street..." value="{{ old('address') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">City & State</label>
                        <div class="flex gap-2">
                            <input type="text" name="city" value="{{ old('city', 'Gurugram') }}" placeholder="City" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                            <input type="text" name="state" value="{{ old('state', 'Haryana') }}" placeholder="State" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Emergency Contact Person</label>
                        <input type="text" name="emergency_contact" placeholder="Relation & Name e.g. Sunita (Wife)" value="{{ old('emergency_contact') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Emergency Phone Number</label>
                        <input type="text" name="emergency_phone" placeholder="Emergency phone" value="{{ old('emergency_phone') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: MEDICAL BASELINE HISTORY -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Baseline Medical History</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Known Medical Conditions / Diseases</label>
                        <textarea name="conditions" rows="2" placeholder="e.g. Type 2 Diabetes, Hypertension..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ old('conditions') }}</textarea>
                    </div>
                    <div>
                        <label class="block font-semibold text-rose-700 mb-1">Drug / Food Allergies</label>
                        <textarea name="allergies" rows="2" placeholder="e.g. Penicillin, NSAIDs, Sulfa..." class="w-full px-3 py-2 border border-rose-200 rounded-xl bg-rose-50/50 outline-none">{{ old('allergies') }}</textarea>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Current Active Medications</label>
                        <textarea name="current_medications" rows="2" placeholder="Prescription drugs currently taking..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ old('current_medications') }}</textarea>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Past Surgeries & Family History</label>
                        <textarea name="surgeries" rows="2" placeholder="Major surgical history..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ old('surgeries') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: MISCELLANEOUS & NOTES -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Referral Source</label>
                        <input type="text" name="referral_source" placeholder="e.g. Doctor recommendation, Friend, Google..." value="{{ old('referral_source') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">General Notes / Remarks</label>
                        <input type="text" name="notes" placeholder="General clinical observation..." value="{{ old('notes') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>
            </div>

            <!-- SECTION 5: OPTIONAL IMMEDIATE APPOINTMENT BOOKING -->
            <div class="pt-4 border-t-2 border-indigo-100 bg-indigo-50/30 -mx-8 px-8 py-5 rounded-2xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="book_appointment" name="book_appointment" value="1" 
                               {{ old('book_appointment') ? 'checked' : '' }}
                               onchange="toggleAppointmentBooking(this.checked)"
                               class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                        <label for="book_appointment" class="font-bold text-slate-900 text-sm cursor-pointer select-none">
                            Book First Appointment Immediately (Optional)
                        </label>
                    </div>
                    <span class="text-[11px] text-blue-600 font-semibold bg-blue-100/70 px-2 py-0.5 rounded-full">
                        Instant OPD Token
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-4 pl-7">
                    Check this option to instantly assign a clinic, date, and live available slot. If unchecked, only the patient profile will be saved.
                </p>

                <div id="appointmentSection" class="{{ old('book_appointment') ? '' : 'hidden' }} pl-7 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- 1. Select Clinic -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">1. Select Clinic *</label>
                            <select name="clinic_id" id="intake_clinic_id" onchange="fetchAvailableSlots()" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold outline-none focus:ring-2 focus:ring-blue-500/20">
                                @foreach($clinics as $cl)
                                    <option value="{{ $cl->id }}" data-fee="{{ $cl->consultation_fee }}" {{ old('clinic_id') == $cl->id ? 'selected' : '' }}>
                                        {{ $cl->name }} ({{ $cl->city }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Select Date -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">2. Appointment Date *</label>
                            <input type="date" name="appointment_date" id="intake_appointment_date" 
                                   value="{{ old('appointment_date', date('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d') }}" 
                                   onchange="fetchAvailableSlots()" 
                                   class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>

                        <!-- 3. Consultation Fee -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Consultation Fee (₹)</label>
                            <input type="number" step="0.01" name="consultation_fee" id="intake_consultation_fee" 
                                   value="{{ old('consultation_fee', $clinics->first()?->consultation_fee ?? 800) }}" 
                                   class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-bold outline-none">
                        </div>
                    </div>

                    <!-- 3. Live Slot Selector -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block font-bold text-slate-700">
                                3. Choose Available Time Slot *
                            </label>
                            <span id="slotStatusMsg" class="text-[11px] text-slate-500 font-semibold">Loading slots...</span>
                        </div>

                        <!-- Hidden input storing selected slot time -->
                        <input type="hidden" name="appointment_time" id="intake_appointment_time" value="{{ old('appointment_time') }}">

                        <!-- Slots Grid -->
                        <div id="slotsPillsGrid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2 p-3 bg-white rounded-xl border border-slate-200 min-h-[60px]">
                            <div class="col-span-full py-3 text-center text-slate-400">
                                Select clinic & date to view available time slots
                            </div>
                        </div>
                    </div>

                    <!-- Chief Complaint -->
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Reason for Visit / Chief Complaint</label>
                        <input type="text" name="appointment_reason" placeholder="e.g. Acute lower back pain, first consultation..." value="{{ old('appointment_reason') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white outline-none">
                    </div>
                </div>
            </div>

            <!-- FORM ACTIONS -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('patients.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-semibold hover:bg-slate-50">
                    Cancel
                </a>
                <button type="submit" class="px-7 py-2.5 bg-[#0a2540] hover:bg-slate-800 text-white font-bold rounded-xl shadow-sm transition flex items-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4"></i> Register Patient Profile
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    const loggedDoctorId = {{ $loggedInDoctor ? $loggedInDoctor->id : ($doctors->first()?->id ?? 1) }};

    function toggleAppointmentBooking(checked) {
        const sec = document.getElementById('appointmentSection');
        if (checked) {
            sec.classList.remove('hidden');
            fetchAvailableSlots();
        } else {
            sec.classList.add('hidden');
            document.getElementById('intake_appointment_time').value = '';
        }
    }

    async function fetchAvailableSlots() {
        const clinicSelect = document.getElementById('intake_clinic_id');
        const clinicId = clinicSelect ? clinicSelect.value : '';
        const dateInput = document.getElementById('intake_appointment_date');
        const date = dateInput ? dateInput.value : '';
        const container = document.getElementById('slotsPillsGrid');
        const statusMsg = document.getElementById('slotStatusMsg');
        const selectedTimeInput = document.getElementById('intake_appointment_time');

        // Update fee from selected clinic
        const selectedOption = clinicSelect.options[clinicSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.fee) {
            document.getElementById('intake_consultation_fee').value = selectedOption.dataset.fee;
        }

        if (!clinicId || !date) {
            container.innerHTML = '<div class="col-span-full py-3 text-center text-slate-400">Please select clinic and date</div>';
            return;
        }

        statusMsg.innerText = 'Checking availability...';
        container.innerHTML = '<div class="col-span-full py-3 text-center text-slate-400"><i class="animate-spin inline-block mr-2">⌛</i> Loading slots...</div>';

        try {
            const res = await fetch(`/api/doctor-slots?doctor_id=${loggedDoctorId}&clinic_id=${clinicId}&date=${date}`);
            const data = await res.json();

            if (!data.success || !data.is_available || !data.slots || data.slots.length === 0) {
                statusMsg.innerText = data.message || 'No available slots on this day';
                container.innerHTML = `<div class="col-span-full py-3 text-center text-rose-500 font-semibold">${data.message || 'No slots available for this clinic on selected date'}</div>`;
                return;
            }

            statusMsg.innerText = `${data.available_count} slot(s) available`;
            container.innerHTML = '';

            data.slots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.innerText = slot.label;
                btn.dataset.time = slot.time;

                if (!slot.is_available) {
                    btn.disabled = true;
                    btn.className = 'py-2 px-2.5 rounded-lg border border-slate-200 bg-slate-100 text-slate-400 text-xs font-semibold cursor-not-allowed line-through opacity-75';
                    btn.title = slot.is_booked ? 'Already Booked' : (slot.reason || 'Blocked');
                } else {
                    const isSelected = selectedTimeInput.value === slot.time;
                    btn.className = isSelected 
                        ? 'py-2 px-2.5 rounded-lg border-2 border-blue-600 bg-blue-600 text-white font-bold text-xs shadow-xs transition'
                        : 'py-2 px-2.5 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-bold text-xs transition cursor-pointer';

                    btn.onclick = () => {
                        selectedTimeInput.value = slot.time;
                        // update styles
                        document.querySelectorAll('#slotsPillsGrid button').forEach(b => {
                            if (!b.disabled) {
                                b.className = 'py-2 px-2.5 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 font-bold text-xs transition cursor-pointer';
                            }
                        });
                        btn.className = 'py-2 px-2.5 rounded-lg border-2 border-blue-600 bg-blue-600 text-white font-bold text-xs shadow-xs transition';
                        statusMsg.innerText = `Selected Slot: ${slot.label}`;
                    };
                }

                container.appendChild(btn);
            });

        } catch (e) {
            statusMsg.innerText = 'Failed to load slots';
            container.innerHTML = '<div class="col-span-full py-3 text-center text-rose-500">Error loading clinic slots. Please try again.</div>';
        }
    }

    // Auto-fetch if book appointment was already checked on load (e.g. validation redirect)
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('book_appointment').checked) {
            fetchAvailableSlots();
        }
    });
</script>
@endsection
