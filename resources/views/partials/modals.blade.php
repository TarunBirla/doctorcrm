@php
    $modalRole = session('current_role', auth()->user()->role ?? 'super_admin');
    $modalLoggedInDoctor = null;
    if ($modalRole === 'doctor' && auth()->check()) {
        $modalLoggedInDoctor = \App\Models\Doctor::where('user_id', auth()->id())->first();
        $modalPatients = $modalLoggedInDoctor ? \App\Models\Patient::where('doctor_id', $modalLoggedInDoctor->id)->with(['appointments' => fn($q) => $q->latest()->limit(1)])->orderBy('first_name')->get() : collect();
        $modalClinics = $modalLoggedInDoctor ? $modalLoggedInDoctor->clinics()->where('is_active', true)->get() : collect();
        if ($modalClinics->isEmpty() && $modalLoggedInDoctor) {
            $modalClinics = \App\Models\Clinic::where('doctor_id', $modalLoggedInDoctor->id)->where('is_active', true)->get();
        }
        if ($modalClinics->isEmpty()) {
            $modalClinics = \App\Models\Clinic::where('is_active', true)->get();
        }
        $modalDoctors = collect($modalLoggedInDoctor ? [$modalLoggedInDoctor] : []);
    } else {
        $modalPatients = \App\Models\Patient::with(['appointments' => fn($q) => $q->latest()->limit(1)])->orderBy('first_name')->get();
        $modalDoctors = \App\Models\Doctor::active()->get();
        $modalClinics = \App\Models\Clinic::where('is_active', true)->get();
    }
    $modalCategories = \App\Models\TreatmentCategory::active()->orderBy('name')->get();
    $nextPatientId = \App\Models\Patient::generatePatientId();
@endphp

<!-- 1. GLOBAL SEARCH MODAL -->
<div id="globalSearchModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-start justify-center pt-20 p-4">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-4 border-b border-slate-100 flex items-center gap-3">
            <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
            <input type="text" id="globalSearchInput" oninput="handleLiveSearch(this.value)" 
                   placeholder="Search patient by name, ID (PAT-000001), phone, appointment, or invoice..." 
                   class="w-full text-sm outline-none text-slate-800 placeholder:text-slate-400 bg-transparent font-semibold">
            <button onclick="closeGlobalSearch()" class="text-slate-400 hover:text-slate-600 text-xs px-2 py-1 rounded-md bg-slate-100 font-bold">
                ESC
            </button>
        </div>
        <div id="globalSearchResults" class="p-4 max-h-96 overflow-y-auto text-xs font-semibold">
            <p class="text-xs text-slate-400 text-center py-6">Type to search records across the clinic...</p>
        </div>
    </div>
</div>

