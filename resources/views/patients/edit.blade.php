@extends('layouts.app')

@section('title', 'Edit Patient & Extend Therapy - ' . $patient->full_name)
@section('breadcrumb', 'Patients / Edit & Extend / ' . $patient->patient_id)
@section('page_title', 'Edit Patient, Recovery % & Extend Therapy Days')

@section('content')
@php
    $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
    $isStaff = \App\Models\User::isStaffRole($currentRole);

    $activeAppointments = $patient->appointments
        ? $patient->appointments->whereNotIn('status', ['cancelled'])->sortBy(fn($a) => $a->appointment_date->format('Y-m-d') . '_' . str_pad($a->id, 8, '0', STR_PAD_LEFT))->values()
        : collect();

    $totalSessions = $activeAppointments->count();
    $completedSessions = $activeAppointments->where('status', 'completed')->count();
    $pendingSessions = max(0, $totalSessions - $completedSessions);
    $lastApt = $activeAppointments->last();

    $defaultDailyFee = (float) ($lastApt?->daily_fee ?? $lastApt?->consultation_fee ?? $patient->clinic?->consultation_fee ?? 500);
    $currentPackageFee = (float) $activeAppointments->sum('consultation_fee');
    if ($currentPackageFee <= 0 && $totalSessions > 0) {
        $currentPackageFee = $totalSessions * $defaultDailyFee;
    }

    // Calculate default next extension date (next day after last appointment, skipping Sunday)
    $nextStartCarbon = $lastApt && $lastApt->appointment_date
        ? \Carbon\Carbon::parse($lastApt->appointment_date)->addDay()
        : now();
    if ($nextStartCarbon->isSunday()) {
        $nextStartCarbon->addDay();
    }
    $defaultExtensionDate = $nextStartCarbon->format('Y-m-d');

    $currentRecoveryPct = (int) old('recovery_percentage', $patient->recovery_percentage ?? ($lastApt?->recovery_percentage ?? 0));
    $latestRecoveryNotes = old('recovery_notes', $lastApt?->recovery_notes ?? '');
@endphp

