@php
    $modalRole = session('current_role', auth()->user()->role ?? 'super_admin');
    $modalLoggedInDoctor = null;
    if ($modalRole === 'doctor' && auth()->check()) {
        $modalLoggedInDoctor = \App\Models\Doctor::where('user_id', auth()->id())->first();
        $modalPatients = $modalLoggedInDoctor ? \App\Models\Patient::where('doctor_id', $modalLoggedInDoctor->id)->orderBy('first_name')->get() : collect();
        $modalClinics = $modalLoggedInDoctor ? $modalLoggedInDoctor->clinics()->where('is_active', true)->get() : collect();
        if ($modalClinics->isEmpty() && $modalLoggedInDoctor) {
            $modalClinics = \App\Models\Clinic::where('doctor_id', $modalLoggedInDoctor->id)->where('is_active', true)->get();
        }
        if ($modalClinics->isEmpty()) {
            $modalClinics = \App\Models\Clinic::where('is_active', true)->get();
        }
        $modalDoctors = collect($modalLoggedInDoctor ? [$modalLoggedInDoctor] : []);
    } else {
        $modalPatients = \App\Models\Patient::orderBy('first_name')->get();
        $modalDoctors = \App\Models\Doctor::active()->get();
        $modalClinics = \App\Models\Clinic::where('is_active', true)->get();
    }
    $nextPatientId = \App\Models\Patient::generatePatientId();
@endphp

<!-- 1. GLOBAL SEARCH MODAL -->
<div id="globalSearchModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-start justify-center pt-20 p-4">
    <div class="bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        <div class="p-4 border-b border-slate-100 flex items-center gap-3">
            <i data-lucide="search" class="w-5 h-5 text-slate-400"></i>
            <input type="text" id="globalSearchInput" oninput="handleLiveSearch(this.value)" 
                   placeholder="Search patient by name, ID (PAT-000001), phone, appointment, or invoice..." 
                   class="w-full text-sm outline-none text-slate-800 placeholder:text-slate-400 bg-transparent">
            <button onclick="closeGlobalSearch()" class="text-slate-400 hover:text-slate-600 text-xs px-2 py-1 rounded-md bg-slate-100">
                ESC
            </button>
        </div>
        <div id="globalSearchResults" class="p-4 max-h-96 overflow-y-auto text-xs">
            <p class="text-xs text-slate-400 text-center py-6">Type to search records across the clinic...</p>
        </div>
    </div>
</div>

<!-- 2. QUICK APPOINTMENT BOOKING MODAL -->
<div id="quickAppointmentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 md:p-6">
    <div class="bg-white w-full md:w-[60%] max-w-4xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto mx-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center shadow-xs">
                    <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Book New Appointment</h3>
                    <p class="text-[11px] text-slate-500">Select clinic, doctor, date and pick an available live slot</p>
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
                    <div class="flex items-center justify-between mb-1">
                        <label class="block font-semibold text-slate-700">Select Patient *</label>
                        <a href="#" onclick="closeModal('quickAppointmentModal'); openModal('quickPatientModal');" class="text-[11px] text-blue-600 font-semibold hover:underline">
                            + Register new patient
                        </a>
                    </div>
                    <select name="patient_id" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                        <option value="">-- Choose registered patient --</option>
                        @foreach($modalPatients as $p)
                            <option value="{{ $p->id }}">{{ $p->full_name }} ({{ $p->patient_id }} • {{ $p->mobile }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Clinic & Doctor -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Select Clinic *</label>
                    <select name="clinic_id" id="modalClinicSelect" required onchange="fetchModalDoctorSlots()" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                        @foreach($modalClinics as $mc)
                            <option value="{{ $mc->id }}" data-fee="{{ $mc->consultation_fee }}">{{ $mc->name }} ({{ $mc->city }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Doctor *</label>
                    <select name="doctor_id" id="modalDoctorSelect" required onchange="fetchModalDoctorSlots()" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                        @foreach($modalDoctors as $doc)
                            <option value="{{ $doc->id }}" {{ ($modalLoggedInDoctor && $modalLoggedInDoctor->id === $doc->id) ? 'selected' : '' }}>
                                {{ $doc->name }} ({{ $doc->specialization }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Type & Fee -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Appointment Type *</label>
                    <select name="appointment_type" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                        <option value="new">New Consultation</option>
                        <option value="follow_up">Follow-up</option>
                        <option value="revisit">Revisit</option>
                        <option value="emergency">Emergency</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Consultation Fee (₹) *</label>
                    <input type="number" name="consultation_fee" value="800" min="0" required
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold">
                </div>

                <!-- Date & Selected Time -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Appointment Date *</label>
                    <input type="date" name="appointment_date" id="modalDateSelect" value="{{ now()->toDateString() }}" required onchange="fetchModalDoctorSlots()"
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Time Slot * <span id="modalSelectedSlotBadge" class="text-blue-600 font-bold ml-1"></span></label>
                    <input type="time" name="appointment_time" id="modalTimeInput" value="10:00" required
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold">
                </div>

                <!-- Dynamic Live Slots Grid (Full width) -->
                <div class="md:col-span-2">
                    <div id="modalSlotContainer" class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                <i data-lucide="clock" class="w-3.5 h-3.5 text-blue-600"></i> Live Available Clinic Slots:
                            </span>
                            <span id="modalSlotStatusText" class="text-[10px] text-slate-500">Pick doctor & clinic to load</span>
                        </div>
                        <div id="modalSlotsGrid" class="flex flex-wrap gap-1.5 max-h-36 overflow-y-auto">
                            <span class="text-[11px] text-slate-400 italic">Select doctor, clinic & date above to load live availability.</span>
                        </div>
                    </div>
                </div>

                <!-- Reason & Notes -->
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Reason / Symptoms</label>
                    <input type="text" name="reason" placeholder="e.g. Knee pain, regular checkup..."
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Internal Notes (Optional)</label>
                    <input type="text" name="notes" placeholder="Special clinical instructions..."
                           class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('quickAppointmentModal')" class="px-5 py-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-sm">
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
                    <h3 class="font-bold text-slate-900 text-sm">Quick Patient Registration</h3>
                    <p class="text-[11px] text-slate-400">Unique ID: <strong class="text-blue-700 font-mono">{{ $nextPatientId }}</strong></p>
                </div>
            </div>
            <button onclick="closeModal('quickPatientModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('patients.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" required placeholder="e.g. Ramesh"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" required placeholder="e.g. Gupta"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Gender *</label>
                    <select name="gender" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Age (Years) *</label>
                    <input type="number" name="age" required min="0" max="120" placeholder="e.g. 42"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Blood Group</label>
                    <select name="blood_group" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                        <option value="">Unknown</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Mobile Phone *</label>
                    <input type="text" name="mobile" required placeholder="10 digit number"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Emergency Contact & Phone</label>
                    <input type="text" name="emergency_contact" placeholder="Spouse / Parent (98XXXXXXXX)"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Known Allergies</label>
                    <input type="text" name="allergies" placeholder="e.g. Penicillin, Sulfa, Dust"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Existing Conditions</label>
                    <input type="text" name="conditions" placeholder="e.g. Diabetes, Hypertension"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeModal('quickPatientModal')" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold">
                    Save & Open Patient Profile
                </button>
            </div>
        </form>
    </div>
</div>
