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
                <h3 class="font-bold text-slate-900 text-base">Physiotherapy Patient Registration</h3>
                <p class="text-xs text-slate-400">Complete demographic intake and therapy classification</p>
            </div>
            <div class="px-3 py-1 bg-blue-50 text-blue-700 font-mono font-bold text-xs rounded-xl border border-blue-200">
                Assigned ID: {{ $nextId }}
            </div>
        </div>

        <form action="{{ route('patients.store') }}" method="POST" id="patientForm" class="space-y-6 text-xs">
            @csrf

            <!-- SECTION 1: CLINICAL PRACTICE & PHYSIOTHERAPY INTAKE -->
            <div class="p-5 bg-blue-50/40 rounded-2xl border border-blue-100/80 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                    <h4 class="font-bold text-blue-900 text-xs uppercase tracking-wider">Clinical Practice & Therapy Allocation</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Select Practice Clinic <span class="text-rose-500">*</span></label>
                        <select name="clinic_id" id="clinic_select" required onchange="updateClinicFee()" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-white font-semibold outline-none focus:border-blue-500 transition text-slate-800">
                            <option value="">Select Clinic</option>
                            @foreach($clinics as $cl)
                                <option value="{{ $cl->id }}" data-fee="{{ $cl->consultation_fee }}" {{ old('clinic_id') == $cl->id ? 'selected' : '' }}>
                                    {{ $cl->name }} ({{ $cl->city ?? 'Clinic' }}) - Fee: ₹{{ number_format($cl->consultation_fee, 0) }}/day
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Treatment Category <span class="text-rose-500">*</span></label>
                        <select name="category_id" id="category_select" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-white font-semibold outline-none focus:border-blue-500 transition text-slate-800">
                            <option value="">Select Treatment Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Chief Complaint & Clinical Description <span class="text-rose-500">*</span></label>
                    <textarea name="description" rows="3" required placeholder="Describe primary symptoms, pain site, onset, severity, diagnosis, or rehabilitation goals..."
                              class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-white font-semibold outline-none focus:border-blue-500 transition text-slate-800 leading-relaxed">{{ old('description') }}</textarea>
                    <p class="text-[11px] text-slate-400 font-semibold mt-1">E.g., Chronic neck pain radiating to right shoulder since 3 weeks; post-operative knee stiffness; lower back spasm.</p>
                </div>
            </div>

            <!-- SECTION 2: PERSONAL INFORMATION -->
            <div class="space-y-3">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Personal Information</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">First Name *</label>
                        <input type="text" name="first_name" required placeholder="e.g. Rajesh" value="{{ old('first_name') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Last Name *</label>
                        <input type="text" name="last_name" required placeholder="e.g. Sharma" value="{{ old('last_name') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Gender *</label>
                        <select name="gender" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
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
                        <input type="text" name="occupation" placeholder="e.g. IT Professional, Homemaker..." value="{{ old('occupation') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: CONTACT INFORMATION -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider text-slate-400">Contact & Address</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Primary Mobile *</label>
                        <input type="text" name="mobile" required placeholder="10-digit number" value="{{ old('mobile') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-bold">
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
                            <input type="text" name="city" value="{{ old('city', $clinics->first()?->city ?? 'Indore') }}" placeholder="City" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                            <input type="text" name="state" value="{{ old('state', $clinics->first()?->state ?? 'Madhya Pradesh') }}" placeholder="State" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
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

            <!-- SECTION 4: MISCELLANEOUS & NOTES -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Referral Source</label>
                        <input type="text" name="referral_source" placeholder="e.g. Doctor recommendation, Friend, Google..." value="{{ old('referral_source') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Internal Notes / Remarks</label>
                        <input type="text" name="notes" placeholder="Internal clinical note..." value="{{ old('notes') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>
            </div>

            <!-- SECTION 5: OPTIONAL PACKAGE APPOINTMENT BOOKING (DAYS-BASED, NO HOURLY SLOTS) -->
            <div class="pt-4 border-t-2 border-indigo-100 bg-indigo-50/30 -mx-8 px-8 py-5 rounded-2xl">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="book_appointment" name="book_appointment" value="1" 
                               {{ old('book_appointment') ? 'checked' : '' }}
                               onchange="toggleAppointmentBooking(this.checked)"
                               class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                        <label for="book_appointment" class="font-bold text-slate-900 text-sm cursor-pointer select-none">
                            Book First Treatment Session / Package Immediately (Optional)
                        </label>
                    </div>
                    <span class="text-[11px] text-blue-700 font-bold bg-blue-100 px-2.5 py-0.5 rounded-full">
                        Days-Based Auto Billing
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-4 pl-7">
                    Select the therapy start date and treatment days package. The total fee will be automatically calculated (Daily Fee × Days) without requiring hourly time slots.
                </p>

                <div id="appointmentSection" class="{{ old('book_appointment') ? '' : 'hidden' }} pl-7 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Start Date -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Start Date *</label>
                            <input type="date" name="appointment_date" id="intake_appointment_date" 
                                   value="{{ old('appointment_date', date('Y-m-d')) }}" 
                                   min="{{ date('Y-m-d') }}" 
                                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-white font-semibold outline-none focus:ring-2 focus:ring-blue-500/20">
                        </div>

                        <!-- Treatment Days Package -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Treatment Duration (Days) *</label>
                            <input type="number" name="treatment_days" id="intake_treatment_days" 
                                   value="{{ old('treatment_days', 5) }}" min="1" max="180" 
                                   oninput="calculateTotalFee()"
                                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-white font-bold outline-none focus:ring-2 focus:ring-blue-500/20 text-blue-700 text-sm">
                            <div class="flex gap-1 mt-1.5">
                                <button type="button" onclick="setDays(1)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600">1 Day</button>
                                <button type="button" onclick="setDays(5)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600">5 Days</button>
                                <button type="button" onclick="setDays(10)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600">10 Days</button>
                                <button type="button" onclick="setDays(15)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600">15 Days</button>
                            </div>
                        </div>

                        <!-- Per-day Fee -->
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Doctor / Clinic Fee Per Day (₹)</label>
                            <input type="number" step="0.01" name="consultation_fee" id="intake_consultation_fee" 
                                   value="{{ old('consultation_fee', $clinics->first()?->consultation_fee ?? 800) }}" 
                                   oninput="calculateTotalFee()"
                                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-white font-bold outline-none focus:ring-2 focus:ring-blue-500/20 text-slate-800 text-sm">
                        </div>
                    </div>

                    <!-- LIVE AUTO FEE CALCULATION BANNER -->
                    <div class="p-4 bg-white rounded-xl border border-blue-200 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                                <i data-lucide="calculator" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Automated Package Fee Calculation</span>
                                <div class="text-xs text-slate-700 font-semibold mt-0.5">
                                    <span id="preview_formula" class="font-bold text-slate-900">₹800 × 5 Days</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-emerald-600 block tracking-wider">Total Package Fee</span>
                            <div class="text-xl font-extrabold text-emerald-700 tracking-tight" id="preview_total_fee">
                                ₹4,000.00
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM ACTIONS -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('patients.index') }}" class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-xl font-semibold hover:bg-slate-50 transition">
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
    function toggleAppointmentBooking(checked) {
        const sec = document.getElementById('appointmentSection');
        if (checked) {
            sec.classList.remove('hidden');
            updateClinicFee();
            calculateTotalFee();
        } else {
            sec.classList.add('hidden');
        }
    }

    function setDays(days) {
        document.getElementById('intake_treatment_days').value = days;
        calculateTotalFee();
    }

    function updateClinicFee() {
        const clinicSelect = document.getElementById('clinic_select');
        if (!clinicSelect) return;
        const opt = clinicSelect.options[clinicSelect.selectedIndex];
        if (opt && opt.dataset.fee) {
            document.getElementById('intake_consultation_fee').value = opt.dataset.fee;
        }
        calculateTotalFee();
    }

    function calculateTotalFee() {
        const days = parseFloat(document.getElementById('intake_treatment_days').value) || 1;
        const dailyFee = parseFloat(document.getElementById('intake_consultation_fee').value) || 0;
        const total = days * dailyFee;

        document.getElementById('preview_formula').innerText = `₹${dailyFee.toLocaleString('en-IN')} daily × ${days} days`;
        document.getElementById('preview_total_fee').innerText = `₹${total.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateClinicFee();
        calculateTotalFee();
    });
</script>
@endsection