<div class="max-w-5xl mx-auto space-y-6">

    <!-- TOP SUMMARY HEADER BAR -->
    <div class="card-custom p-5 sm:p-6 bg-white flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-navy-900 text-white flex items-center justify-center font-extrabold text-sm shrink-0 shadow-sm">
                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900">{{ $patient->full_name }}</h2>
                    <span class="font-mono font-bold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-lg text-xs">
                        {{ $patient->patient_id }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-extrabold {{ $currentRecoveryPct >= 100 ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : ($currentRecoveryPct >= 75 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200') }}">
                        {{ $currentRecoveryPct }}% Recovered
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $patient->clinic?->name ?? 'Main Clinic' }} • {{ $patient->category?->name ?? 'Physiotherapy' }} • Mobile: {{ $patient->mobile }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('patients.show', $patient->id) }}" class="px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 hover:bg-slate-50 transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Back to Patient Profile</span>
            </a>
        </div>
    </div>

    <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="space-y-6 text-xs">
        @csrf
        @method('PUT')

        <!-- ========================================================================= -->
        <!-- SECTION 1: TREATMENT PACKAGE, RECOVERY % & EXTEND DAYS (EDIT & EXTEND)    -->
        <!-- ========================================================================= -->
        <div class="card-custom overflow-hidden border-2 border-indigo-200 bg-white shadow-sm">
            <div class="bg-gradient-to-r from-[#0a2540] via-[#0f2744] to-indigo-950 text-white px-5 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center text-emerald-300 shrink-0">
                        <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-extrabold tracking-tight">Treatment Sessions, Recovery % & Extend Days (Edit & Extend)</h3>
                        <p class="text-[11px] text-slate-300">Update patient recovery % (e.g. 80% or 100% Cured) or add extra treatment days (auto-skips Sunday)</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-xl bg-white/10 border border-white/15 text-xs font-extrabold">
                        Sessions: <strong class="text-emerald-300">{{ $completedSessions }} / {{ $totalSessions }} Completed</strong>
                    </span>
                    @if(!$isStaff)
                        <span class="px-3 py-1 rounded-xl bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-xs font-extrabold">
                            Current Package: ₹{{ number_format($currentPackageFee, 0) }}
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-5 sm:p-6 space-y-6">
                <!-- KPI STRIP OF EXISTING SESSIONS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Booked Sessions</span>
                        <div class="text-lg font-extrabold text-slate-900 mt-0.5">{{ $totalSessions }} Days</div>
                        <span class="text-[11px] text-slate-500">{{ $completedSessions }} Done • {{ $pendingSessions }} Scheduled</span>
                    </div>

                    @if(!$isStaff)
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Per-Day Doctor Fee</span>
                        <div class="text-lg font-extrabold text-blue-700 mt-0.5">₹{{ number_format($defaultDailyFee, 0) }} <span class="text-xs font-semibold text-slate-400">/ day</span></div>
                        <span class="text-[11px] text-slate-500">Current Total: ₹{{ number_format($currentPackageFee, 0) }}</span>
                    </div>
                    @endif

                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Last Session Date</span>
                        <div class="text-lg font-extrabold text-slate-900 mt-0.5">
                            {{ $lastApt && $lastApt->appointment_date ? $lastApt->appointment_date->format('d M Y') : 'No Sessions' }}
                        </div>
                        <span class="text-[11px] text-slate-500">
                            {{ $lastApt ? ($lastApt->appointment_time . ' (' . ucfirst($lastApt->status) . ')') : 'Book below' }}
                        </span>
                    </div>

                    <div class="p-3.5 rounded-xl {{ $currentRecoveryPct >= 100 ? 'bg-emerald-50 border-emerald-200' : 'bg-indigo-50/70 border-indigo-200' }} border">
                        <span class="text-[10px] font-bold uppercase tracking-wider {{ $currentRecoveryPct >= 100 ? 'text-emerald-700' : 'text-indigo-700' }} block">Current Recovery Status</span>
                        <div class="text-lg font-extrabold {{ $currentRecoveryPct >= 100 ? 'text-emerald-800' : 'text-indigo-900' }} mt-0.5" id="topRecoveryBadgeText">
                            {{ $currentRecoveryPct }}% Recovery
                        </div>
                        <span class="text-[11px] {{ $currentRecoveryPct >= 100 ? 'text-emerald-700' : 'text-indigo-600' }} font-bold">
                            {{ $currentRecoveryPct >= 100 ? '🌟 Fully Recovered' : ($currentRecoveryPct >= 75 ? 'High Improvement' : 'Under Active Therapy') }}
                        </span>
                    </div>
                </div>

                @if($pendingSessions > 0)
                <div class="p-3.5 rounded-xl bg-amber-50/80 border border-amber-200 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <i data-lucide="check-square" class="w-4 h-4 text-amber-700 shrink-0"></i>
                        <span class="text-amber-900 font-bold text-xs">
                            {{ $pendingSessions }} session(s) are currently marked as Scheduled/Waiting. Have those {{ $totalSessions }} days already been completed?
                        </span>
                    </div>
                    <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-amber-300 text-amber-900 font-extrabold text-xs cursor-pointer hover:bg-amber-100/50 transition">
                        <input type="checkbox" name="mark_previous_completed" value="1" class="w-4 h-4 accent-emerald-600 rounded">
                        <span>Yes, Mark Previous {{ $totalSessions }} Sessions as Completed ✔</span>
                    </label>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- LEFT BOX: STEP 1 - PATIENT RECOVERY % (80% / 100% SAHI HUA) -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-600 block">Step 1 • Clinical Evaluation</span>
                                <h4 class="font-extrabold text-slate-900 text-sm mt-0.5">Patient Recovery % (मरीज कितना % सही हुआ?)</h4>
                            </div>
                            <div class="flex items-center gap-1 bg-white px-2.5 py-1.5 rounded-xl border border-slate-300 shadow-2xs">
                                <input type="number" id="editRecoveryInput" name="recovery_percentage" min="0" max="100" value="{{ $currentRecoveryPct }}"
                                       oninput="syncEditRecovery(this.value)"
                                       class="w-14 text-right font-black text-sm text-indigo-700 outline-none bg-transparent">
                                <span class="font-extrabold text-slate-700 text-xs">%</span>
                            </div>
                        </div>

                        <input type="range" id="editRecoverySlider" min="0" max="100" step="5" value="{{ $currentRecoveryPct }}"
                               oninput="syncEditRecovery(this.value)"
                               class="w-full h-2.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">

                        <!-- QUICK RECOVERY PRESET PILLS (INCLUDING 80% & 100%) -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase mr-1">Quick Set:</span>
                            <button type="button" onclick="syncEditRecovery(25)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 font-bold text-slate-700 transition">25%</button>
                            <button type="button" onclick="syncEditRecovery(50)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 font-bold text-slate-700 transition">50%</button>
                            <button type="button" onclick="syncEditRecovery(70)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 font-bold text-slate-700 transition">70%</button>
                            <button type="button" onclick="syncEditRecovery(80)" class="px-3 py-1 rounded-lg bg-indigo-50 border border-indigo-300 hover:bg-indigo-100 font-extrabold text-indigo-800 transition">80% Recovery</button>
                            <button type="button" onclick="syncEditRecovery(90)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-emerald-400 font-bold text-slate-700 transition">90%</button>
                            <button type="button" onclick="syncEditRecovery(100)" class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold transition shadow-2xs">🌟 100% Recovery</button>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Doctor's Recovery Note / Patient Feedback</label>
                            <textarea name="recovery_notes" rows="2" placeholder="e.g. After 5 days patient is 80% relieved, extending 2 more days / Or: Patient 100% recovered after completing all sessions..."
                                      class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white outline-none focus:border-blue-500 font-semibold text-slate-800">{{ $latestRecoveryNotes }}</textarea>
                        </div>
                    </div>

                    <!-- RIGHT BOX: STEP 2 - EXTEND TREATMENT DAYS (+2 DAYS -> CREATES 2 NEW APPOINTMENTS SKIPPING SUNDAY) -->
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/60 space-y-4">
                        <div class="flex items-center justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block">Step 2 • Edit & Extend Sessions</span>
                                <h4 class="font-extrabold text-slate-900 text-sm mt-0.5">Add Extra Appointment Days (+Days)</h4>
                            </div>
                            <div class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-slate-300 shadow-2xs">
                                <span class="font-extrabold text-blue-600 text-xs">+</span>
                                <input type="number" id="editAdditionalDaysInput" name="additional_days" min="0" max="180" value="{{ old('additional_days', 0) }}"
                                       oninput="recalcEditPackageMath()"
                                       class="w-14 text-center font-black text-sm text-blue-700 outline-none bg-transparent">
                                <span class="font-extrabold text-slate-700 text-xs">Days</span>
                            </div>
                        </div>

                        <!-- QUICK +DAYS PRESET BUTTONS -->
                        <div class="flex flex-wrap items-center gap-1.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase mr-1">Quick Extend:</span>
                            <button type="button" onclick="setEditExtraDays(0)" class="px-2.5 py-1 rounded-lg bg-slate-200/70 text-slate-700 font-bold hover:bg-slate-300 transition">0 (No Extend)</button>
                            <button type="button" onclick="setEditExtraDays(1)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 font-bold text-slate-700 transition">+1 Day</button>
                            <button type="button" onclick="setEditExtraDays(2)" class="px-3 py-1 rounded-lg bg-blue-50 border border-blue-300 hover:bg-blue-100 font-extrabold text-blue-800 transition">+2 Days</button>
                            <button type="button" onclick="setEditExtraDays(3)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 font-bold text-slate-700 transition">+3 Days</button>
                            <button type="button" onclick="setEditExtraDays(5)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 font-bold text-slate-700 transition">+5 Days</button>
                            <button type="button" onclick="setEditExtraDays(7)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 font-bold text-slate-700 transition">+7 Days</button>
                            <button type="button" onclick="setEditExtraDays(10)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:border-blue-400 font-bold text-slate-700 transition">+10 Days</button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @if(!$isStaff)
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Per-Day Fee (₹ / Day)</label>
                                <input type="number" step="0.01" id="editDailyFeeInput" name="daily_fee" value="{{ old('daily_fee', $defaultDailyFee) }}" min="0"
                                       oninput="recalcEditPackageMath()"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-extrabold text-slate-900 outline-none focus:border-blue-500">
                            </div>
                            @else
                                <input type="hidden" id="editDailyFeeInput" name="daily_fee" value="{{ $defaultDailyFee }}">
                            @endif

                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Start Date for Extended Days</label>
                                <input type="date" name="extension_start_date" value="{{ old('extension_start_date', $defaultExtensionDate) }}"
                                       class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none focus:border-blue-500">
                            </div>
                        </div>

                        <!-- LIVE EXTENSION & FEE SUMMARY BOX -->
                        <div class="p-3.5 rounded-xl bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-indigo-900" id="editTotalDaysFormula">
                                    {{ $totalSessions }} Existing Days + 0 Extra Days = {{ $totalSessions }} Total Days
                                </span>
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded bg-white text-indigo-700 border border-indigo-200">
                                    Sunday Auto-Skipped
                                </span>
                            </div>
                            @if(!$isStaff)
                            <div class="flex items-center justify-between pt-1 border-t border-indigo-200/60">
                                <span class="text-[11px] font-semibold text-slate-600" id="editFeeBreakdownText">
                                    Existing: ₹{{ number_format($currentPackageFee, 0) }} + New: ₹0
                                </span>
                                <span class="text-sm font-black text-emerald-700" id="editGrandTotalFeeText">
                                    Total: ₹{{ number_format($currentPackageFee, 2) }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- MINI SCHEDULE PREVIEW OF EXISTING SESSIONS -->
                @if($activeAppointments->isNotEmpty())
                <div class="pt-2 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-2.5">
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400">
                            Current Appointment Sessions Schedule ({{ $completedSessions }}/{{ $totalSessions }} Completed)
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach($activeAppointments as $idx => $sApt)
                            @php
                                $sNum = $idx + 1;
                                $isDone = $sApt->status === 'completed';
                            @endphp
                            <div class="px-3 py-2 rounded-xl border text-xs flex items-center gap-2 {{ $isDone ? 'bg-emerald-50/70 border-emerald-200 text-emerald-900' : 'bg-white border-slate-200 text-slate-700' }}">
                                <span class="w-5 h-5 rounded-full text-[10px] font-extrabold flex items-center justify-center {{ $isDone ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $sNum }}
                                </span>
                                <div>
                                    <div class="font-extrabold leading-tight">Session {{ $sNum }} • {{ $sApt->appointment_date->format('d M (D)') }}</div>
                                    <div class="text-[10px] {{ $isDone ? 'text-emerald-700 font-bold' : 'text-slate-400 font-semibold' }}">
                                        {{ ucfirst($sApt->status) }} @if(!$isStaff) • ₹{{ number_format($sApt->consultation_fee, 0) }} @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 2: PATIENT DEMOGRAPHICS & CLINICAL DETAILS                        -->
        <!-- ========================================================================= -->
        <div class="card-custom p-6 sm:p-8 bg-white space-y-6">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base">Patient Demographic & Clinic Details</h3>
                    <p class="text-xs text-slate-400">Update clinic branch, therapy category, and contact information</p>
                </div>
            </div>

            <!-- CLINICAL PRACTICE & THERAPY ALLOCATION -->
            <div class="p-4 bg-blue-50/50 rounded-2xl border border-blue-100 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Practice Clinic</label>
                        <select name="clinic_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none">
                            <option value="">-- Select Clinic --</option>
                            @foreach($clinics as $cl)
                                <option value="{{ $cl->id }}" {{ old('clinic_id', $patient->clinic_id) == $cl->id ? 'selected' : '' }}>
                                    {{ $cl->name }} ({{ $cl->city ?? 'Clinic' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Treatment Category</label>
                        <select name="category_id" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $patient->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Chief Complaint & Symptoms</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-white font-semibold text-slate-800 outline-none" placeholder="Primary complaint / symptoms...">{{ old('description', $patient->description) }}</textarea>
                </div>
            </div>

            <!-- PERSONAL DETAILS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Gender *</label>
                    <select name="gender" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        <option value="Male" {{ $patient->gender === 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $patient->gender === 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $patient->gender === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Age *</label>
                    <input type="number" name="age" value="{{ old('age', $patient->age) }}" required min="0" max="120" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-bold">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Date of Birth</label>
                    <input type="date" name="dob" value="{{ old('dob', $patient->dob ? $patient->dob->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Blood Group</label>
                    <select name="blood_group" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        <option value="">Unknown</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $bg)
                            <option value="{{ $bg }}" {{ $patient->blood_group === $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- CONTACT -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-100">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Primary Mobile *</label>
                    <input type="text" name="mobile" value="{{ old('mobile', $patient->mobile) }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none font-semibold">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Alternate Phone</label>
                    <input type="text" name="alt_mobile" value="{{ old('alt_mobile', $patient->alt_mobile) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $patient->email) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-semibold text-slate-700 mb-1">Address</label>
                    <input type="text" name="address" value="{{ old('address', $patient->address) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">City & State</label>
                    <div class="flex gap-2">
                        <input type="text" name="city" value="{{ old('city', $patient->city) }}" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        <input type="text" name="state" value="{{ old('state', $patient->state) }}" class="w-1/2 px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Emergency Contact</label>
                    <input type="text" name="emergency_contact" value="{{ old('emergency_contact', $patient->emergency_contact) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Emergency Phone</label>
                    <input type="text" name="emergency_phone" value="{{ old('emergency_phone', $patient->emergency_phone) }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">General Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">{{ old('notes', $patient->notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('patients.show', $patient->id) }}" class="px-5 py-2.5 border border-slate-200 rounded-xl font-semibold hover:bg-slate-50">Cancel</a>
                <button type="submit" class="px-7 py-2.5 bg-navy-900 text-white font-extrabold rounded-xl hover:bg-navy-800 transition shadow-sm flex items-center gap-2 cursor-pointer">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
                    <span>Save Patient, Recovery % & Extended Sessions</span>
                </button>
            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
    const existingSessionsCount = {{ (int) $totalSessions }};
    const existingPackageFee = {{ (float) $currentPackageFee }};

    function syncEditRecovery(val) {
        let num = parseInt(val) || 0;
        if (num < 0) num = 0;
        if (num > 100) num = 100;
        const inputEl = document.getElementById('editRecoveryInput');
        const sliderEl = document.getElementById('editRecoverySlider');
        const badgeEl = document.getElementById('topRecoveryBadgeText');
        if (inputEl) inputEl.value = num;
        if (sliderEl) sliderEl.value = num;
        if (badgeEl) badgeEl.innerText = num + '% Sahi Hua';
        if (num === 100) {
            setEditExtraDays(0);
        }
    }

    function setEditExtraDays(days) {
        const addEl = document.getElementById('editAdditionalDaysInput');
        if (addEl) {
            addEl.value = days;
            recalcEditPackageMath();
        }
    }

    function recalcEditPackageMath() {
        const addDays = parseInt(document.getElementById('editAdditionalDaysInput')?.value) || 0;
        const dailyFee = parseFloat(document.getElementById('editDailyFeeInput')?.value) || 0;
        const newTotalDays = existingSessionsCount + addDays;
        const addedFee = addDays * dailyFee;
        const grandTotalFee = existingPackageFee + addedFee;

        const formulaEl = document.getElementById('editTotalDaysFormula');
        if (formulaEl) {
            formulaEl.innerText = `${existingSessionsCount} Existing Days + ${addDays} Extra Days = ${newTotalDays} Total Days`;
        }

        const feeBreakdownEl = document.getElementById('editFeeBreakdownText');
        if (feeBreakdownEl) {
            feeBreakdownEl.innerText = `Existing: ₹${existingPackageFee.toLocaleString('en-IN')} + New (${addDays}d): ₹${addedFee.toLocaleString('en-IN')}`;
        }

        const grandTotalEl = document.getElementById('editGrandTotalFeeText');
        if (grandTotalEl) {
            grandTotalEl.innerText = `Total: ₹${grandTotalFee.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        }
    }
</script>
@endpush
@endsection
