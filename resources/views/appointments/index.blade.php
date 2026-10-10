@extends('layouts.app')

@section('title', 'Appointments & Queue Management')
@section('breadcrumb', 'Appointments')
@section('page_title', 'Appointment Operations & Queue')

@section('content')
<div class="space-y-6">

    <!-- 4 TOP KPI CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Appointments</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $totalAppointments }}</div>
        </div>

        <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Revenue Collected</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($totalRevenue, 2) }}</div>
        </div>

        <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block">Total Dues Pending</span>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">₹{{ number_format($totalDues, 2) }}</div>
        </div>

        <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Completed Consultations</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $completedAppointments }}</div>
        </div>

    </div>

    <!-- FILTER BAR (WITH CLINIC & CATEGORY FILTERS) -->
    <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
        <form action="{{ route('appointments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3 text-xs items-end">
            
            <div class="lg:col-span-2">
                <label class="block font-semibold text-slate-600 mb-1">Search Patient</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Code, name, phone, token..." 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 outline-none focus:bg-white focus:border-blue-500 font-semibold">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Clinic Branch</label>
                <select name="clinic_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                    <option value="">All Clinics</option>
                    @foreach($clinics as $cl)
                        <option value="{{ $cl->id }}" {{ (string) request('clinic_id', $clinicId ?? '') === (string) $cl->id ? 'selected' : '' }}>
                            {{ $cl->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Therapy Category</label>
                <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                    <option value="">All Categories</option>
                    @foreach($categories as $cg)
                        <option value="{{ $cg->id }}" {{ (string) request('category_id', $categoryId ?? '') === (string) $cg->id ? 'selected' : '' }}>
                            {{ $cg->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Appointment Date</label>
                <input type="date" name="date" value="{{ $date }}" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
            </div>

            <div>
                <label class="block font-semibold text-slate-600 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                    <option value="">All Statuses</option>
                    <option value="scheduled" {{ $status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="waiting" {{ $status === 'waiting' ? 'selected' : '' }}>Waiting</option>
                    <option value="in_consultation" {{ $status === 'in_consultation' ? 'selected' : '' }}>In Consultation</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="no_show" {{ $status === 'no_show' ? 'selected' : '' }}>No Show</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-sm text-center">
                    Filter
                </button>
                <a href="{{ route('appointments.index') }}" class="px-3 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-semibold text-center">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- DATA TABLE CONTAINER -->
    <div class="card-custom bg-white border border-slate-100 shadow-xs overflow-hidden">
        
        <!-- HEADER WITH ADD APPOINTMENT CTA -->
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Appointments / Physiotherapy Queue</h3>
                <p class="text-xs text-slate-400">Treatment sessions tracking with recovery percentage, extension days and real-time status</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('calendar.index') }}" class="px-3 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Calendar View</span>
                </a>
                <button onclick="openModal('quickAppointmentModal')" class="flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    <span>Book Physiotherapy</span>
                </button>
            </div>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-3.5 pl-5">Token / No</th>
                        <th class="p-3.5">Date & Clinic</th>
                        <th class="p-3.5">Patient / ID</th>
                        <th class="p-3.5">Category & Chief Complaint</th>
                        <th class="p-3.5 text-center">Duration (Days)</th>
                        <th class="p-3.5 min-w-[170px]">Recovery % (कितना % सही हुआ)</th>
                        <th class="p-3.5">Fee (₹)</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appointments as $apt)
                        @php
                            $totalDays = $apt->treatment_days + ($apt->extended_days ?? 0);
                            $pct = (int) ($apt->recovery_percentage ?? 0);
                            $barColor = $pct >= 75 ? 'bg-emerald-500' : ($pct >= 40 ? 'bg-amber-500' : ($pct > 0 ? 'bg-blue-500' : 'bg-slate-300'));
                            $badgeClass = $pct >= 75 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($pct >= 40 ? 'bg-amber-50 text-amber-800 border-amber-200' : ($pct > 0 ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200'));
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="p-3.5 pl-5">
                                <span class="font-bold text-slate-900 block">Token #{{ $apt->token_number }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $apt->appointment_no }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-semibold text-slate-800 block">{{ $apt->appointment_date->format('d M Y') }}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <i data-lucide="building" class="w-3 h-3 text-slate-400"></i>
                                    <span class="text-[11px] text-slate-600 font-semibold">{{ $apt->clinic?->name ?? 'Main Clinic' }}</span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <a href="{{ route('patients.show', $apt->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-600 block transition">
                                    {{ $apt->patient->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $apt->patient->patient_id }} • {{ $apt->patient->mobile }}</span>
                            </td>
                            <td class="p-3.5 max-w-xs">
                                @if($apt->category)
                                    <span class="inline-block text-[10px] px-2 py-0.5 bg-blue-50 text-blue-700 font-bold rounded-lg border border-blue-200 mb-1">
                                        {{ $apt->category->name }}
                                    </span>
                                @endif
                                <div class="text-slate-600 text-[11px] truncate">
                                    {{ $apt->reason ?? 'Physiotherapy Rehabilitation' }}
                                </div>
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-bold {{ $apt->extended_days > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $totalDays }} Days
                                    </span>
                                    @if($apt->extended_days > 0)
                                        <span class="text-[9px] text-indigo-600 font-bold mt-0.5">({{ $apt->treatment_days }}d + {{ $apt->extended_days }}d ext)</span>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div>
                                    <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                        <span class="px-1.5 py-0.5 rounded border text-[10px] {{ $badgeClass }}">
                                            {{ $pct }}% Sahi Hua
                                        </span>
                                        <span class="text-[9px] text-slate-400 font-semibold">{{ $apt->getRecoveryLevelLabel() }}</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300" style="width: {{ max(4, $pct) }}%"></div>
                                    </div>
                                    @if($apt->recovery_notes)
                                        <p class="text-[10px] text-slate-500 italic mt-1 line-clamp-1 truncate" title="{{ $apt->recovery_notes }}">
                                            "{{ $apt->recovery_notes }}"
                                        </p>
                                    @endif
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div class="font-bold text-slate-900">₹{{ number_format($apt->consultation_fee, 2) }}</div>
                                <span class="text-[10px] text-slate-400 block font-semibold">₹{{ number_format($apt->daily_fee ?? ($apt->consultation_fee / max(1, $totalDays)), 0) }}/day</span>
                                @if($apt->payment_status === 'paid')
                                    <span class="inline-flex items-center gap-0.5 text-[9px] font-bold text-emerald-700 mt-0.5">
                                        <i data-lucide="check" class="w-2.5 h-2.5"></i> Paid
                                    </span>
                                @else
                                    <span class="inline-block text-[9px] font-bold text-rose-600 mt-0.5">
                                        {{ ucfirst($apt->payment_status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if($apt->status === 'in_consultation')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-ping"></span> In Consultation
                                    </span>
                                @elseif($apt->status === 'waiting')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Waiting
                                    </span>
                                @elseif($apt->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Completed
                                    </span>
                                @elseif($apt->status === 'cancelled')
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 capitalize">
                                        {{ $apt->status }}
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- EDIT & EXTEND THERAPY ACTION BUTTON -->
                                    <button type="button" 
                                            onclick='openEditExtendModal(@json($apt), "{{ addslashes($apt->patient->full_name) }}", "{{ addslashes($apt->clinic?->name ?? "Clinic") }}", "{{ addslashes($apt->category?->name ?? "Category") }}")'
                                            class="px-2.5 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white rounded-xl text-[11px] font-bold transition flex items-center gap-1 border border-indigo-200/80 shadow-2xs"
                                            title="Edit Days / Extend Treatment / Update Recovery %">
                                        <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                                        <span>Edit / +Days</span>
                                    </button>

                                    <!-- One-Click Consultation Action -->
                                    @if($apt->status === 'waiting' || $apt->status === 'in_consultation')
                                        <a href="{{ route('prescriptions.create', ['appointment_id' => $apt->id]) }}" 
                                           title="Doctor Consultation & Prescription Room" 
                                           class="px-2 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-[11px] font-bold transition flex items-center gap-1 shadow-2xs">
                                            <i data-lucide="stethoscope" class="w-3 h-3"></i>
                                            <span>Consult</span>
                                        </a>
                                    @elseif($apt->status === 'scheduled' || $apt->status === 'confirmed')
                                        <form action="{{ route('appointments.status', $apt->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="waiting">
                                            <button type="submit" title="Mark Patient Arrived & Waiting" 
                                                    class="px-2 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-xl text-[11px] font-bold transition">
                                                Check In
                                            </button>
                                        </form>
                                    @endif

                                    <!-- View Patient Profile -->
                                    <a href="{{ route('patients.show', $apt->patient_id) }}" title="Patient Profile" 
                                       class="p-1.5 bg-slate-50 text-slate-600 hover:bg-slate-200 rounded-xl transition">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>

                                    <!-- Delete / Cancel -->
                                    @if($apt->status !== 'completed')
                                        <form action="{{ route('appointments.destroy', $apt->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Cancel Appointment" class="p-1.5 bg-slate-50 text-rose-600 hover:bg-rose-100 rounded-xl transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="calendar-x" class="w-6 h-6"></i>
                                </div>
                                <h4 class="font-bold text-slate-700 text-sm">No Appointments Found</h4>
                                <p class="text-xs text-slate-400 mt-1">Try adjusting the clinic, category, or date filters above.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($appointments->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                {{ $appointments->links() }}
            </div>
        @endif

    </div>

</div>

<!-- INTERACTIVE MODAL: EDIT APPOINTMENT, EXTEND SESSIONS & UPDATE RECOVERY % -->
<div id="editExtendAppointmentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 md:p-6">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto mx-auto animate-in fade-in zoom-in-95 duration-200">
        
        <!-- MODAL HEADER -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center shadow-xs font-bold">
                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Edit & Extend Therapy Appointment</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">Extend treatment days (e.g. +3 days) & record recovery progress %</p>
                </div>
            </div>
            <button type="button" onclick="closeEditExtendModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- FORM -->
        <form id="editExtendAppointmentForm" method="POST" class="p-6 space-y-5 text-xs">
            @csrf
            @method('PUT')

            <!-- PATIENT SUMMARY BANNER -->
            <div class="p-4 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase font-bold text-blue-700 tracking-wider block">Patient & Treatment</span>
                    <h4 id="modalExtPatientName" class="text-sm font-extrabold text-slate-900 mt-0.5">Patient Name</h4>
                    <p id="modalExtClinicCatInfo" class="text-[11px] text-slate-600 font-semibold mt-0.5">Clinic • Category</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Current Duration</span>
                    <div id="modalExtCurrentDaysBadge" class="text-sm font-extrabold text-indigo-700">5 Days</div>
                </div>
            </div>

            <!-- SECTION 1: RECOVERY PERCENTAGE TRACKING (KITNA % SAHI HUA) -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block font-bold text-slate-800 text-xs">Patient Recovery % (मरीज कितना % सही हुआ?)</label>
                        <p class="text-[11px] text-slate-500 font-semibold">Slide to record overall recovery / symptom improvement level</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <input type="number" id="modalExtRecoveryInput" name="recovery_percentage" min="0" max="100" value="0"
                               oninput="syncRecoveryFromInput(this.value)"
                               class="w-16 px-2 py-1 text-right text-xs font-black text-indigo-700 bg-white border border-slate-300 rounded-lg outline-none focus:border-blue-500">
                        <span class="font-bold text-slate-700 text-xs">%</span>
                    </div>
                </div>

                <!-- SLIDER -->
                <input type="range" id="modalExtRecoverySlider" min="0" max="100" value="0" step="5"
                       oninput="syncRecoveryFromSlider(this.value)"
                       class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">

                <!-- LIVE LEVEL BADGE -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[10px] text-slate-400 font-bold">0% (Severe/Initial)</span>
                    <span id="modalExtRecoveryLevelText" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700">
                        Initial Assessment (0%)
                    </span>
                    <span class="text-[10px] text-emerald-600 font-bold">100% (Fully Cured)</span>
                </div>
            </div>

            <!-- SECTION 2: EXTEND SESSIONS / EDIT DAYS -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block font-bold text-slate-800 text-xs">Add More Sessions / Extend Appointment Days</label>
                        <p class="text-[11px] text-slate-500 font-semibold">Agar patient pura sahi nahi hua to aage aur din add karein</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-xs font-bold text-slate-600">+</span>
                        <input type="number" id="modalExtAdditionalDaysInput" name="additional_days" min="0" max="180" value="0"
                               oninput="recalcModalExtMath()"
                               class="w-16 px-2 py-1 text-center text-xs font-black text-blue-700 bg-white border border-slate-300 rounded-lg outline-none focus:border-blue-500">
                        <span class="font-bold text-slate-700 text-xs">Days</span>
                    </div>
                </div>

                <!-- PRESET QUICK BUTTONS -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Quick Add:</span>
                    <button type="button" onclick="setModalExtExtraDays(1)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+1 Day</button>
                    <button type="button" onclick="setModalExtExtraDays(2)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+2 Days</button>
                    <button type="button" onclick="setModalExtExtraDays(3)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition active:bg-indigo-100">+3 Days</button>
                    <button type="button" onclick="setModalExtExtraDays(5)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+5 Days</button>
                    <button type="button" onclick="setModalExtExtraDays(7)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+7 Days</button>
                    <button type="button" onclick="setModalExtExtraDays(10)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+10 Days</button>
                    <button type="button" onclick="setModalExtExtraDays(0)" class="px-2 py-1 rounded-lg bg-slate-100 text-slate-500 font-bold text-xs transition">Reset</button>
                </div>

                <!-- TOTAL DURATION SUMMARY & FEE MATH -->
                <div class="mt-3 p-3 rounded-xl bg-indigo-50/70 border border-indigo-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-indigo-800 block">Total Treatment Duration</span>
                        <div id="modalExtTotalDurationFormula" class="text-xs font-bold text-indigo-900 mt-0.5">5 Days + 0 Days = 5 Total Days</div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold text-emerald-800 block">Updated Package Fee</span>
                        <div id="modalExtTotalFeeDisplay" class="text-sm font-black text-emerald-700">₹4,000.00</div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: DAILY FEE & STATUS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Per-Day Session Fee (₹)</label>
                    <input type="number" step="0.01" id="modalExtDailyFee" name="daily_fee" value="800" min="0"
                           oninput="recalcModalExtMath()"
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-bold text-slate-800">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Appointment Status</label>
                    <select id="modalExtStatusSelect" name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="waiting">Waiting</option>
                        <option value="in_consultation">In Consultation</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 4: DOCTOR'S RECOVERY REMARKS / EXTENSION REASON -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Doctor's Recovery Remarks & Reason for Extension</label>
                <textarea id="modalExtRecoveryNotes" name="recovery_notes" rows="2" 
                          placeholder="e.g. Patient 40% relieved after 5 days, continuing 3 more days for cervical decompression and exercise therapy..."
                          class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800"></textarea>
            </div>

            <!-- MODAL ACTIONS -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditExtendModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition shadow-sm flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Save & Update Appointment</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentAppointmentData = null;

function openEditExtendModal(apt, patientName, clinicName, categoryName) {
    currentAppointmentData = apt;
    const form = document.getElementById('editExtendAppointmentForm');
    form.action = `/appointments/${apt.id}`;

    document.getElementById('modalExtPatientName').innerText = patientName || 'Patient';
    document.getElementById('modalExtClinicCatInfo').innerText = `${clinicName || 'Clinic'} • ${categoryName || 'Physiotherapy'}`;
    
    const baseDays = parseInt(apt.treatment_days) || 1;
    const currentExt = parseInt(apt.extended_days) || 0;
    const currentTotal = baseDays + currentExt;
    document.getElementById('modalExtCurrentDaysBadge').innerText = `${currentTotal} Days (${baseDays}d + ${currentExt}d ext)`;

    // Recovery %
    const recoveryPct = parseInt(apt.recovery_percentage) || 0;
    document.getElementById('modalExtRecoveryInput').value = recoveryPct;
    document.getElementById('modalExtRecoverySlider').value = recoveryPct;
    updateRecoveryLevelDisplay(recoveryPct);

    // Days & Fee
    document.getElementById('modalExtAdditionalDaysInput').value = 0;
    document.getElementById('modalExtDailyFee').value = parseFloat(apt.daily_fee || (apt.consultation_fee / Math.max(1, currentTotal)) || 800).toFixed(2);
    document.getElementById('modalExtStatusSelect').value = apt.status || 'scheduled';
    document.getElementById('modalExtRecoveryNotes').value = apt.recovery_notes || '';

    recalcModalExtMath();
    document.getElementById('editExtendAppointmentModal').classList.remove('hidden');
}

function closeEditExtendModal() {
    document.getElementById('editExtendAppointmentModal').classList.add('hidden');
}

function setModalExtExtraDays(days) {
    document.getElementById('modalExtAdditionalDaysInput').value = days;
    recalcModalExtMath();
}

function syncRecoveryFromSlider(val) {
    document.getElementById('modalExtRecoveryInput').value = val;
    updateRecoveryLevelDisplay(parseInt(val));
}

function syncRecoveryFromInput(val) {
    let num = parseInt(val) || 0;
    if (num < 0) num = 0;
    if (num > 100) num = 100;
    document.getElementById('modalExtRecoverySlider').value = num;
    updateRecoveryLevelDisplay(num);
}

function updateRecoveryLevelDisplay(pct) {
    const el = document.getElementById('modalExtRecoveryLevelText');
    if (!el) return;
    if (pct >= 100) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300";
        el.innerText = `🌟 Fully Recovered (100%)`;
    } else if (pct >= 75) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200";
        el.innerText = `🟢 High Recovery (${pct}%)`;
    } else if (pct >= 50) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200";
        el.innerText = `🟡 Significant Improvement (${pct}%)`;
    } else if (pct >= 25) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-800 border border-blue-200";
        el.innerText = `🟠 Moderate Recovery (${pct}%)`;
    } else if (pct > 0) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-800 border border-rose-200";
        el.innerText = `🔴 Mild / Initial Relief (${pct}%)`;
    } else {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700";
        el.innerText = `Assessment Pending (0%)`;
    }
}

function recalcModalExtMath() {
    if (!currentAppointmentData) return;
    const baseDays = parseInt(currentAppointmentData.treatment_days) || 1;
    const currentExt = parseInt(currentAppointmentData.extended_days) || 0;
    const newAddedDays = parseInt(document.getElementById('modalExtAdditionalDaysInput').value) || 0;
    const totalDays = baseDays + currentExt + newAddedDays;

    const dailyFee = parseFloat(document.getElementById('modalExtDailyFee').value) || 0;
    const totalFee = totalDays * dailyFee;

    const formulaEl = document.getElementById('modalExtTotalDurationFormula');
    const displayEl = document.getElementById('modalExtTotalFeeDisplay');

    if (formulaEl) {
        formulaEl.innerText = `${baseDays + currentExt} Days + ${newAddedDays} Added = ${totalDays} Total Days`;
    }
    if (displayEl) {
        displayEl.innerText = `₹${totalFee.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
    }
}
</script>
@endpush
@endsection
