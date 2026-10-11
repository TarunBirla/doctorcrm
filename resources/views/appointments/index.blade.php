@extends('layouts.app')

@section('title', 'Appointments & Queue Management')
@section('breadcrumb', 'Appointments')
@section('page_title', 'Appointment Operations & Queue')

@section('content')
@php
    $currRole = $currentRole ?? session('current_role', auth()->user()->role ?? 'super_admin');
    $isStaff = \App\Models\User::isStaffRole($currRole);
    $activePeriod = $period ?? (empty($date) ? 'all' : 'custom_date');
@endphp
<div class="space-y-6">

    <!-- QUICK TABS: ALL APPOINTMENTS / TODAY / UPCOMING -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-slate-100 shadow-xs">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('appointments.index', array_merge(request()->except(['page', 'date', 'period']), ['period' => 'all'])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $activePeriod === 'all' && empty($date) ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                <span>All Appointments ({{ $allAppointmentsCount ?? $totalAppointments }})</span>
            </a>

            <a href="{{ route('appointments.index', array_merge(request()->except(['page', 'date', 'period']), ['period' => 'today'])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $activePeriod === 'today' || $date === now()->toDateString() ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                <i data-lucide="calendar-check" class="w-3.5 h-3.5"></i>
                <span>Today's Appointments ({{ $todayAppointmentsCount ?? 0 }})</span>
            </a>

            <a href="{{ route('appointments.index', array_merge(request()->except(['page', 'date', 'period']), ['period' => 'upcoming'])) }}"
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 {{ $activePeriod === 'upcoming' ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                <i data-lucide="calendar-clock" class="w-3.5 h-3.5"></i>
                <span>Upcoming ({{ $upcomingAppointmentsCount ?? 0 }})</span>
            </a>

            <a href="{{ route('appointments.index', array_merge(request()->except(['page']), ['status' => 'completed'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                <span>Completed ({{ $completedAppointments }})</span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('appointments.create') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center gap-1.5">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>+ Book Appointment</span>
            </a>
        </div>
    </div>

    <!-- 4 TOP KPI CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                {{ empty($date) && $activePeriod === 'all' ? 'All Appointments' : 'Filtered Appointments' }}
            </span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $totalAppointments }}</div>
        </div>

        @if(!$isStaff)
            <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Revenue Collected</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($totalRevenue, 2) }}</div>
            </div>

            <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 block">Total Dues Pending</span>
                <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">₹{{ number_format($totalDues, 2) }}</div>
            </div>
        @else
            <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block">Today's Appointments</span>
                <div class="text-3xl font-extrabold text-blue-600 tracking-tight mt-1">{{ $todayAppointmentsCount ?? 0 }}</div>
            </div>

            <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600 block">Waiting in Queue</span>
                <div class="text-3xl font-extrabold text-amber-600 tracking-tight mt-1">{{ $waitingAppointments }}</div>
            </div>
        @endif

        <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Completed Consultations</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $completedAppointments }}</div>
        </div>

    </div>

    <!-- FILTER BAR (WITH CLINIC & CATEGORY FILTERS) -->
    <div class="card-custom p-5 bg-white border border-slate-100 shadow-xs">
        <form action="{{ route('appointments.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-3 text-xs items-end">
            <input type="hidden" name="period" value="{{ $activePeriod }}">
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
                <label class="block font-semibold text-slate-600 mb-1">Specific Date (Optional)</label>
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
                <h3 class="text-sm font-bold text-slate-900">
                    All Appointments & Physiotherapy Sessions ({{ $appointments->total() }})
                </h3>
                <p class="text-xs text-slate-400">Treatment sessions tracking with recovery percentage, extension days and real-time status</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('calendar.index') }}" class="px-3 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Calendar View</span>
                </a>
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
                        <th class="p-3.5 text-center">Session & Package</th>
                        <th class="p-3.5 min-w-[165px]">Recovery % (कितना % सही हुआ)</th>
                        @if(!$isStaff)
                            <th class="p-3.5">Session / Pkg Fee</th>
                        @endif
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 pr-5 text-right">Session Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($appointments as $apt)
                        @php
                            $patientApts = $apt->patient && $apt->patient->appointments
                                ? $apt->patient->appointments->whereNotIn('status', ['cancelled'])
                                    ->sortBy(fn($a) => $a->appointment_date->format('Y-m-d') . '_' . str_pad($a->id, 8, '0', STR_PAD_LEFT))
                                    ->values()
                                : collect([$apt]);

                            $patientTotalSessions = max(1, $patientApts->count());
                            $patientCompletedSessions = $patientApts->where('status', 'completed')->count();
                            $foundIndex = $patientApts->search(fn($item) => $item->id === $apt->id);
                            $sessionNumber = ($foundIndex !== false) ? ($foundIndex + 1) : 1;
                            $isLastSession = ($sessionNumber === $patientTotalSessions);

                            $dailyFeeVal = (float) ($apt->daily_fee ?? $apt->consultation_fee ?? 500);
                            $patientPackageFee = (float) $patientApts->sum('consultation_fee');
                            if ($patientPackageFee <= 0) {
                                $patientPackageFee = $patientTotalSessions * $dailyFeeVal;
                            }

                            $pct = (int) ($apt->patient?->recovery_percentage ?? $apt->recovery_percentage ?? 0);
                            $barColor = $pct >= 75 ? 'bg-emerald-500' : ($pct >= 40 ? 'bg-amber-500' : ($pct > 0 ? 'bg-blue-500' : 'bg-slate-300'));
                            $badgeClass = $pct >= 100
                                ? 'bg-emerald-600 text-white border-emerald-600'
                                : ($pct >= 75 ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : ($pct >= 40 ? 'bg-amber-50 text-amber-800 border-amber-200' : ($pct > 0 ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200')));
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition group">
                            <td class="p-3.5 pl-5">
                                <span class="font-bold text-slate-900 block">Token #{{ $apt->token_number }}</span>
                                <span class="text-[10px] font-mono text-slate-400">{{ $apt->appointment_no }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="font-semibold text-slate-800 block">{{ $apt->appointment_date->format('d M Y (D)') }}</span>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <i data-lucide="building" class="w-3 h-3 text-slate-400"></i>
                                    <span class="text-[11px] text-slate-600 font-semibold">{{ $apt->clinic?->name ?? 'Main Clinic' }}</span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <a href="{{ route('patients.show', $apt->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-600 block transition">
                                    {{ $apt->patient?->full_name ?? 'Patient' }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $apt->patient?->patient_id }} • {{ $apt->patient?->mobile }}</span>
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
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold {{ $isLastSession ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-800' }}">
                                        Session {{ $sessionNumber }} of {{ $patientTotalSessions }}
                                    </span>
                                    <span class="text-[10px] font-bold mt-0.5 {{ $patientCompletedSessions >= $patientTotalSessions ? 'text-emerald-600' : 'text-slate-400' }}">
                                        {{ $patientCompletedSessions }}/{{ $patientTotalSessions }} Completed
                                    </span>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <div>
                                    <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                                        <span class="px-1.5 py-0.5 rounded border text-[10px] {{ $badgeClass }}">
                                            {{ $pct >= 100 ? '🌟 100% Sahi Ho Gaya' : $pct . '% Sahi Hua' }}
                                        </span>
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
                            @if(!$isStaff)
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-900">₹{{ number_format($dailyFeeVal, 0) }} <span class="text-[10px] font-normal text-slate-400">/day</span></div>
                                    <span class="text-[10px] text-indigo-700 block font-extrabold">Pkg ({{ $patientTotalSessions }}d): ₹{{ number_format($patientPackageFee, 0) }}</span>
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
                            @endif
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
                                        Completed ✔
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
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <!-- 1. DAILY SESSION COMPLETION / CHECK-IN ACTIONS -->
                                    @if($apt->status !== 'completed' && $apt->status !== 'cancelled')
                                        @if($apt->status === 'waiting' || $apt->status === 'in_consultation')
                                            <a href="{{ route('prescriptions.create', ['appointment_id' => $apt->id]) }}" 
                                               title="Doctor Consultation & Prescription Room" 
                                               class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-[11px] font-bold transition flex items-center gap-1 shadow-2xs">
                                                <i data-lucide="stethoscope" class="w-3 h-3"></i>
                                                <span>Consult</span>
                                            </a>
                                        @else
                                            <form action="{{ route('appointments.status', $apt->id) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="status" value="waiting">
                                                <button type="submit" title="Mark Patient Arrived & Waiting" 
                                                        class="px-2 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white rounded-xl text-[11px] font-bold transition">
                                                    Check In
                                                </button>
                                            </form>
                                        @endif

                                        <!-- 1-Click Complete Session Button -->
                                        <form action="{{ route('appointments.status', $apt->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" title="Mark Session {{ $sessionNumber }} Completed"
                                                    class="px-2.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200 rounded-xl text-[11px] font-bold transition inline-flex items-center gap-1">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                <span>Done</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- 2. FINAL SESSION PACKAGE EVALUATION & EXTEND (+DAYS) BUTTON -->
                                    @if($isLastSession)
                                        @if($pct >= 100 && $patientCompletedSessions >= $patientTotalSessions)
                                            <span class="px-2.5 py-1.5 bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-xl text-[10px] font-extrabold inline-flex items-center gap-1">
                                                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                                                <span>100% Cured</span>
                                            </span>
                                        @else
                                            <button type="button"
                                                    onclick='openEditExtendModal(@json($apt), "{{ addslashes($apt->patient?->full_name ?? "Patient") }}", "{{ addslashes($apt->clinic?->name ?? "Clinic") }}", "{{ addslashes($apt->category?->name ?? "Category") }}", {{ $patientTotalSessions }}, {{ $patientCompletedSessions }}, {{ $dailyFeeVal }}, {{ $patientPackageFee }}, {{ $pct }})'
                                                    class="px-2.5 py-1.5 bg-indigo-600 text-white hover:bg-indigo-700 rounded-xl text-[11px] font-bold transition flex items-center gap-1 shadow-xs cursor-pointer"
                                                    title="Final Session: Evaluate Recovery % (80% / 100%) or Extend +Days">
                                                <i data-lucide="calendar-plus" class="w-3.5 h-3.5"></i>
                                                <span>Review / +Extend</span>
                                            </button>
                                        @endif
                                    @endif

                                    <!-- Edit Patient & Package Link -->
                                    @if($apt->patient_id)
                                        <a href="{{ route('patients.edit', $apt->patient_id) }}" title="Edit Patient / Package & Extend Days"
                                           class="p-1.5 bg-slate-50 text-indigo-600 hover:bg-indigo-100 rounded-xl transition">
                                            <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <a href="{{ route('patients.show', $apt->patient_id) }}" title="Patient Profile" 
                                           class="p-1.5 bg-slate-50 text-slate-600 hover:bg-slate-200 rounded-xl transition">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </a>
                                    @endif

                                    <!-- Delete / Cancel -->
                                    @if($apt->status !== 'completed')
                                        <form action="{{ route('appointments.destroy', $apt->id) }}" method="POST" onsubmit="return confirm('Cancel this appointment session?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Cancel Session" class="p-1.5 bg-slate-50 text-rose-600 hover:bg-rose-100 rounded-xl transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isStaff ? 8 : 9 }}" class="p-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-3">
                                    <i data-lucide="calendar-x" class="w-6 h-6"></i>
                                </div>
                                <h4 class="font-bold text-slate-700 text-sm">No Appointments Found</h4>
                                <p class="text-xs text-slate-400 mt-1">Try clicking "All Appointments" or resetting the filters above.</p>
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

<!-- INTERACTIVE MODAL: EVALUATE RECOVERY % (80% / 100%) & EXTEND TREATMENT SESSIONS (+DAYS) -->
<div id="editExtendAppointmentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 md:p-6">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto mx-auto animate-in fade-in zoom-in-95 duration-200">
        
        <!-- MODAL HEADER -->
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-[#0a2540] text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 text-emerald-300 flex items-center justify-center shadow-xs font-bold">
                    <i data-lucide="calendar-plus" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-white text-sm">Review Recovery % & Extend Therapy Package</h3>
                    <p class="text-[11px] text-slate-300 font-semibold">Evaluate recovery (e.g. 80% or 100% Cured) or add +Extra Days (auto-skips Sunday)</p>
                </div>
            </div>
            <button type="button" onclick="closeEditExtendModal()" class="p-1 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- FORM -->
        <form id="editExtendAppointmentForm" method="POST" class="p-6 space-y-5 text-xs">
            @csrf
            @method('PUT')

            <!-- PATIENT SUMMARY BANNER -->
            <div class="p-4 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <span class="text-[10px] uppercase font-bold text-blue-700 tracking-wider block">Patient & Treatment Package</span>
                    <h4 id="modalExtPatientName" class="text-sm font-extrabold text-slate-900 mt-0.5">Patient Name</h4>
                    <p id="modalExtClinicCatInfo" class="text-[11px] text-slate-600 font-semibold mt-0.5">Clinic • Category</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-500 block">Booked Package Progress</span>
                    <div id="modalExtCurrentDaysBadge" class="text-sm font-extrabold text-indigo-700">5 / 5 Days Completed</div>
                </div>
            </div>

            <label class="flex items-center gap-2.5 p-3 rounded-xl bg-emerald-50/70 border border-emerald-200 cursor-pointer">
                <input type="checkbox" name="mark_previous_completed" value="1" checked class="w-4 h-4 accent-emerald-600 rounded">
                <span class="text-xs font-extrabold text-emerald-900">Mark all existing sessions of this package as Completed ✔</span>
            </label>

            <!-- SECTION 1: RECOVERY PERCENTAGE TRACKING (KITNA % SAHI HUA) -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block font-bold text-slate-800 text-xs">Step 1: Patient Recovery % (मरीज कितना % सही हुआ?)</label>
                        <p class="text-[11px] text-slate-500 font-semibold">Set 80% if extending, or 100% when patient is completely cured</p>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <input type="number" id="modalExtRecoveryInput" name="recovery_percentage" min="0" max="100" value="80"
                               oninput="syncRecoveryFromInput(this.value)"
                               class="w-16 px-2 py-1 text-right text-xs font-black text-indigo-700 bg-white border border-slate-300 rounded-lg outline-none focus:border-blue-500">
                        <span class="font-bold text-slate-700 text-xs">%</span>
                    </div>
                </div>

                <!-- SLIDER -->
                <input type="range" id="modalExtRecoverySlider" min="0" max="100" value="80" step="5"
                       oninput="syncRecoveryFromSlider(this.value)"
                       class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">

                <!-- QUICK RECOVERY BUTTONS -->
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                    <span class="text-[10px] font-bold text-slate-400 uppercase mr-1">Quick Set:</span>
                    <button type="button" onclick="syncRecoveryFromInput(50)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-bold text-slate-700 hover:bg-slate-100">50%</button>
                    <button type="button" onclick="syncRecoveryFromInput(75)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-bold text-slate-700 hover:bg-slate-100">75%</button>
                    <button type="button" onclick="syncRecoveryFromInput(80)" class="px-3 py-1 rounded-lg bg-indigo-50 border border-indigo-300 font-extrabold text-indigo-800 hover:bg-indigo-100">80% Sahi Hua</button>
                    <button type="button" onclick="syncRecoveryFromInput(90)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 font-bold text-slate-700 hover:bg-slate-100">90%</button>
                    <button type="button" onclick="syncRecoveryFromInput(100); setModalExtExtraDays(0);" class="px-3 py-1 rounded-lg bg-emerald-600 text-white font-extrabold hover:bg-emerald-700">🌟 100% Sahi Ho Gaya</button>
                </div>

                <!-- LIVE LEVEL BADGE -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[10px] text-slate-400 font-bold">0% (Initial)</span>
                    <span id="modalExtRecoveryLevelText" class="text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700">
                        High Recovery (80%)
                    </span>
                    <span class="text-[10px] text-emerald-600 font-bold">100% (Fully Cured)</span>
                </div>
            </div>

            <!-- SECTION 2: EXTEND SESSIONS / EDIT DAYS -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block font-bold text-slate-800 text-xs">Step 2: Extend Treatment Days (आगे और कितने दिन जोड़ने हैं?)</label>
                        <p class="text-[11px] text-slate-500 font-semibold">Creates new daily appointments automatically (skipping Sunday)</p>
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
                    <button type="button" onclick="setModalExtExtraDays(0)" class="px-2.5 py-1 rounded-lg bg-slate-200/70 text-slate-700 font-bold text-xs transition">0 (No Extend)</button>
                    <button type="button" onclick="setModalExtExtraDays(1)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+1 Day</button>
                    <button type="button" onclick="setModalExtExtraDays(2)" class="px-3 py-1 rounded-lg bg-blue-50 border border-blue-300 hover:bg-blue-100 text-blue-800 font-extrabold text-xs transition">+2 Days</button>
                    <button type="button" onclick="setModalExtExtraDays(3)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+3 Days</button>
                    <button type="button" onclick="setModalExtExtraDays(5)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+5 Days</button>
                    <button type="button" onclick="setModalExtExtraDays(7)" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 hover:bg-indigo-50 hover:border-indigo-300 text-slate-700 font-bold text-xs transition">+7 Days</button>
                </div>

                <!-- TOTAL DURATION SUMMARY & FEE MATH -->
                <div class="mt-3 p-3 rounded-xl bg-indigo-50/70 border border-indigo-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-indigo-800 block">Total Package Sessions (Sunday Skipped)</span>
                        <div id="modalExtTotalDurationFormula" class="text-xs font-bold text-indigo-900 mt-0.5">5 Existing Days + 0 New Days = 5 Total Days</div>
                    </div>
                    @if(!$isStaff)
                        <div class="text-right">
                            <span class="text-[10px] uppercase font-bold text-emerald-800 block">Updated Package Fee</span>
                            <div id="modalExtTotalFeeDisplay" class="text-sm font-black text-emerald-700">₹2,500.00</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SECTION 3: DAILY FEE & STATUS -->
            <div class="grid grid-cols-1 {{ $isStaff ? 'md:grid-cols-1' : 'md:grid-cols-2' }} gap-3">
                @if(!$isStaff)
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Per-Day Session Fee (₹)</label>
                        <input type="number" step="0.01" id="modalExtDailyFee" name="daily_fee" value="500" min="0"
                               oninput="recalcModalExtMath()"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-bold text-slate-800">
                    </div>
                @else
                    <input type="hidden" id="modalExtDailyFee" name="daily_fee" value="500">
                @endif
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Current Session Status</label>
                    <select id="modalExtStatusSelect" name="status" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800">
                        <option value="completed">Completed ✔</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="waiting">Waiting</option>
                        <option value="in_consultation">In Consultation</option>
                    </select>
                </div>
            </div>

            <!-- SECTION 4: DOCTOR'S RECOVERY REMARKS / EXTENSION REASON -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Doctor's Recovery Remarks & Notes</label>
                <textarea id="modalExtRecoveryNotes" name="recovery_notes" rows="2" 
                          placeholder="e.g. Patient 80% relieved after 5 days, extending +2 days / Or: 100% recovered after completing all sessions..."
                          class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-blue-500 outline-none font-semibold text-slate-800"></textarea>
            </div>

            <!-- MODAL ACTIONS -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditExtendModal()" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2.5 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-extrabold transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-400"></i>
                    <span>Save Recovery % & Create Extended Sessions</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentAppointmentData = null;
let currentPatientTotalSessions = 1;
let currentPatientPackageFee = 0;

function openEditExtendModal(apt, patientName, clinicName, categoryName, totalSessions, completedSessions, dailyFeeVal, packageFeeVal, recoveryPctVal) {
    currentAppointmentData = apt;
    currentPatientTotalSessions = parseInt(totalSessions) || 1;
    currentPatientPackageFee = parseFloat(packageFeeVal) || 0;

    const form = document.getElementById('editExtendAppointmentForm');
    form.action = `/appointments/${apt.id}`;

    document.getElementById('modalExtPatientName').innerText = patientName || 'Patient';
    document.getElementById('modalExtClinicCatInfo').innerText = `${clinicName || 'Clinic'} • ${categoryName || 'Physiotherapy'}`;
    document.getElementById('modalExtCurrentDaysBadge').innerText = `${completedSessions} / ${currentPatientTotalSessions} Days Completed (₹${currentPatientPackageFee.toLocaleString('en-IN')})`;

    // Recovery %
    const recoveryPct = parseInt(recoveryPctVal ?? apt.recovery_percentage) || 0;
    document.getElementById('modalExtRecoveryInput').value = recoveryPct;
    document.getElementById('modalExtRecoverySlider').value = recoveryPct;
    updateRecoveryLevelDisplay(recoveryPct);

    // Days & Fee
    document.getElementById('modalExtAdditionalDaysInput').value = 0;
    document.getElementById('modalExtDailyFee').value = parseFloat(dailyFeeVal || apt.daily_fee || 500).toFixed(2);
    document.getElementById('modalExtStatusSelect').value = 'completed';
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
    document.getElementById('modalExtRecoveryInput').value = num;
    document.getElementById('modalExtRecoverySlider').value = num;
    updateRecoveryLevelDisplay(num);
}

function updateRecoveryLevelDisplay(pct) {
    const el = document.getElementById('modalExtRecoveryLevelText');
    if (!el) return;
    if (pct >= 100) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300";
        el.innerText = `🌟 Fully Recovered (100% Sahi Ho Gaya)`;
    } else if (pct >= 75) {
        el.className = "text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200";
        el.innerText = `🟢 High Recovery (${pct}% Sahi Hua)`;
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
    const existingDays = currentPatientTotalSessions || 1;
    const newAddedDays = parseInt(document.getElementById('modalExtAdditionalDaysInput').value) || 0;
    const totalDays = existingDays + newAddedDays;

    const dailyFee = parseFloat(document.getElementById('modalExtDailyFee').value) || 0;
    const addedFee = newAddedDays * dailyFee;
    const totalFee = currentPatientPackageFee + addedFee;

    const formulaEl = document.getElementById('modalExtTotalDurationFormula');
    const displayEl = document.getElementById('modalExtTotalFeeDisplay');

    if (formulaEl) {
        formulaEl.innerText = `${existingDays} Existing Days + ${newAddedDays} New Days = ${totalDays} Total Days`;
    }
    if (displayEl) {
        displayEl.innerText = `₹${totalFee.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})} (+₹${addedFee.toLocaleString('en-IN')})`;
    }
}
</script>
@endpush
@endsection
