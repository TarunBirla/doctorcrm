@extends('layouts.app')

@section('title', 'Payment Manage & Reports')
@section('breadcrumb', 'Finance / Payment Manage')
@section('page_title', 'Payment Manage & Complete Financial Reports')

@section('content')
<div class="space-y-6">

    <!-- TOP HEADER BANNER & EXPORT ACTIONS -->
    <div class="card-custom p-5 sm:p-6 bg-white flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-start gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0">
                <i data-lucide="indian-rupee" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Payment Manage & Complete Reports</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        {{ $periodLabel }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Track all patient-wise <strong class="text-emerald-700">Paid</strong>, <strong class="text-rose-600">Unpaid</strong> &amp; <strong class="text-amber-700">Partial Due</strong> bills with Day, Week, Month, Year, Clinic &amp; Category filters.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('payments.report.print', request()->query()) }}" target="_blank"
               class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50/70 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition shadow-2xs">
                <i data-lucide="printer" class="w-4 h-4 text-emerald-600"></i>
                <span>Print / PDF Report</span>
            </a>
            <a href="{{ route('payments.export', request()->query()) }}"
               class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold transition shadow-2xs">
                <i data-lucide="download" class="w-4 h-4 text-slate-500"></i>
                <span>Export CSV / Excel</span>
            </a>
            <a href="{{ route('invoices.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-navy-900 hover:bg-navy-800 text-white text-xs font-bold transition shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Create Invoice</span>
            </a>
        </div>
    </div>

    <!-- DAY / WEEK / MONTH / YEAR SNAPSHOT CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Today -->
        <a href="{{ route('payments.index', array_merge(request()->except(['page', 'date', 'month', 'year', 'start_date', 'end_date']), ['period' => 'today'])) }}"
           class="card-custom p-4 sm:p-5 transition hover:shadow-md block {{ $period === 'today' ? 'ring-2 ring-emerald-600 bg-emerald-50/30 border-emerald-300' : 'bg-white hover:border-slate-300' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider {{ $period === 'today' ? 'text-emerald-700' : 'text-slate-400' }} flex items-center gap-1.5">
                    <i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-emerald-600"></i>
                    Today (Day Report)
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                    {{ $periodSnapshots['today']['count'] }} Bills
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">
                ₹{{ number_format($periodSnapshots['today']['paid'], 0) }}
                <span class="text-[11px] font-bold text-emerald-600 ml-1">Paid</span>
            </div>
            <div class="flex items-center justify-between text-[11px] font-semibold mt-2 pt-2 border-t border-slate-100">
                <span class="text-slate-500">Billed: <strong class="text-slate-700">₹{{ number_format($periodSnapshots['today']['billed'], 0) }}</strong></span>
                <span class="text-rose-600 font-bold">Due: ₹{{ number_format($periodSnapshots['today']['due'], 0) }}</span>
            </div>
        </a>

        <!-- This Week -->
        <a href="{{ route('payments.index', array_merge(request()->except(['page', 'date', 'month', 'year', 'start_date', 'end_date']), ['period' => 'this_week'])) }}"
           class="card-custom p-4 sm:p-5 transition hover:shadow-md block {{ $period === 'this_week' ? 'ring-2 ring-blue-600 bg-blue-50/30 border-blue-300' : 'bg-white hover:border-slate-300' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider {{ $period === 'this_week' ? 'text-blue-700' : 'text-slate-400' }} flex items-center gap-1.5">
                    <i data-lucide="calendar-range" class="w-3.5 h-3.5 text-blue-600"></i>
                    This Week Report
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                    {{ $periodSnapshots['week']['count'] }} Bills
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">
                ₹{{ number_format($periodSnapshots['week']['paid'], 0) }}
                <span class="text-[11px] font-bold text-blue-600 ml-1">Paid</span>
            </div>
            <div class="flex items-center justify-between text-[11px] font-semibold mt-2 pt-2 border-t border-slate-100">
                <span class="text-slate-500">Billed: <strong class="text-slate-700">₹{{ number_format($periodSnapshots['week']['billed'], 0) }}</strong></span>
                <span class="text-rose-600 font-bold">Due: ₹{{ number_format($periodSnapshots['week']['due'], 0) }}</span>
            </div>
        </a>

        <!-- This Month -->
        <a href="{{ route('payments.index', array_merge(request()->except(['page', 'date', 'month', 'year', 'start_date', 'end_date']), ['period' => 'this_month'])) }}"
           class="card-custom p-4 sm:p-5 transition hover:shadow-md block {{ $period === 'this_month' ? 'ring-2 ring-indigo-600 bg-indigo-50/30 border-indigo-300' : 'bg-white hover:border-slate-300' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider {{ $period === 'this_month' ? 'text-indigo-700' : 'text-slate-400' }} flex items-center gap-1.5">
                    <i data-lucide="calendar-days" class="w-3.5 h-3.5 text-indigo-600"></i>
                    Monthly ({{ now()->format('M Y') }})
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                    {{ $periodSnapshots['month']['count'] }} Bills
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">
                ₹{{ number_format($periodSnapshots['month']['paid'], 0) }}
                <span class="text-[11px] font-bold text-indigo-600 ml-1">Paid</span>
            </div>
            <div class="flex items-center justify-between text-[11px] font-semibold mt-2 pt-2 border-t border-slate-100">
                <span class="text-slate-500">Billed: <strong class="text-slate-700">₹{{ number_format($periodSnapshots['month']['billed'], 0) }}</strong></span>
                <span class="text-rose-600 font-bold">Due: ₹{{ number_format($periodSnapshots['month']['due'], 0) }}</span>
            </div>
        </a>

        <!-- This Year -->
        <a href="{{ route('payments.index', array_merge(request()->except(['page', 'date', 'month', 'year', 'start_date', 'end_date']), ['period' => 'this_year'])) }}"
           class="card-custom p-4 sm:p-5 transition hover:shadow-md block {{ $period === 'this_year' ? 'ring-2 ring-slate-900 bg-slate-50 border-slate-400' : 'bg-white hover:border-slate-300' }}">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider {{ $period === 'this_year' ? 'text-slate-900' : 'text-slate-400' }} flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-700"></i>
                    Yearly ({{ now()->format('Y') }})
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                    {{ $periodSnapshots['year']['count'] }} Bills
                </span>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">
                ₹{{ number_format($periodSnapshots['year']['paid'], 0) }}
                <span class="text-[11px] font-bold text-emerald-600 ml-1">Paid</span>
            </div>
            <div class="flex items-center justify-between text-[11px] font-semibold mt-2 pt-2 border-t border-slate-100">
                <span class="text-slate-500">Billed: <strong class="text-slate-700">₹{{ number_format($periodSnapshots['year']['billed'], 0) }}</strong></span>
                <span class="text-rose-600 font-bold">Due: ₹{{ number_format($periodSnapshots['year']['due'], 0) }}</span>
            </div>
        </a>
    </div>

    <!-- ADVANCED MULTI-FILTER PANEL -->
    <div class="card-custom p-4 sm:p-5 bg-white space-y-4">
        <form method="GET" action="{{ route('payments.index') }}" id="paymentFilterForm" class="space-y-4 text-xs">
            <input type="hidden" name="tab" id="activeTabInput" value="{{ $activeTab }}">
            <input type="hidden" name="period" id="periodField" value="{{ $period }}">

            <!-- Quick Period Selector Pills -->
            <div class="flex flex-wrap items-center gap-1.5 pb-3 border-b border-slate-100">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mr-1.5 flex items-center gap-1">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-blue-600"></i>
                    Time Period:
                </span>
                @php
                    $periodOptions = [
                        'all' => 'All Payments',
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'this_week' => 'This Week',
                        'last_week' => 'Last Week',
                        'this_month' => 'This Month',
                        'last_month' => 'Last Month',
                        'this_year' => 'This Year',
                        'custom_date' => 'Specific Day',
                        'custom_month' => 'Specific Month',
                        'custom_year' => 'Specific Year',
                        'custom_range' => 'Custom Range',
                    ];
                @endphp
                @foreach($periodOptions as $pKey => $pText)
                    <button type="button" onclick="selectPeriodOption('{{ $pKey }}')"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition {{ $period === $pKey ? 'bg-navy-900 text-white shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700' }}">
                        {{ $pText }}
                    </button>
                @endforeach
            </div>

            <!-- Dynamic Date / Month / Year / Range Inputs -->
            <div id="customPeriodInputs" class="{{ in_array($period, ['custom_date', 'custom_month', 'custom_year', 'custom_range']) ? 'flex' : 'hidden' }} flex-wrap items-end gap-3 p-3.5 rounded-xl bg-blue-50/50 border border-blue-200/80">
                <div id="wrapCustomDate" class="{{ $period === 'custom_date' ? 'block' : 'hidden' }}">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Select Specific Day</label>
                    <input type="date" name="date" value="{{ $date ?: now()->toDateString() }}"
                           class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-blue-500">
                </div>

                <div id="wrapCustomMonth" class="{{ $period === 'custom_month' ? 'block' : 'hidden' }}">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Select Month &amp; Year</label>
                    <input type="month" name="month" value="{{ $month ?: now()->format('Y-m') }}"
                           class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-blue-500">
                </div>

                <div id="wrapCustomYear" class="{{ $period === 'custom_year' ? 'block' : 'hidden' }}">
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Select Year</label>
                    <select name="year" class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-blue-500 min-w-[120px]">
                        @for($y = (int)now()->format('Y') + 1; $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div id="wrapCustomRange" class="{{ $period === 'custom_range' ? 'flex' : 'hidden' }} flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">From Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                               class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">To Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                               class="px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 outline-none focus:border-blue-500">
                    </div>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-xs flex items-center gap-1.5">
                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                    <span>Apply Date Filter</span>
                </button>
            </div>

            <!-- Main Filter Dropdowns: Patient, Clinic, Category, Status, Method, Search -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                <div>
                    <label class="block font-bold text-slate-600 mb-1">Patient-Wise</label>
                    <select name="patient_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                        <option value="">All Patients</option>
                        @foreach($patients as $pt)
                            <option value="{{ $pt->id }}" {{ (string)$patientId === (string)$pt->id ? 'selected' : '' }}>
                                {{ $pt->full_name }} ({{ $pt->patient_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Clinic-Wise</label>
                    <select name="clinic_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                        <option value="">All Clinics</option>
                        @foreach($clinics as $cl)
                            <option value="{{ $cl->id }}" {{ (string)$clinicId === (string)$cl->id ? 'selected' : '' }}>
                                {{ $cl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Category-Wise</label>
                    <select name="category_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Paid / Unpaid Status</label>
                    <select name="payment_status" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                        <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>All (Paid + Unpaid)</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid Only</option>
                        <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                        <option value="partially_paid" {{ $paymentStatus === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="due" {{ $paymentStatus === 'due' ? 'selected' : '' }}>All Pending Dues</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Payment Mode</label>
                    <select name="method" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                        <option value="">All Modes</option>
                        <option value="Cash" {{ $method === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="UPI" {{ $method === 'UPI' ? 'selected' : '' }}>UPI / GPay / PhonePe</option>
                        <option value="Card" {{ $method === 'Card' ? 'selected' : '' }}>Card</option>
                        <option value="Bank Transfer" {{ $method === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-600 mb-1">Search Patient / Invoice</label>
                    <div class="flex items-center gap-1.5">
                        <input type="text" name="search" value="{{ $search }}" placeholder="Name, ID, Mobile, INV..."
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 outline-none focus:bg-white focus:border-blue-500 font-semibold">
                        <button type="submit" class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition shrink-0" title="Search">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </button>
                        <a href="{{ route('payments.index') }}" class="p-2 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl transition shrink-0" title="Reset Filters">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- FILTERED KPI TOTALS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="card-custom p-5 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Billed ({{ $periodLabel }})</span>
                <div class="text-2xl font-black text-slate-900 mt-1">₹{{ number_format($totalBilled, 2) }}</div>
                <span class="text-[11px] font-semibold text-blue-600 mt-1 block">{{ $allFilteredInvoices->count() }} Total Invoices</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="receipt" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Paid / Collected</span>
                <div class="text-2xl font-black text-emerald-700 mt-1">₹{{ number_format($totalCollected, 2) }}</div>
                <span class="text-[11px] font-semibold text-emerald-600 mt-1 block">{{ $paidCount }} Paid • {{ $partialCount }} Partial</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Unpaid / Pending Due</span>
                <div class="text-2xl font-black text-rose-600 mt-1">₹{{ number_format($totalDue, 2) }}</div>
                <span class="text-[11px] font-semibold text-rose-600 mt-1 block">{{ $unpaidCount }} Unpaid • {{ $partialCount }} Partial Due</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white flex items-center justify-between">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Payment Mode Split</span>
                <div class="grid grid-cols-2 gap-x-3 gap-y-1 text-xs font-bold text-slate-700 mt-1.5">
                    <span>Cash: <strong class="text-slate-900">₹{{ number_format($cashTotal, 0) }}</strong></span>
                    <span>UPI: <strong class="text-blue-700">₹{{ number_format($upiTotal, 0) }}</strong></span>
                    <span>Card: <strong class="text-indigo-700">₹{{ number_format($cardTotal, 0) }}</strong></span>
                    <span>Bank: <strong class="text-teal-700">₹{{ number_format($bankTotal, 0) }}</strong></span>
                </div>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                <i data-lucide="wallet" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- VIEW MODE TABS -->
    <div class="border-b border-slate-200 bg-white rounded-2xl p-1.5 shadow-2xs overflow-x-auto">
        <div class="flex items-center gap-1.5 min-w-max text-xs">
            <button type="button" id="btnTabAll" onclick="switchPaymentTab('all_payments')"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition {{ $activeTab === 'all_payments' ? 'bg-navy-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i data-lucide="list-checks" class="w-4 h-4"></i>
                <span>All Payments — Paid &amp; Unpaid ({{ $allFilteredInvoices->count() }})</span>
            </button>

            <button type="button" id="btnTabPatient" onclick="switchPaymentTab('patient_wise')"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition {{ $activeTab === 'patient_wise' ? 'bg-navy-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Patient-Wise Summary ({{ $patientWiseSummary->count() }} Patients)</span>
            </button>

            <button type="button" id="btnTabReports" onclick="switchPaymentTab('reports')"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition {{ $activeTab === 'reports' ? 'bg-navy-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                <span>Detailed Reports (Day / Month / Clinic / Category)</span>
            </button>
        </div>
    </div>

    <!-- TAB 1: ALL PAYMENTS (PAID, UNPAID, PARTIALLY PAID) -->
    <div id="tabSecAllPayments" class="{{ $activeTab === 'all_payments' ? 'block' : 'hidden' }}">
        <div class="card-custom bg-white overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="file-Spreadsheet" class="w-4 h-4 text-blue-600"></i>
                        <span>All Patient Invoices &amp; Payment Records</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">View Paid, Unpaid &amp; Partially Paid bills, collect pending dues, or download/print any invoice</p>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="px-3 py-1 rounded-full font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Paid: ₹{{ number_format($totalCollected, 0) }}
                    </span>
                    <span class="px-3 py-1 rounded-full font-bold bg-rose-50 text-rose-700 border border-rose-200">
                        Unpaid Due: ₹{{ number_format($totalDue, 0) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-3.5 pl-5">Invoice No &amp; Date</th>
                            <th class="p-3.5">Patient Details</th>
                            <th class="p-3.5">Clinic &amp; Category</th>
                            <th class="p-3.5">Total Bill</th>
                            <th class="p-3.5">Paid Amount</th>
                            <th class="p-3.5">Unpaid / Due</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Mode</th>
                            <th class="p-3.5 pr-5 text-right">Invoice &amp; Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoices as $inv)
                            @php
                                $clinicObj = $inv->appointment?->clinic ?? $inv->appointments->first()?->clinic ?? $inv->patient?->clinic;
                                $catObj = $inv->appointment?->category ?? $inv->appointments->first()?->category ?? $inv->patient?->category;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5 pl-5">
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="font-bold text-blue-700 hover:underline font-mono block">
                                        {{ $inv->invoice_no }}
                                    </a>
                                    <span class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ $inv->invoice_date ? $inv->invoice_date->format('d M Y') : 'N/A' }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    @if($inv->patient)
                                        <a href="{{ route('patients.show', $inv->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700 block">
                                            {{ $inv->patient->full_name }}
                                        </a>
                                        <span class="text-[11px] text-slate-400">
                                            {{ $inv->patient->patient_id }} • {{ $inv->patient->mobile }}
                                        </span>
                                    @else
                                        <span class="font-semibold text-slate-600">Walk-in Patient</span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <div class="font-semibold text-slate-800">{{ $clinicObj?->name ?? 'Main Clinic' }}</div>
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-bold border border-blue-100 inline-block mt-0.5">
                                        {{ $catObj?->name ?? 'Physiotherapy' }}
                                    </span>
                                </td>
                                <td class="p-3.5 font-extrabold text-slate-900">
                                    ₹{{ number_format((float)$inv->total_amount, 2) }}
                                </td>
                                <td class="p-3.5 font-extrabold text-emerald-700">
                                    ₹{{ number_format((float)$inv->paid_amount, 2) }}
                                </td>
                                <td class="p-3.5 font-extrabold {{ $inv->due_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                    ₹{{ number_format((float)$inv->due_amount, 2) }}
                                </td>
                                <td class="p-3.5">
                                    @if($inv->payment_status === 'paid' || $inv->due_amount <= 0)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> PAID
                                        </span>
                                    @elseif($inv->payment_status === 'partially_paid')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> PARTIAL DUE
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> UNPAID
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ $inv->payment_method ?: 'Pending' }}
                                    </span>
                                </td>
                                <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-1.5">
                                        @if($inv->due_amount > 0)
                                            <button type="button"
                                                    onclick="openCollectPaymentModal({{ $inv->id }}, '{{ $inv->invoice_no }}', '{{ addslashes($inv->patient?->full_name ?? 'Patient') }}', {{ (float)$inv->due_amount }}, '{{ addslashes($clinicObj?->name ?? 'Main Clinic') }}', '{{ ($clinicObj && $clinicObj->qr_code) ? asset($clinicObj->qr_code) : '' }}')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition shadow-2xs">
                                                <i data-lucide="indian-rupee" class="w-3 h-3"></i>
                                                <span>Collect</span>
                                            </button>
                                        @endif
                                        <a href="{{ route('invoices.print', $inv->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[11px] font-semibold transition"
                                           title="Download / Print Invoice PDF">
                                            <i data-lucide="download" class="w-3 h-3 text-slate-500"></i>
                                            <span>Invoice</span>
                                        </a>
                                        <a href="{{ route('invoices.show', $inv->id) }}"
                                           class="p-1.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-slate-100 text-slate-600 transition"
                                           title="View Full Bill Details">
                                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-12 text-center text-slate-400">
                                    <i data-lucide="receipt" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                    <div class="font-bold text-sm text-slate-700">No Payment Records Match Your Filter</div>
                                    <p class="text-xs text-slate-400 mt-1 mb-3">Try selecting "All Payments" or resetting the filters above.</p>
                                    <a href="{{ route('payments.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold">
                                        Show All Payments
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($invoices->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $invoices->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- TAB 2: PATIENT-WISE PAID & UNPAID SUMMARY -->
    <div id="tabSecPatientWise" class="{{ $activeTab === 'patient_wise' ? 'block' : 'hidden' }}">
        <div class="card-custom bg-white overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                        <span>Patient-Wise Payment Summary (Paid &amp; Unpaid)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Consolidated total billed, paid amount, and pending due balance for each patient</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-3.5 pl-5">Patient Name &amp; ID</th>
                            <th class="p-3.5">Mobile</th>
                            <th class="p-3.5">Total Bills</th>
                            <th class="p-3.5">Total Billed (₹)</th>
                            <th class="p-3.5">Total Paid (₹)</th>
                            <th class="p-3.5">Unpaid / Due (₹)</th>
                            <th class="p-3.5">Payment Status</th>
                            <th class="p-3.5 pr-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($patientWiseSummary as $pRow)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-3.5 pl-5">
                                    <div class="font-bold text-slate-900">{{ $pRow['patient_name'] }}</div>
                                    <span class="text-[11px] font-mono text-slate-400">{{ $pRow['patient_code'] }}</span>
                                </td>
                                <td class="p-3.5 font-semibold text-slate-700">{{ $pRow['mobile'] }}</td>
                                <td class="p-3.5">
                                    <span class="px-2 py-0.5 rounded-lg font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        {{ $pRow['invoices_count'] }} Bills
                                    </span>
                                    <span class="text-[11px] text-slate-400 ml-1">
                                        ({{ $pRow['paid_invoices'] }} Paid / {{ $pRow['unpaid_invoices'] }} Due)
                                    </span>
                                </td>
                                <td class="p-3.5 font-extrabold text-slate-900">₹{{ number_format($pRow['total_billed'], 2) }}</td>
                                <td class="p-3.5 font-extrabold text-emerald-700">₹{{ number_format($pRow['total_paid'], 2) }}</td>
                                <td class="p-3.5 font-extrabold {{ $pRow['total_due'] > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                    ₹{{ number_format($pRow['total_due'], 2) }}
                                </td>
                                <td class="p-3.5">
                                    @if($pRow['status'] === 'paid')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">ALL PAID</span>
                                    @elseif($pRow['status'] === 'partially_paid')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">PARTIAL DUE</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">UNPAID</span>
                                    @endif
                                </td>
                                <td class="p-3.5 pr-5 text-right whitespace-nowrap">
                                    @if($pRow['patient_id'])
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('payments.index', array_merge(request()->query(), ['patient_id' => $pRow['patient_id'], 'tab' => 'all_payments'])) }}"
                                               class="px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-[11px] font-semibold transition flex items-center gap-1">
                                                <i data-lucide="filter" class="w-3 h-3 text-blue-600"></i>
                                                <span>Filter Bills</span>
                                            </a>
                                            <a href="{{ route('patients.show', $pRow['patient_id']) }}"
                                               class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-semibold transition flex items-center gap-1">
                                                <i data-lucide="user" class="w-3 h-3"></i>
                                                <span>Profile</span>
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-10 text-center text-slate-400">No patient records found for this filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: DETAILED REPORTS (CLINIC-WISE, CATEGORY-WISE, MONTH-WISE, DAY-WISE) -->
    <div id="tabSecReports" class="{{ $activeTab === 'reports' ? 'block' : 'hidden' }} space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Clinic-Wise Payment Report -->
            <div class="card-custom bg-white overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="building-2" class="w-4 h-4 text-blue-600"></i>
                        <span>Clinic-Wise Payment Report</span>
                    </h3>
                    <span class="text-[11px] font-semibold text-slate-400">{{ $clinicWiseSummary->count() }} Clinics</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-3 pl-5">Clinic Name</th>
                                <th class="p-3">Bills</th>
                                <th class="p-3">Total Billed</th>
                                <th class="p-3">Paid</th>
                                <th class="p-3 pr-5">Unpaid Due</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($clinicWiseSummary as $cRow)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="p-3 pl-5 font-bold text-slate-900">{{ $cRow['clinic_name'] }}</td>
                                    <td class="p-3 font-semibold text-slate-600">{{ $cRow['invoices_count'] }}</td>
                                    <td class="p-3 font-bold text-slate-800">₹{{ number_format($cRow['total_billed'], 0) }}</td>
                                    <td class="p-3 font-bold text-emerald-700">₹{{ number_format($cRow['total_paid'], 0) }}</td>
                                    <td class="p-3 pr-5 font-bold text-rose-600">₹{{ number_format($cRow['total_due'], 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-400">No clinic data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Category-Wise Payment Report -->
            <div class="card-custom bg-white overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-indigo-600"></i>
                        <span>Category-Wise Payment Report</span>
                    </h3>
                    <span class="text-[11px] font-semibold text-slate-400">{{ $categoryWiseSummary->count() }} Categories</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-3 pl-5">Treatment Category</th>
                                <th class="p-3">Bills</th>
                                <th class="p-3">Total Billed</th>
                                <th class="p-3">Paid</th>
                                <th class="p-3 pr-5">Unpaid Due</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($categoryWiseSummary as $catRow)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="p-3 pl-5 font-bold text-slate-900">{{ $catRow['category_name'] }}</td>
                                    <td class="p-3 font-semibold text-slate-600">{{ $catRow['invoices_count'] }}</td>
                                    <td class="p-3 font-bold text-slate-800">₹{{ number_format($catRow['total_billed'], 0) }}</td>
                                    <td class="p-3 font-bold text-emerald-700">₹{{ number_format($catRow['total_paid'], 0) }}</td>
                                    <td class="p-3 pr-5 font-bold text-rose-600">₹{{ number_format($catRow['total_due'], 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-400">No category data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Payment Report -->
            <div class="card-custom bg-white overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="calendar-days" class="w-4 h-4 text-emerald-600"></i>
                        <span>Monthly Payment Report (Month-Wise)</span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/70 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-3 pl-5">Month</th>
                                <th class="p-3">Bills</th>
                                <th class="p-3">Total Billed</th>
                                <th class="p-3">Paid</th>
                                <th class="p-3 pr-5">Unpaid Due</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($monthlyReport as $mRow)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="p-3 pl-5 font-bold text-slate-900">{{ $mRow['formatted_month'] }}</td>
                                    <td class="p-3 font-semibold text-slate-600">{{ $mRow['invoices_count'] }}</td>
                                    <td class="p-3 font-bold text-slate-800">₹{{ number_format($mRow['total_billed'], 0) }}</td>
                                    <td class="p-3 font-bold text-emerald-700">₹{{ number_format($mRow['total_paid'], 0) }}</td>
                                    <td class="p-3 pr-5 font-bold text-rose-600">₹{{ number_format($mRow['total_due'], 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-400">No monthly data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Daily Payment Report -->
            <div class="card-custom bg-white overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="calendar-clock" class="w-4 h-4 text-teal-600"></i>
                        <span>Daily Payment Report (Day-Wise)</span>
                    </h3>
                </div>
                <div class="overflow-x-auto max-h-96">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="sticky top-0 bg-slate-50">
                            <tr class="border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="p-3 pl-5">Date</th>
                                <th class="p-3">Bills</th>
                                <th class="p-3">Total Billed</th>
                                <th class="p-3">Paid</th>
                                <th class="p-3 pr-5">Unpaid Due</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($dailyReport as $dRow)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="p-3 pl-5 font-bold text-slate-900">{{ $dRow['formatted_date'] }}</td>
                                    <td class="p-3 font-semibold text-slate-600">{{ $dRow['invoices_count'] }}</td>
                                    <td class="p-3 font-bold text-slate-800">₹{{ number_format($dRow['total_billed'], 0) }}</td>
                                    <td class="p-3 font-bold text-emerald-700">₹{{ number_format($dRow['total_paid'], 0) }}</td>
                                    <td class="p-3 pr-5 font-bold text-rose-600">₹{{ number_format($dRow['total_due'], 0) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="p-8 text-center text-slate-400">No daily data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- MODAL: COLLECT DUE PAYMENT -->
<div id="collectPaymentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto mx-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/70">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="indian-rupee" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Record / Collect Due Payment</h3>
                    <p class="text-[11px] text-slate-500">Update invoice balance &amp; generate receipt</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('collectPaymentModal')" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form method="POST" id="collectPaymentForm" action="" class="p-6 space-y-4 text-xs">
            @csrf
            <div class="p-3.5 rounded-xl bg-emerald-50/80 border border-emerald-200 space-y-1">
                <div class="flex items-center justify-between">
                    <div class="text-[11px] text-emerald-800">Invoice: <strong id="collectInvNo" class="font-mono">-</strong></div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-white text-blue-700 font-bold text-[10px] border border-emerald-200">
                        <i data-lucide="building-2" class="w-3 h-3"></i>
                        <span id="collectClinicName">Clinic</span>
                    </span>
                </div>
                <div class="text-sm font-black text-slate-900" id="collectPatientName">-</div>
                <div class="text-xs font-extrabold text-rose-600">Pending Balance: ₹<span id="collectDueDisplay">0.00</span></div>
            </div>

            <!-- CLINIC QR CODE BOX -->
            <div id="collectModalQrBox" class="hidden p-3.5 rounded-xl bg-slate-50 border border-emerald-200 text-center space-y-2">
                <div class="flex items-center justify-center gap-1.5 text-emerald-800 font-bold text-xs">
                    <i data-lucide="qr-code" class="w-4 h-4 text-emerald-600"></i>
                    <span>Scan Clinic QR to Pay (<span id="collectModalQrClinicLabel">Clinic</span>)</span>
                </div>
                <div class="bg-white p-2.5 rounded-xl border border-emerald-200 inline-block shadow-xs">
                    <img id="collectModalQrImg" src="" alt="Clinic Payment QR" class="w-44 h-44 object-contain mx-auto">
                </div>
                <p class="text-[11px] text-emerald-700 font-semibold">Scan via GPay, PhonePe, Paytm or any UPI App</p>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Amount Received (₹) <span class="text-rose-500">*</span></label>
                <input type="number" step="0.01" min="1" name="amount" id="collectAmountInput" required
                       class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-emerald-500 outline-none font-bold text-sm text-slate-900">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Payment Mode <span class="text-rose-500">*</span></label>
                <select name="payment_method" required
                        class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-emerald-500 outline-none font-semibold text-slate-800">
                    <option value="Cash">Cash</option>
                    <option value="UPI">UPI / GPay / PhonePe</option>
                    <option value="Card">Debit / Credit Card</option>
                    <option value="Bank Transfer">Bank Transfer / NEFT</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Reference / Note (Optional)</label>
                <input type="text" name="reference_no" placeholder="e.g. UPI Ref No or Cash Receipt note"
                       class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white focus:border-emerald-500 outline-none font-semibold text-slate-800">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="closeModal('collectPaymentModal')" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition shadow-xs flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Confirm Payment</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function selectPeriodOption(period) {
    document.getElementById('periodField').value = period;
    const customWrap = document.getElementById('customPeriodInputs');
    const wDate = document.getElementById('wrapCustomDate');
    const wMonth = document.getElementById('wrapCustomMonth');
    const wYear = document.getElementById('wrapCustomYear');
    const wRange = document.getElementById('wrapCustomRange');

    wDate.classList.add('hidden');
    wDate.classList.remove('block');
    wMonth.classList.add('hidden');
    wMonth.classList.remove('block');
    wYear.classList.add('hidden');
    wYear.classList.remove('block');
    wRange.classList.add('hidden');
    wRange.classList.remove('flex');

    if (['custom_date', 'custom_month', 'custom_year', 'custom_range'].includes(period)) {
        customWrap.classList.remove('hidden');
        customWrap.classList.add('flex');
        if (period === 'custom_date') { wDate.classList.remove('hidden'); wDate.classList.add('block'); }
        if (period === 'custom_month') { wMonth.classList.remove('hidden'); wMonth.classList.add('block'); }
        if (period === 'custom_year') { wYear.classList.remove('hidden'); wYear.classList.add('block'); }
        if (period === 'custom_range') { wRange.classList.remove('hidden'); wRange.classList.add('flex'); }
    } else {
        customWrap.classList.add('hidden');
        customWrap.classList.remove('flex');
        document.getElementById('paymentFilterForm').submit();
    }
}

function switchPaymentTab(tab) {
    document.getElementById('activeTabInput').value = tab;

    const secAll = document.getElementById('tabSecAllPayments');
    const secPatient = document.getElementById('tabSecPatientWise');
    const secReports = document.getElementById('tabSecReports');

    secAll.classList.toggle('hidden', tab !== 'all_payments');
    secAll.classList.toggle('block', tab === 'all_payments');

    secPatient.classList.toggle('hidden', tab !== 'patient_wise');
    secPatient.classList.toggle('block', tab === 'patient_wise');

    secReports.classList.toggle('hidden', tab !== 'reports');
    secReports.classList.toggle('block', tab === 'reports');

    const activeCls = 'flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition bg-navy-900 text-white shadow-xs';
    const inactiveCls = 'flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold transition text-slate-600 hover:text-slate-900 hover:bg-slate-50';

    document.getElementById('btnTabAll').className = (tab === 'all_payments') ? activeCls : inactiveCls;
    document.getElementById('btnTabPatient').className = (tab === 'patient_wise') ? activeCls : inactiveCls;
    document.getElementById('btnTabReports').className = (tab === 'reports') ? activeCls : inactiveCls;
}

function openCollectPaymentModal(invoiceId, invoiceNo, patientName, dueAmount, clinicName = 'Main Clinic', qrUrl = '') {
    const form = document.getElementById('collectPaymentForm');
    form.action = "{{ url('invoices') }}/" + invoiceId + "/record-payment";
    document.getElementById('collectInvNo').textContent = invoiceNo;
    document.getElementById('collectPatientName').textContent = patientName;
    document.getElementById('collectClinicName').textContent = clinicName || 'Main Clinic';
    document.getElementById('collectDueDisplay').textContent = dueAmount.toFixed(2);
    const amtInput = document.getElementById('collectAmountInput');
    amtInput.max = dueAmount;
    amtInput.value = dueAmount;

    const qrBox = document.getElementById('collectModalQrBox');
    const qrImg = document.getElementById('collectModalQrImg');
    const qrLabel = document.getElementById('collectModalQrClinicLabel');
    if (qrUrl && qrUrl.trim() !== '') {
        qrImg.src = qrUrl;
        qrLabel.textContent = clinicName || 'Clinic';
        qrBox.classList.remove('hidden');
    } else {
        qrImg.src = '';
        qrBox.classList.add('hidden');
    }

    openModal('collectPaymentModal');
}
</script>
@endpush
@endsection