<!-- 2. QUICK APPOINTMENT BOOKING MODAL (PHYSIOTHERAPY PACKAGE BASED) -->
<div id="quickAppointmentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 md:p-6">
    <div class="bg-white w-full md:w-[60%] max-w-4xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto mx-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shadow-xs">
                    <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Book Physiotherapy Appointment</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">Clinic & Category-based Session Booking with Auto Fee (Daily Fee × Days)</p>
                </div>
            </div>
            <button onclick="closeModal('quickAppointmentModal')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('appointments.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Patient Selection -->
                <div class="md:col-span-2">
                    <div class="mb-1">
                        <label class="block font-bold text-slate-700">Select Patient *</label>
                    </div>
                    <select name="patient_id" id="modalPatientSelect" onchange="handleModalPatientChange()" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        <option value="">-- Choose registered patient --</option>
                        @foreach($modalPatients as $p)
                            @php
                                $lastAppt = $p->appointments->first();
                                $resolvedClinicId = $p->clinic_id ?? $lastAppt?->clinic_id;
                                $resolvedCatId = $p->category_id ?? $lastAppt?->category_id;
                                $resolvedRecovery = $p->recovery_percentage ?? $lastAppt?->recovery_percentage ?? 0;
                                $resolvedDays = $lastAppt?->treatment_days ?? 5;
                            @endphp
                            <option value="{{ $p->id }}" 
                                    data-clinic-id="{{ $resolvedClinicId }}" 
                                    data-category-id="{{ $resolvedCatId }}"
                                    data-recovery="{{ $resolvedRecovery }}"
                                    data-days="{{ $resolvedDays }}">
                                {{ $p->full_name }} ({{ $p->patient_id }} • {{ $p->mobile }})
                            </option>
                        @endforeach
                    </select>

                    <!-- Auto-Fill Alert Badge -->
                    <div id="patientAutoFillBadge" class="hidden mt-2 p-2 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-between text-xs text-blue-800 font-semibold animate-in fade-in duration-200">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                            <span id="patientAutoFillText">Auto-filled Clinic & Category</span>
                        </div>
                        <span id="patientAutoRecoveryBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 hidden"></span>
                    </div>
                </div>

                <!-- Clinic & Category -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Practice Clinic *</label>
                    <select name="clinic_id" id="modalClinicSelect" required onchange="handleModalClinicChange()" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        <option value="">-- Select Clinic --</option>
                        @foreach($modalClinics as $mc)
                            <option value="{{ $mc->id }}" data-fee="{{ $mc->consultation_fee }}">{{ $mc->name }} ({{ $mc->city ?? 'Clinic' }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Treatment Category *</label>
                    <select name="category_id" id="modalCategorySelect" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        <option value="">-- Choose Therapy Category --</option>
                        @foreach($modalCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Doctor -->
                @if($modalRole === 'super_admin')
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Doctor *</label>
                    <select name="doctor_id" id="modalDoctorSelect" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        @foreach($modalDoctors as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialization }})</option>
                        @endforeach
                    </select>
                </div>
                @else
                <input type="hidden" name="doctor_id" value="{{ $modalLoggedInDoctor ? $modalLoggedInDoctor->id : ($modalDoctors->first()?->id ?? 1) }}">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Consulting Doctor</label>
                    <div class="px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-100 font-bold text-slate-700">
                        {{ $modalLoggedInDoctor ? $modalLoggedInDoctor->name : 'OPD Doctor' }}
                    </div>
                </div>
                @endif

                <!-- Start Date -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Start Date *</label>
                    <input type="date" name="appointment_date" id="modalDateSelect" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}" required
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                </div>

                <!-- Treatment Days -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Treatment Duration (Days) *</label>
                    <input type="number" name="treatment_days" id="modalTreatmentDays" value="5" min="1" max="180" required oninput="calculateModalTotalFee()"
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-bold text-blue-700">
                </div>

                <!-- Daily Fee -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Daily / Per-Session Fee (₹) *</label>
                    <input type="number" step="0.01" name="daily_fee" id="modalDailyFee" value="{{ $modalClinics->first()?->consultation_fee ?? 800 }}" min="0" required oninput="calculateModalTotalFee()"
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-bold text-slate-800">
                </div>

                <!-- LIVE AUTO FEE CALCULATION BANNER -->
                <div class="md:col-span-2 p-4 bg-gradient-to-r from-blue-50 to-indigo-50/60 rounded-xl border border-blue-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Auto Calculated Amount</span>
                        <span id="modalFeeFormula" class="text-xs font-bold text-slate-800">₹800 × 5 Days</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold text-emerald-700 block">Total Package Fee</span>
                        <div id="modalTotalFeeDisplay" class="text-lg font-black text-emerald-700 tracking-tight">₹4,000.00</div>
                    </div>
                </div>

                <!-- Appointment Type & Symptoms -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Appointment Type *</label>
                    <select name="appointment_type" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        <option value="new">New Assessment & Session</option>
                        <option value="follow_up">Ongoing Session</option>
                        <option value="revisit">Periodic Revisit</option>
                        <option value="emergency">Acute Pain Emergency</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Chief Complaint / Condition</label>
                    <input type="text" name="reason" placeholder="e.g. Cervical pain, post-op knee stiffness..."
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                </div>

                <!-- Payment Collection At Booking -->
                <div class="md:col-span-2 p-4 bg-emerald-50/60 rounded-xl border border-emerald-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-800 flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="payment_collected" value="1" id="modalPaymentCollectedCheckbox" onchange="toggleModalPaymentOptions(this)" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs font-black text-emerald-950">Collect Payment Now (Paid Upfront)</span>
                        </label>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-0.5 rounded-full">
                            Marks all package sessions as Paid
                        </span>
                    </div>

                    <div id="modalPaymentFields" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-emerald-200/60">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1 text-[11px]">Payment Method *</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white outline-none font-semibold text-slate-800 text-xs">
                                <option value="Cash">Cash Counter</option>
                                <option value="UPI">UPI / GPay / PhonePe</option>
                                <option value="Card">Card Swipe</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1 text-[11px]">Payment Reference</label>
                            <input type="text" name="payment_notes" placeholder="Receipt / Txn Ref (optional)" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white outline-none font-semibold text-slate-800 text-xs">
                        </div>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('quickAppointmentModal')" class="px-5 py-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-sm">
                    Confirm & Generate Token
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. QUICK PATIENT REGISTRATION MODAL -->
<div id="quickPatientModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Quick Patient Intake</h3>
                    <p class="text-[11px] text-slate-400 font-semibold">Assigned ID: <strong class="text-blue-700 font-mono">{{ $nextPatientId }}</strong></p>
                </div>
            </div>
            <button onclick="closeModal('quickPatientModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('patients.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf

            <!-- Clinic & Category Selection -->
            <div class="grid grid-cols-2 gap-3 p-3 bg-blue-50/50 rounded-xl border border-blue-100">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Select Clinic *</label>
                    <select name="clinic_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none">
                        @foreach($modalClinics as $mc)
                            <option value="{{ $mc->id }}">{{ $mc->name }} ({{ $mc->city ?? 'Clinic' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Treatment Category *</label>
                    <select name="category_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none">
                        <option value="">Select Category</option>
                        @foreach($modalCategories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Name -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" required placeholder="e.g. Ramesh"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" required placeholder="e.g. Gupta"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold">
                </div>
            </div>

            <!-- Demographics -->
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Gender *</label>
                    <select name="gender" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Age (Years) *</label>
                    <input type="number" name="age" required min="0" max="120" placeholder="e.g. 42"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-bold">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mobile Phone *</label>
                    <input type="text" name="mobile" required placeholder="10 digit number"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-bold">
                </div>
            </div>

            <!-- Chief Complaint / Description -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Chief Complaint & Symptoms *</label>
                <textarea name="description" rows="2" required placeholder="Describe pain site, symptoms, duration, diagnosis..."
                          class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('quickPatientModal')" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-sm">
                    Save & Open Patient Profile
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handleModalPatientChange() {
    const pSelect = document.getElementById('modalPatientSelect');
    if (!pSelect) return;
    const opt = pSelect.options[pSelect.selectedIndex];
    const badgeEl = document.getElementById('patientAutoFillBadge');
    const textEl = document.getElementById('patientAutoFillText');
    const recoveryEl = document.getElementById('patientAutoRecoveryBadge');

    if (!opt || !opt.value) {
        if (badgeEl) badgeEl.classList.add('hidden');
        return;
    }

    const clinicId = opt.dataset.clinicId;
    const categoryId = opt.dataset.categoryId;
    const recovery = parseInt(opt.dataset.recovery) || 0;
    let filled = [];

    if (clinicId) {
        const clinicSelect = document.getElementById('modalClinicSelect');
        if (clinicSelect) {
            clinicSelect.value = clinicId;
            handleModalClinicChange();
            filled.push('Clinic');
        }
    }

    if (categoryId) {
        const catSelect = document.getElementById('modalCategorySelect');
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

function handleModalClinicChange() {
    const clinicSelect = document.getElementById('modalClinicSelect');
    const dailyFeeInput = document.getElementById('modalDailyFee');
    if (clinicSelect && dailyFeeInput) {
        const opt = clinicSelect.options[clinicSelect.selectedIndex];
        if (opt && opt.dataset.fee) {
            dailyFeeInput.value = parseFloat(opt.dataset.fee).toFixed(2);
        }
    }
    calculateModalTotalFee();
}

function calculateModalTotalFee() {
    const days = parseFloat(document.getElementById('modalTreatmentDays')?.value) || 1;
    const dailyFee = parseFloat(document.getElementById('modalDailyFee')?.value) || 0;
    const total = days * dailyFee;

    const formulaEl = document.getElementById('modalFeeFormula');
    const displayEl = document.getElementById('modalTotalFeeDisplay');
    if (formulaEl) formulaEl.innerText = `₹${dailyFee.toLocaleString('en-IN')} daily × ${days} Days`;
    if (displayEl) displayEl.innerText = `₹${total.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
}

function toggleModalPaymentOptions(chk) {
    const fields = document.getElementById('modalPaymentFields');
    if (fields) {
        if (chk.checked) {
            fields.classList.remove('hidden');
        } else {
            fields.classList.add('hidden');
        }
    }
}
</script>
