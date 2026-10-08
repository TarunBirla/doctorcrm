@extends('layouts.app')

@section('title', 'Clinic Operations Dashboard')
@section('breadcrumb', 'Overview')
@section('page_title', 'Clinic Operations Dashboard')

@section('content')
<div class="space-y-6">

    <!-- DATE FILTER BAR (MATCHING THEME) -->
    <div class="card-custom p-4 flex flex-wrap items-center justify-between gap-4 bg-white">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Date Period:</span>
            <span class="text-xs font-semibold text-blue-700 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">{{ $filterLabel }}</span>
        </div>

        <!-- Filter Selector Form -->
        <form action="{{ route('dashboard') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
            <select name="date_filter" onchange="this.form.submit()" 
                    class="px-3 py-1.5 border border-slate-200 rounded-xl bg-slate-50 font-medium text-slate-700 hover:bg-white focus:bg-white focus:border-blue-500 outline-none">
                <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $dateFilter === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="tomorrow" {{ $dateFilter === 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                <option value="this_week" {{ $dateFilter === 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="last_week" {{ $dateFilter === 'last_week' ? 'selected' : '' }}>Last Week</option>
                <option value="this_month" {{ $dateFilter === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $dateFilter === 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ $dateFilter === 'this_year' ? 'selected' : '' }}>This Year</option>
                <option value="custom" {{ $dateFilter === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
            </select>

            @if($dateFilter === 'custom')
                <input type="date" name="start_date" value="{{ request('start_date', $startDate->toDateString()) }}" class="px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-xs">
                <span class="text-slate-400">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate->toDateString()) }}" class="px-2.5 py-1.5 border border-slate-200 rounded-xl bg-slate-50 text-xs">
                <button type="submit" class="px-3 py-1.5 bg-navy-900 text-white rounded-xl font-bold">Apply</button>
            @endif

            <a href="{{ route('dashboard', ['date_filter' => 'today']) }}" class="px-3 py-1.5 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-medium">
                Reset
            </a>
        </form>
    </div>

    <!-- 4 MAIN METRIC CARDS (EXACT SCREENSHOT STYLE) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- CARD 1: APPOINTMENTS -->
        <div class="card-custom p-5 relative overflow-hidden transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Appointments</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $totalAppointments }}</div>
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Done: <strong class="text-emerald-700">{{ $completedAppointments }}</strong></span>
                    <span>Pending: <strong class="text-amber-700">{{ $pendingAppointments + $waitingAppointments }}</strong></span>
                    <span>Cancel: <strong class="text-rose-700">{{ $cancelledAppointments }}</strong></span>
                </div>
            </div>
        </div>

        <!-- CARD 2: PATIENT QUEUE & WAITING -->
        <div class="card-custom p-5 relative overflow-hidden transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Today's Patient Queue</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $waitingAppointments + $inConsultationAppointments }} <span class="text-sm font-normal text-slate-400">Patients</span>
                </div>
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>In Doctor Room: <strong class="text-indigo-700">{{ $inConsultationAppointments }}</strong></span>
                    <span>Waiting Lobby: <strong class="text-purple-700">{{ $waitingAppointments }}</strong></span>
                </div>
            </div>
        </div>

        <!-- CARD 3: TOTAL REVENUE / COLLECTION -->
        <div class="card-custom p-5 relative overflow-hidden transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Collection</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight">₹{{ number_format($totalCollected, 2) }}</div>
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>Billed: <strong class="text-slate-700">₹{{ number_format($totalBilled, 0) }}</strong></span>
                    <span>Expenses: <strong class="text-rose-600">₹{{ number_format($totalExpenses, 0) }}</strong></span>
                    <a href="{{ route('reports.financial') }}" class="text-blue-600 font-semibold hover:underline">Report →</a>
                </div>
            </div>
        </div>

        <!-- CARD 4: FINANCIAL BALANCES / DUES -->
        <div class="card-custom p-5 relative overflow-hidden transition hover:shadow-md">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Financial Balances</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-xs text-slate-500">Outstanding Dues (Receivable):</div>
                <div class="text-2xl font-extrabold text-rose-600 tracking-tight">₹{{ number_format($totalOutstandingDues, 2) }}</div>
                <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span>{{ $patientsWithDuesCount }} Patients with Dues</span>
                    <a href="{{ route('due-payments.index') }}" class="text-blue-700 font-bold hover:underline">Collect Dues →</a>
                </div>
            </div>
        </div>

    </div>

    <!-- QUICK ACTIONS ROW (MATCHING MEDIA 1 SCREENSHOT) -->
    <div class="card-custom p-4 bg-white flex flex-wrap items-center gap-3">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">QUICK ACTIONS:</span>
        
        <button onclick="openModal('quickPatientModal')" 
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-blue-200 bg-blue-50/50 hover:bg-blue-100/70 text-blue-800 text-xs font-semibold transition">
            <i data-lucide="user-plus" class="w-3.5 h-3.5 text-blue-600"></i>
            <span>Register Patient</span>
        </button>

        <button onclick="openModal('quickAppointmentModal')" 
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-indigo-200 bg-indigo-50/50 hover:bg-indigo-100/70 text-indigo-800 text-xs font-semibold transition">
            <i data-lucide="calendar-plus" class="w-3.5 h-3.5 text-indigo-600"></i>
            <span>New Appointment</span>
        </button>

        <a href="{{ route('consultations.create') }}" 
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-emerald-200 bg-emerald-50/50 hover:bg-emerald-100/70 text-emerald-800 text-xs font-semibold transition">
            <i data-lucide="stethoscope" class="w-3.5 h-3.5 text-emerald-600"></i>
            <span>Start Consultation</span>
        </a>

        <a href="{{ route('invoices.create') }}" 
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-amber-200 bg-amber-50/50 hover:bg-amber-100/70 text-amber-800 text-xs font-semibold transition">
            <i data-lucide="receipt" class="w-3.5 h-3.5 text-amber-600"></i>
            <span>POS Billing Counter</span>
        </a>

        <a href="{{ route('due-payments.index') }}" 
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-rose-200 bg-rose-50/50 hover:bg-rose-100/70 text-rose-800 text-xs font-semibold transition">
            <i data-lucide="credit-card" class="w-3.5 h-3.5 text-rose-600"></i>
            <span>Collect Pending Dues</span>
        </a>

        <a href="{{ route('queue.index') }}" 
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-purple-200 bg-purple-50/50 hover:bg-purple-100/70 text-purple-800 text-xs font-semibold transition">
            <i data-lucide="layout-list" class="w-3.5 h-3.5 text-purple-600"></i>
            <span>Live Queue Board</span>
        </a>
    </div>

    <!-- WHO IS NEXT CALLOUT CARD (IF PATIENT WAITING) -->
    @if($nextPatientAppointment)
        <div class="card-custom p-4 bg-gradient-to-r from-blue-50 via-indigo-50/60 to-white border-blue-200 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-black text-sm shadow-sm shadow-blue-200">
                    #{{ $nextPatientAppointment->token_number }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-600 text-white uppercase tracking-wider">WHO IS NEXT</span>
                        <span class="text-xs text-slate-500">Waiting since {{ $nextPatientAppointment->waiting_since ? $nextPatientAppointment->waiting_since->diffForHumans() : 'just now' }}</span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-900 mt-0.5">
                        {{ $nextPatientAppointment->patient->full_name }} 
                        <span class="text-xs font-normal text-slate-500">({{ $nextPatientAppointment->patient->patient_id }} • {{ $nextPatientAppointment->patient->age }}y / {{ $nextPatientAppointment->patient->gender }})</span>
                    </h4>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <form action="{{ route('queue.start', $nextPatientAppointment->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        <i data-lucide="stethoscope" class="w-4 h-4"></i>
                        <span>Start Consultation Now</span>
                    </button>
                </form>
                <a href="{{ route('patients.show', $nextPatientAppointment->patient_id) }}" class="px-3 py-2 border border-slate-200 bg-white rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    View Record
                </a>
            </div>
        </div>
    @endif

    <!-- TWO COLUMN DATA SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- COLUMN 1 & 2: TODAY'S QUEUE & APPOINTMENTS TABLE (2 COLS) -->
        <div class="lg:col-span-2 space-y-6">
            
            <div class="card-custom bg-white overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Today's Patient Queue</h3>
                        <p class="text-xs text-slate-400">Real-time status of patients in clinic today</p>
                    </div>
                    <a href="{{ route('queue.index') }}" class="text-xs font-semibold text-blue-700 hover:underline flex items-center gap-1">
                        <span>View Full Queue</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-3 pl-5">Token</th>
                                <th class="p-3">Patient</th>
                                <th class="p-3">Time & Type</th>
                                <th class="p-3">Status</th>
                                <th class="p-3">Payment</th>
                                <th class="p-3 pr-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($todayQueue as $apt)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="p-3 pl-5 font-bold text-slate-700">
                                        <span class="w-7 h-7 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-800">
                                            #{{ $apt->token_number }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <a href="{{ route('patients.show', $apt->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700">
                                            {{ $apt->patient->full_name }}
                                        </a>
                                        <div class="text-[11px] text-slate-400">{{ $apt->patient->patient_id }} • {{ $apt->patient->mobile }}</div>
                                    </td>
                                    <td class="p-3">
                                        <div class="font-medium text-slate-700">{{ $apt->appointment_time }}</div>
                                        <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase bg-slate-100 text-slate-600">
                                            {{ $apt->appointment_type }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        @if($apt->status === 'in_consultation')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800 animate-pulse">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span> In Consultation
                                            </span>
                                        @elseif($apt->status === 'waiting')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Waiting
                                            </span>
                                        @elseif($apt->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span> Completed
                                            </span>
                                        @else
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 capitalize">
                                                {{ $apt->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3">
                                        @if($apt->payment_status === 'paid')
                                            <span class="text-[11px] font-bold text-emerald-700">₹{{ number_format($apt->consultation_fee, 0) }} (Paid)</span>
                                        @elseif($apt->payment_status === 'partially_paid')
                                            <span class="text-[11px] font-bold text-amber-700">Partial Due</span>
                                        @else
                                            <span class="text-[11px] font-bold text-rose-600">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="p-3 pr-5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if($apt->status === 'waiting' || $apt->status === 'confirmed' || $apt->status === 'scheduled')
                                                <form action="{{ route('queue.start', $apt->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" title="Start Consultation" class="p-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-lg transition">
                                                        <i data-lucide="stethoscope" class="w-3.5 h-3.5"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('patients.show', $apt->patient_id) }}" title="Patient Profile" class="p-1.5 bg-slate-50 text-slate-600 hover:bg-slate-200 rounded-lg transition">
                                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">
                                        <i data-lucide="calendar" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                        No appointments scheduled in the queue today.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- APPOINTMENTS & REVENUE 7-DAY TREND CHART -->
            <div class="card-custom p-5 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">7-Day Operational Activity Trend</h3>
                        <p class="text-xs text-slate-400">Patient footfall and daily billing collection</p>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

        </div>

        <!-- COLUMN 3: FINANCIAL BREAKDOWN & QUICK METRICS (1 COL) -->
        <div class="space-y-6">

            <!-- PAYMENT METHODS BREAKDOWN (MATCHING THEME) -->
            <div class="card-custom p-5 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Payment Breakdown</h3>
                    <span class="text-[10px] font-bold text-slate-400 uppercase">This Period</span>
                </div>

                <div class="space-y-3.5 text-xs">
                    <div>
                        <div class="flex items-center justify-between font-semibold text-slate-700 mb-1">
                            <span class="flex items-center gap-1.5"><i data-lucide="banknote" class="w-3.5 h-3.5 text-emerald-600"></i> Cash</span>
                            <span>₹{{ number_format($cashCollection, 2) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $totalCollected > 0 ? min(100, ($cashCollection / $totalCollected) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between font-semibold text-slate-700 mb-1">
                            <span class="flex items-center gap-1.5"><i data-lucide="smartphone" class="w-3.5 h-3.5 text-blue-600"></i> UPI / QR Code</span>
                            <span>₹{{ number_format($upiCollection, 2) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $totalCollected > 0 ? min(100, ($upiCollection / $totalCollected) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between font-semibold text-slate-700 mb-1">
                            <span class="flex items-center gap-1.5"><i data-lucide="credit-card" class="w-3.5 h-3.5 text-indigo-600"></i> Credit / Debit Card</span>
                            <span>₹{{ number_format($cardCollection, 2) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $totalCollected > 0 ? min(100, ($cardCollection / $totalCollected) * 100) : 0 }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between font-semibold text-slate-700 mb-1">
                            <span class="flex items-center gap-1.5"><i data-lucide="building" class="w-3.5 h-3.5 text-purple-600"></i> Bank Transfer</span>
                            <span>₹{{ number_format($bankCollection, 2) }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $totalCollected > 0 ? min(100, ($bankCollection / $totalCollected) * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-700">Net Clinic Profit:</span>
                    <span class="font-extrabold {{ $netIncome >= 0 ? 'text-emerald-700' : 'text-rose-600' }} text-sm">
                        ₹{{ number_format($netIncome, 2) }}
                    </span>
                </div>
            </div>

            <!-- RECENT PAYMENTS LOG -->
            <div class="card-custom p-5 bg-white">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">Recent Collections</h3>
                    <a href="{{ route('payments.index') }}" class="text-xs text-blue-600 font-semibold hover:underline">View All</a>
                </div>

                <div class="space-y-3 text-xs">
                    @forelse($recentTransactions as $txn)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div>
                                <span class="font-bold text-slate-800 block">{{ $txn->patient ? $txn->patient->full_name : 'Patient' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $txn->receipt_no }} • {{ $txn->payment_method }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-emerald-700 block">+₹{{ number_format($txn->amount, 2) }}</span>
                                <span class="text-[10px] text-slate-400">{{ $txn->payment_date->format('d M') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No recent payments recorded.</p>
                    @endforelse
                </div>
            </div>

            <!-- CLINIC DOCTOR PROFILE MINI-CARD -->
            @if($doctors->first())
                @php $doc = $doctors->first(); @endphp
                <div class="card-custom p-5 bg-gradient-to-br from-slate-900 to-navy-900 text-white">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center font-bold text-base text-blue-300">
                            DR
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-white">{{ $doc->name }}</h4>
                            <p class="text-[11px] text-blue-200">{{ $doc->specialization }}</p>
                            <span class="text-[10px] text-slate-400 font-mono">Reg: {{ $doc->registration_no }}</span>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
                        <span>Consultation Fee:</span>
                        <span class="font-bold text-white text-sm">₹{{ number_format($doc->consultation_fee, 0) }}</span>
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Initialize 7-Day Trend Chart
    const ctx = document.getElementById('trendChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendDates) !!},
                datasets: [
                    {
                        label: 'Appointments',
                        data: {!! json_encode($trendAppointments) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Collections (₹)',
                        data: {!! json_encode($trendCollections) !!},
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.3,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Appointments Count', font: { size: 10 } }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Amount (INR)', font: { size: 10 } }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11 } }
                    }
                }
            }
        });
    }
</script>
@endpush
