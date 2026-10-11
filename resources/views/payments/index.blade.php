@extends('layouts.app')

@section('title', 'Payment Manage & Reports')
@section('breadcrumb', 'Finance / Payment Manage & Reports')

@section('content')
<div class="page-header" style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; margin-bottom:20px;">
    <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:10px;">
            <i class="fa-solid fa-indian-rupee-sign" style="color:var(--primary);"></i>
            Payment Manage & Complete Reports
        </h1>
        <p class="page-subtitle">
            Showing: <strong style="color:var(--primary);">{{ $periodLabel }}</strong> — All Patient-wise Paid, Unpaid & Due Payments with Day, Week, Month, Year, Clinic & Category filters.
        </p>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('payments.report.print', request()->query()) }}" target="_blank" class="btn btn-outline" style="border-color:#0d9488; color:#0f766e; font-weight:700;">
            <i class="fa-solid fa-file-pdf"></i> Print / Download Report PDF
        </a>
        <a href="{{ route('payments.export', request()->query()) }}" class="btn btn-outline" style="font-weight:700;">
            <i class="fa-solid fa-file-csv"></i> Export Excel / CSV
        </a>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary" style="font-weight:700;">
            <i class="fa-solid fa-plus"></i> + Create Invoice
        </a>
    </div>
</div>

<!-- Quick Day / Week / Month / Year Snapshot Cards -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(235px, 1fr)); gap:14px; margin-bottom:20px;">
    <!-- Today -->
    <a href="{{ route('payments.index', array_merge(request()->except(['page', 'date', 'month', 'year', 'start_date', 'end_date']), ['period' => 'today'])) }}"
       style="text-decoration:none; padding:16px 18px; border-radius:14px; background:{{ $period === 'today' ? 'linear-gradient(135deg,#0f766e,#0d9488)' : '#ffffff' }}; color:{{ $period === 'today' ? '#ffffff' : 'var(--text-primary)' }}; border:2px solid {{ $period === 'today' ? '#0f766e' : 'var(--border)' }}; box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:all 0.2s;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; opacity:0.85;">
                <i class="fa-solid fa-calendar-day"></i> Today (Day Report)
            </span>
            <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:20px; background:{{ $period === 'today' ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }};">
                {{ $periodSnapshots['today']['count'] }} Bills
            </span>
        </div>
        <div style="font-size:21px; font-weight:800; margin-bottom:6px;">
            ₹{{ number_format($periodSnapshots['today']['paid'], 0) }}
            <span style="font-size:11.5px; font-weight:600; opacity:0.8;">Paid</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:12px; opacity:0.9;">
            <span>Billed: ₹{{ number_format($periodSnapshots['today']['billed'], 0) }}</span>
            <span style="color:{{ $period === 'today' ? '#fde68a' : '#dc2626' }}; font-weight:700;">Due: ₹{{ number_format($periodSnapshots['today']['due'], 0) }}</span>
        </div>
    </a>

    <!-- This Week -->
    <a href="{{ route('payments.index', array_merge(request()->except(['page', 'date', 'month', 'year', 'start_date', 'end_date']), ['period' => 'this_week'])) }}"
       style="text-decoration:none; padding:16px 18px; border-radius:14px; background:{{ $period === 'this_week' ? 'linear-gradient(135deg,#0284c7,#0369a1)' : '#ffffff' }}; color:{{ $period === 'this_week' ? '#ffffff' : 'var(--text-primary)' }}; border:2px solid {{ $period === 'this_week' ? '#0284c7' : 'var(--border)' }}; box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:all 0.2s;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; opacity:0.85;">
                <i class="fa-solid fa-calendar-week"></i> This Week Report
            </span>
            <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:20px; background:{{ $period === 'this_week' ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }};">
                {{ $periodSnapshots['week']['count'] }} Bills
            </span>
        </div>
        <div style="font-size:21px; font-weight:800; margin-bottom:6px;">
            ₹{{ number_format($periodSnapshots['week']['paid'], 0) }}
            <span style="font-size:11.5px; font-weight:600; opacity:0.8;">Paid</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:12px; opacity:0.9;">
            <span>Billed: ₹{{ number_format($periodSnapshots['week']['billed'], 0) }}</span>
            <span style="color:{{ $period === 'this_week' ? '#fde68a' : '#dc2626' }}; font-weight:700;">Due: ₹{{ number_format($periodSnapshots['week']['due'], 0) }}</span>
        </div>
    </a>

    <!-- This Month -->
    <a href="{{ route('payments.index', array_merge(request()->except(['page', 'date', 'month', 'year', 'start_date', 'end_date']), ['period' => 'this_month'])) }}"
       style="text-decoration:none; padding:16px 18px; border-radius:14px; background:{{ $period === 'this_month' ? 'linear-gradient(135deg,#4f46e5,#4338ca)' : '#ffffff' }}; color:{{ $period === 'this_month' ? '#ffffff' : 'var(--text-primary)' }}; border:2px solid {{ $period === 'this_month' ? '#4f46e5' : 'var(--border)' }}; box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:all 0.2s;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; opacity:0.85;">
                <i class="fa-solid fa-calendar-days"></i> Monthly ({{ now()->format('M Y') }})
            </span>
            <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:20px; background:{{ $period === 'this_month' ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }};">
                {{ $periodSnapshots['month']['count'] }} Bills
            </span>
        </div>
        <div style="font-size:21px; font-weight:800; margin-bottom:6px;">
            ₹{{ number_format($periodSnapshots['month']['paid'], 0) }}
            <span style="font-size:11.5px; font-weight:600; opacity:0.8;">Paid</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:12px; opacity:0.9;">
            <span>Billed: ₹{{ number_format($periodSnapshots['month']['billed'], 0) }}</span>
            <span style="color:{{ $period === 'this_month' ? '#fde68a' : '#dc2626' }}; font-weight:700;">Due: ₹{{ number_format($periodSnapshots['month']['due'], 0) }}</span>
        </div>
    </a>

    <!-- This Year -->
    <a href="{{ route('payments.index', array_merge(request()->except(['page', 'date', 'month', 'year', 'start_date', 'end_date']), ['period' => 'this_year'])) }}"
       style="text-decoration:none; padding:16px 18px; border-radius:14px; background:{{ $period === 'this_year' ? 'linear-gradient(135deg,#0f172a,#1e293b)' : '#ffffff' }}; color:{{ $period === 'this_year' ? '#ffffff' : 'var(--text-primary)' }}; border:2px solid {{ $period === 'this_year' ? '#0f172a' : 'var(--border)' }}; box-shadow:0 2px 8px rgba(0,0,0,0.04); transition:all 0.2s;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <span style="font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; opacity:0.85;">
                <i class="fa-solid fa-calendar"></i> Yearly ({{ now()->format('Y') }})
            </span>
            <span style="font-size:11px; font-weight:700; padding:2px 8px; border-radius:20px; background:{{ $period === 'this_year' ? 'rgba(255,255,255,0.2)' : '#f1f5f9' }};">
                {{ $periodSnapshots['year']['count'] }} Bills
            </span>
        </div>
        <div style="font-size:21px; font-weight:800; margin-bottom:6px;">
            ₹{{ number_format($periodSnapshots['year']['paid'], 0) }}
            <span style="font-size:11.5px; font-weight:600; opacity:0.8;">Paid</span>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:12px; opacity:0.9;">
            <span>Billed: ₹{{ number_format($periodSnapshots['year']['billed'], 0) }}</span>
            <span style="color:{{ $period === 'this_year' ? '#fde68a' : '#dc2626' }}; font-weight:700;">Due: ₹{{ number_format($periodSnapshots['year']['due'], 0) }}</span>
        </div>
    </a>
</div>

<!-- ADVANCED MULTI-FILTER PANEL -->
<div class="card" style="margin-bottom:20px; border:1px solid #cbd5e1;">
    <div class="card-body" style="padding:18px 20px;">
        <form method="GET" action="{{ route('payments.index') }}" id="paymentFilterForm">
            <input type="hidden" name="tab" id="activeTabInput" value="{{ $activeTab }}">

            <!-- Quick Period Selector Pills -->
            <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center; margin-bottom:14px; padding-bottom:12px; border-bottom:1px dashed var(--border);">
                <span style="font-size:12px; font-weight:700; color:var(--text-secondary); margin-right:4px;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Period:
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
                        'custom_range' => 'Custom Date Range',
                    ];
                @endphp
                @foreach($periodOptions as $pKey => $pText)
                    <button type="button" onclick="selectPeriodOption('{{ $pKey }}')"
                        class="btn btn-sm {{ $period === $pKey ? 'btn-primary' : 'btn-outline' }}"
                        style="padding:5px 11px; font-size:12px; border-radius:20px;">
                        {{ $pText }}
                    </button>
                @endforeach
                <input type="hidden" name="period" id="periodField" value="{{ $period }}">
            </div>

            <!-- Dynamic Date / Month / Year / Range Inputs -->
            <div id="customPeriodInputs" style="display:{{ in_array($period, ['custom_date', 'custom_month', 'custom_year', 'custom_range']) ? 'flex' : 'none' }}; gap:12px; flex-wrap:wrap; align-items:flex-end; margin-bottom:14px; padding:12px 14px; background:#f8fafc; border-radius:10px; border:1px solid var(--border);">
                <div id="wrapCustomDate" style="display:{{ $period === 'custom_date' ? 'block' : 'none' }};">
                    <label class="form-label" style="font-size:12px;">Select Specific Day</label>
                    <input type="date" name="date" value="{{ $date ?: now()->toDateString() }}" class="form-control">
                </div>

                <div id="wrapCustomMonth" style="display:{{ $period === 'custom_month' ? 'block' : 'none' }};">
                    <label class="form-label" style="font-size:12px;">Select Month & Year</label>
                    <input type="month" name="month" value="{{ $month ?: now()->format('Y-m') }}" class="form-control">
                </div>

                <div id="wrapCustomYear" style="display:{{ $period === 'custom_year' ? 'block' : 'none' }};">
                    <label class="form-label" style="font-size:12px;">Select Year</label>
                    <select name="year" class="form-control" style="min-width:130px;">
                        @for($y = (int)now()->format('Y') + 1; $y >= 2022; $y--)
                            <option value="{{ $y }}" {{ (string)$year === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                <div id="wrapCustomRange" style="display:{{ $period === 'custom_range' ? 'flex' : 'none' }}; gap:10px; flex-wrap:wrap;">
                    <div>
                        <label class="form-label" style="font-size:12px;">From Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                    </div>
                    <div>
                        <label class="form-label" style="font-size:12px;">To Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-sm" style="height:38px;">
                    <i class="fa-solid fa-check"></i> Apply Date Filter
                </button>
            </div>

            <!-- Main Filter Dropdowns: Patient, Clinic, Category, Status, Method, Search -->
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(175px, 1fr)); gap:12px; align-items:end;">
                <div>
                    <label class="form-label" style="font-size:11.5px; margin-bottom:4px;">Patient-Wise Filter</label>
                    <select name="patient_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Patients</option>
                        @foreach($patients as $pt)
                            <option value="{{ $pt->id }}" {{ (string)$patientId === (string)$pt->id ? 'selected' : '' }}>
                                {{ $pt->full_name }} ({{ $pt->patient_id }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size:11.5px; margin-bottom:4px;">Clinic-Wise Filter</label>
                    <select name="clinic_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Clinics</option>
                        @foreach($clinics as $cl)
                            <option value="{{ $cl->id }}" {{ (string)$clinicId === (string)$cl->id ? 'selected' : '' }}>
                                {{ $cl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size:11.5px; margin-bottom:4px;">Category-Wise Filter</label>
                    <select name="category_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size:11.5px; margin-bottom:4px;">Paid / Unpaid Status</label>
                    <select name="payment_status" class="form-control" onchange="this.form.submit()">
                        <option value="all" {{ $paymentStatus === 'all' ? 'selected' : '' }}>All (Paid + Unpaid)</option>
                        <option value="paid" {{ $paymentStatus === 'paid' ? 'selected' : '' }}>Paid Only</option>
                        <option value="unpaid" {{ $paymentStatus === 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                        <option value="partially_paid" {{ $paymentStatus === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="due" {{ $paymentStatus === 'due' ? 'selected' : '' }}>All Pending Dues (Unpaid + Partial)</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size:11.5px; margin-bottom:4px;">Payment Mode</label>
                    <select name="method" class="form-control" onchange="this.form.submit()">
                        <option value="">All Modes</option>
                        <option value="Cash" {{ $method === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="UPI" {{ $method === 'UPI' ? 'selected' : '' }}>UPI / GPay / PhonePe</option>
                        <option value="Card" {{ $method === 'Card' ? 'selected' : '' }}>Card</option>
                        <option value="Bank Transfer" {{ $method === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" style="font-size:11.5px; margin-bottom:4px;">Search Patient / Invoice</label>
                    <div style="display:flex; gap:6px;">
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Name, ID, Mobile, INV...">
                        <button type="submit" class="btn btn-primary" title="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                        <a href="{{ route('payments.index') }}" class="btn btn-outline" title="Reset All Filters"><i class="fa-solid fa-rotate-left"></i></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- FILTERED KPI TOTALS -->
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 22px;">
    <div class="kpi-card">
        <div class="kpi-icon blue"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        <div class="kpi-info">
            <div class="kpi-label">Total Billed ({{ $periodLabel }})</div>
            <div class="kpi-value">₹{{ number_format($totalBilled, 2) }}</div>
            <div class="kpi-change up"><i class="fa-solid fa-receipt"></i> {{ $allFilteredInvoices->count() }} Total Invoices</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon green"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-info">
            <div class="kpi-label">Total Paid / Collected</div>
            <div class="kpi-value" style="color:#16a34a;">₹{{ number_format($totalCollected, 2) }}</div>
            <div class="kpi-change up"><i class="fa-solid fa-check-double"></i> {{ $paidCount }} Fully Paid • {{ $partialCount }} Partial</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon red" style="background:#fee2e2; color:#dc2626;"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div class="kpi-info">
            <div class="kpi-label">Total Unpaid / Pending Due</div>
            <div class="kpi-value" style="color:#dc2626;">₹{{ number_format($totalDue, 2) }}</div>
            <div class="kpi-change down"><i class="fa-solid fa-clock"></i> {{ $unpaidCount }} Unpaid • {{ $partialCount }} Partial Due</div>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-icon teal"><i class="fa-solid fa-wallet"></i></div>
        <div class="kpi-info">
            <div class="kpi-label">Mode Breakdown</div>
            <div style="font-size:12.5px; font-weight:700; color:var(--text-primary); margin-top:4px; line-height:1.5;">
                Cash: ₹{{ number_format($cashTotal, 0) }} • UPI: ₹{{ number_format($upiTotal, 0) }}<br>
                Card: ₹{{ number_format($cardTotal, 0) }} • Bank: ₹{{ number_format($bankTotal, 0) }}
            </div>
        </div>
    </div>
</div>

<!-- VIEW MODE TABS -->
<div style="display:flex; gap:10px; margin-bottom:18px; border-bottom:2px solid var(--border); padding-bottom:12px; flex-wrap:wrap;">
    <button type="button" id="btnTabAll" onclick="switchPaymentTab('all_payments')"
        class="btn {{ $activeTab === 'all_payments' ? 'btn-primary' : 'btn-outline' }}" style="font-weight:700; border-radius:10px;">
        <i class="fa-solid fa-list-check"></i> All Payments — Paid & Unpaid ({{ $allFilteredInvoices->count() }})
    </button>
    <button type="button" id="btnTabPatient" onclick="switchPaymentTab('patient_wise')"
        class="btn {{ $activeTab === 'patient_wise' ? 'btn-primary' : 'btn-outline' }}" style="font-weight:700; border-radius:10px;">
        <i class="fa-solid fa-hospital-user"></i> Patient-Wise Summary ({{ $patientWiseSummary->count() }} Patients)
    </button>
    <button type="button" id="btnTabReports" onclick="switchPaymentTab('reports')"
        class="btn {{ $activeTab === 'reports' ? 'btn-primary' : 'btn-outline' }}" style="font-weight:700; border-radius:10px;">
        <i class="fa-solid fa-chart-pie"></i> Detailed Reports (Day / Month / Clinic / Category)
    </button>
</div>

<!-- TAB 1: ALL PAYMENTS (PAID, UNPAID, PARTIALLY PAID) -->
<div id="tabSecAllPayments" style="display:{{ $activeTab === 'all_payments' ? 'block' : 'none' }};">
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
            <div>
                <span class="card-title"><i class="fa-solid fa-file-invoice-dollar" style="color:var(--primary); margin-right:8px;"></i>All Patient Invoices & Payment Records</span>
                <p style="margin:2px 0 0; font-size:12px; color:var(--text-muted);">View Paid, Unpaid & Partially Paid bills, collect pending dues, or download/print any invoice.</p>
            </div>
            <div style="display:flex; gap:8px;">
                <span class="badge badge-completed" style="padding:6px 10px;">Paid: ₹{{ number_format($totalCollected, 0) }}</span>
                <span class="badge badge-cancelled" style="padding:6px 10px;">Unpaid Due: ₹{{ number_format($totalDue, 0) }}</span>
            </div>
        </div>
        <div class="table-container" style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice No & Date</th>
                        <th>Patient Details</th>
                        <th>Clinic & Category</th>
                        <th>Total Bill</th>
                        <th>Paid Amount</th>
                        <th>Unpaid / Due</th>
                        <th>Status</th>
                        <th>Mode</th>
                        <th style="text-align:right;">Invoice & Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                        @php
                            $clinicObj = $inv->appointment?->clinic ?? $inv->appointments->first()?->clinic;
                            $catObj = $inv->appointment?->category ?? $inv->appointments->first()?->category;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('invoices.show', $inv->id) }}" style="font-weight:700; color:var(--primary); text-decoration:none;">
                                    {{ $inv->invoice_no }}
                                </a>
                                <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                                    <i class="fa-regular fa-calendar"></i> {{ $inv->invoice_date ? $inv->invoice_date->format('d M Y') : 'N/A' }}
                                </div>
                            </td>
                            <td>
                                @if($inv->patient)
                                    <a href="{{ route('patients.show', $inv->patient_id) }}" style="font-weight:700; color:var(--text-primary); text-decoration:none;">
                                        {{ $inv->patient->full_name }}
                                    </a>
                                    <div style="font-size:11.5px; color:var(--text-muted);">
                                        {{ $inv->patient->patient_id }} • <i class="fa-solid fa-phone" style="font-size:10px;"></i> {{ $inv->patient->mobile }}
                                    </div>
                                @else
                                    <span style="font-weight:600;">Walk-in Patient</span>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:12.5px; font-weight:600; color:var(--text-primary);">
                                    <i class="fa-solid fa-hospital" style="color:var(--primary); margin-right:4px;"></i>{{ $clinicObj?->name ?? 'Main Clinic' }}
                                </div>
                                <div style="font-size:11.5px; color:var(--text-muted); margin-top:2px;">
                                    <i class="fa-solid fa-Notes-medical"></i> {{ $catObj?->name ?? 'Consultation / Physio' }}
                                </div>
                            </td>
                            <td style="font-weight:700; font-size:14px;">
                                ₹{{ number_format((float)$inv->total_amount, 2) }}
                            </td>
                            <td style="font-weight:700; font-size:14px; color:#16a34a;">
                                ₹{{ number_format((float)$inv->paid_amount, 2) }}
                            </td>
                            <td style="font-weight:700; font-size:14px; color:{{ $inv->due_amount > 0 ? '#dc2626' : '#64748b' }};">
                                ₹{{ number_format((float)$inv->due_amount, 2) }}
                            </td>
                            <td>
                                @if($inv->payment_status === 'paid' || $inv->due_amount <= 0)
                                    <span class="badge badge-completed" style="background:#dcfce7; color:#15803d; font-weight:700;">
                                        <i class="fa-solid fa-circle-check"></i> PAID
                                    </span>
                                @elseif($inv->payment_status === 'partially_paid')
                                    <span class="badge badge-waiting" style="background:#fef3c7; color:#b45309; font-weight:700;">
                                        <i class="fa-solid fa-clock"></i> PARTIAL DUE
                                    </span>
                                @else
                                    <span class="badge badge-cancelled" style="background:#fee2e2; color:#b91c1c; font-weight:700;">
                                        <i class="fa-solid fa-circle-exclamation"></i> UNPAID
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-confirmed" style="font-size:11.5px;">
                                    {{ $inv->payment_method ?: 'Pending' }}
                                </span>
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <div style="display:inline-flex; gap:6px; align-items:center;">
                                    @if($inv->due_amount > 0)
                                        <button type="button" class="btn btn-primary btn-sm"
                                            onclick="openCollectPaymentModal({{ $inv->id }}, '{{ $inv->invoice_no }}', '{{ addslashes($inv->patient?->full_name ?? 'Patient') }}', {{ (float)$inv->due_amount }})"
                                            style="background:#16a34a; border-color:#16a34a; padding:5px 10px; font-size:11.5px;">
                                            <i class="fa-solid fa-indian-rupee-sign"></i> Collect
                                        </button>
                                    @endif
                                    <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" class="btn btn-outline btn-sm" title="Download / Print Invoice PDF" style="padding:5px 10px; font-size:11.5px; font-weight:600;">
                                        <i class="fa-solid fa-download"></i> Invoice
                                    </a>
                                    <a href="{{ route('invoices.show', $inv->id) }}" class="btn btn-outline btn-sm" title="View Full Bill Details" style="padding:5px 9px;">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding:45px 20px; color:var(--text-muted);">
                                <i class="fa-solid fa-file-invoice-dollar" style="font-size:32px; margin-bottom:10px; opacity:0.4; display:block;"></i>
                                <div style="font-weight:700; font-size:15px; color:var(--text-primary);">No Payment Records Match Your Filter</div>
                                <p style="font-size:12.5px; margin:4px 0 12px;">Try selecting "All Payments" or resetting the filters above.</p>
                                <a href="{{ route('payments.index') }}" class="btn btn-outline btn-sm">Show All Payments</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div style="padding: 16px 20px; border-top: 1px solid var(--border);">
                {{ $invoices->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

<!-- TAB 2: PATIENT-WISE PAID & UNPAID SUMMARY -->
<div id="tabSecPatientWise" style="display:{{ $activeTab === 'patient_wise' ? 'block' : 'none' }};">
    <div class="card">
        <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <span class="card-title"><i class="fa-solid fa-hospital-user" style="color:var(--primary); margin-right:8px;"></i>Patient-Wise Payment Summary (Paid & Unpaid)</span>
                <p style="margin:2px 0 0; font-size:12px; color:var(--text-muted);">Consolidated billing, paid amount, and pending balance for each patient.</p>
            </div>
        </div>
        <div class="table-container" style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Patient Name & ID</th>
                        <th>Mobile</th>
                        <th>Total Bills</th>
                        <th>Total Billed (₹)</th>
                        <th>Total Paid (₹)</th>
                        <th>Unpaid / Due (₹)</th>
                        <th>Payment Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patientWiseSummary as $pRow)
                        <tr>
                            <td>
                                <div style="font-weight:700; color:var(--text-primary);">{{ $pRow['patient_name'] }}</div>
                                <div style="font-size:11.5px; color:var(--text-muted);">{{ $pRow['patient_code'] }}</div>
                            </td>
                            <td>{{ $pRow['mobile'] }}</td>
                            <td>
                                <span class="badge badge-confirmed">{{ $pRow['invoices_count'] }} Bills</span>
                                <span style="font-size:11px; color:var(--text-muted); margin-left:4px;">
                                    ({{ $pRow['paid_invoices'] }} Paid / {{ $pRow['unpaid_invoices'] }} Due)
                                </span>
                            </td>
                            <td style="font-weight:700;">₹{{ number_format($pRow['total_billed'], 2) }}</td>
                            <td style="font-weight:700; color:#16a34a;">₹{{ number_format($pRow['total_paid'], 2) }}</td>
                            <td style="font-weight:700; color:{{ $pRow['total_due'] > 0 ? '#dc2626' : '#64748b' }};">
                                ₹{{ number_format($pRow['total_due'], 2) }}
                            </td>
                            <td>
                                @if($pRow['status'] === 'paid')
                                    <span class="badge badge-completed">ALL PAID</span>
                                @elseif($pRow['status'] === 'partially_paid')
                                    <span class="badge badge-waiting">PARTIAL DUE</span>
                                @else
                                    <span class="badge badge-cancelled">UNPAID</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                @if($pRow['patient_id'])
                                    <a href="{{ route('payments.index', array_merge(request()->query(), ['patient_id' => $pRow['patient_id'], 'tab' => 'all_payments'])) }}" class="btn btn-outline btn-sm">
                                        <i class="fa-solid fa-filter"></i> Filter Bills
                                    </a>
                                    <a href="{{ route('patients.show', $pRow['patient_id']) }}" class="btn btn-outline btn-sm">
                                        <i class="fa-solid fa-user"></i> Profile
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center; padding:35px; color:var(--text-muted);">No patient records found for this filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 3: DETAILED REPORTS (CLINIC-WISE, CATEGORY-WISE, MONTH-WISE, DAY-WISE) -->
<div id="tabSecReports" style="display:{{ $activeTab === 'reports' ? 'block' : 'none' }};">
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(440px, 1fr)); gap:20px; margin-bottom:20px;">
        <!-- Clinic-Wise Payment Report -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-hospital" style="color:var(--primary); margin-right:8px;"></i>Clinic-Wise Payment Report</span>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Clinic Name</th>
                            <th>Bills</th>
                            <th>Total Billed</th>
                            <th>Paid</th>
                            <th>Unpaid Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clinicWiseSummary as $cRow)
                            <tr>
                                <td style="font-weight:700;">{{ $cRow['clinic_name'] }}</td>
                                <td>{{ $cRow['invoices_count'] }}</td>
                                <td style="font-weight:600;">₹{{ number_format($cRow['total_billed'], 0) }}</td>
                                <td style="font-weight:700; color:#16a34a;">₹{{ number_format($cRow['total_paid'], 0) }}</td>
                                <td style="font-weight:700; color:#dc2626;">₹{{ number_format($cRow['total_due'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; padding:20px;">No clinic data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Category-Wise Payment Report -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-layer-group" style="color:var(--primary); margin-right:8px;"></i>Category-Wise Payment Report</span>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Treatment Category</th>
                            <th>Bills</th>
                            <th>Total Billed</th>
                            <th>Paid</th>
                            <th>Unpaid Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryWiseSummary as $catRow)
                            <tr>
                                <td style="font-weight:700;">{{ $catRow['category_name'] }}</td>
                                <td>{{ $catRow['invoices_count'] }}</td>
                                <td style="font-weight:600;">₹{{ number_format($catRow['total_billed'], 0) }}</td>
                                <td style="font-weight:700; color:#16a34a;">₹{{ number_format($catRow['total_paid'], 0) }}</td>
                                <td style="font-weight:700; color:#dc2626;">₹{{ number_format($catRow['total_due'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; padding:20px;">No category data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(440px, 1fr)); gap:20px;">
        <!-- Monthly Payment Report -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-calendar-days" style="color:var(--primary); margin-right:8px;"></i>Monthly Payment Report (Month-Wise)</span>
            </div>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Bills</th>
                            <th>Total Billed</th>
                            <th>Paid</th>
                            <th>Unpaid Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyReport as $mRow)
                            <tr>
                                <td style="font-weight:700;">{{ $mRow['formatted_month'] }}</td>
                                <td>{{ $mRow['invoices_count'] }}</td>
                                <td style="font-weight:600;">₹{{ number_format($mRow['total_billed'], 0) }}</td>
                                <td style="font-weight:700; color:#16a34a;">₹{{ number_format($mRow['total_paid'], 0) }}</td>
                                <td style="font-weight:700; color:#dc2626;">₹{{ number_format($mRow['total_due'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; padding:20px;">No monthly data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Daily Payment Report -->
        <div class="card">
            <div class="card-header">
                <span class="card-title"><i class="fa-solid fa-calendar-day" style="color:var(--primary); margin-right:8px;"></i>Daily Payment Report (Day-Wise)</span>
            </div>
            <div class="table-container" style="max-height:420px; overflow-y:auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Bills</th>
                            <th>Total Billed</th>
                            <th>Paid</th>
                            <th>Unpaid Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dailyReport as $dRow)
                            <tr>
                                <td style="font-weight:700;">{{ $dRow['formatted_date'] }}</td>
                                <td>{{ $dRow['invoices_count'] }}</td>
                                <td style="font-weight:600;">₹{{ number_format($dRow['total_billed'], 0) }}</td>
                                <td style="font-weight:700; color:#16a34a;">₹{{ number_format($dRow['total_paid'], 0) }}</td>
                                <td style="font-weight:700; color:#dc2626;">₹{{ number_format($dRow['total_due'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center; padding:20px;">No daily data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: COLLECT DUE PAYMENT -->
<div class="modal-backdrop" id="collectPaymentModal">
    <div class="modal" style="max-width:460px;">
        <div class="modal-header">
            <span class="modal-title"><i class="fa-solid fa-hand-holding-dollar" style="color:#16a34a; margin-right:8px;"></i>Record / Collect Due Payment</span>
            <button type="button" class="modal-close" onclick="closeModal('collectPaymentModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" id="collectPaymentForm" action="">
            @csrf
            <div class="modal-body" style="display:grid; gap:14px;">
                <div style="padding:12px 14px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px;">
                    <div style="font-size:12px; color:#166534;">Invoice: <strong id="collectInvNo">-</strong></div>
                    <div style="font-size:14px; font-weight:700; color:#14532d; margin-top:2px;" id="collectPatientName">-</div>
                    <div style="font-size:13px; color:#dc2626; font-weight:700; margin-top:4px;">Pending Balance: ₹<span id="collectDueDisplay">0.00</span></div>
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">Amount Received (₹) <span style="color:#dc2626;">*</span></label>
                    <input type="number" step="0.01" min="1" name="amount" id="collectAmountInput" class="form-control" required>
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">Payment Mode <span style="color:#dc2626;">*</span></label>
                    <select name="payment_method" class="form-control" required>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI / GPay / PhonePe</option>
                        <option value="Card">Debit / Credit Card</option>
                        <option value="Bank Transfer">Bank Transfer / NEFT</option>
                    </select>
                </div>

                <div class="form-group" style="margin:0;">
                    <label class="form-label">Reference / Note (Optional)</label>
                    <input type="text" name="reference_no" class="form-control" placeholder="e.g. UPI Ref No or Cash Receipt note">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('collectPaymentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="background:#16a34a; border-color:#16a34a;">
                    <i class="fa-solid fa-check"></i> Confirm Payment
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

    wDate.style.display = 'none';
    wMonth.style.display = 'none';
    wYear.style.display = 'none';
    wRange.style.display = 'none';

    if (['custom_date', 'custom_month', 'custom_year', 'custom_range'].includes(period)) {
        customWrap.style.display = 'flex';
        if (period === 'custom_date') wDate.style.display = 'block';
        if (period === 'custom_month') wMonth.style.display = 'block';
        if (period === 'custom_year') wYear.style.display = 'block';
        if (period === 'custom_range') wRange.style.display = 'flex';
    } else {
        customWrap.style.display = 'none';
        document.getElementById('paymentFilterForm').submit();
    }
}

function switchPaymentTab(tab) {
    document.getElementById('activeTabInput').value = tab;
    document.getElementById('tabSecAllPayments').style.display = (tab === 'all_payments') ? 'block' : 'none';
    document.getElementById('tabSecPatientWise').style.display = (tab === 'patient_wise') ? 'block' : 'none';
    document.getElementById('tabSecReports').style.display = (tab === 'reports') ? 'block' : 'none';

    document.getElementById('btnTabAll').className = 'btn ' + (tab === 'all_payments' ? 'btn-primary' : 'btn-outline');
    document.getElementById('btnTabPatient').className = 'btn ' + (tab === 'patient_wise' ? 'btn-primary' : 'btn-outline');
    document.getElementById('btnTabReports').className = 'btn ' + (tab === 'reports' ? 'btn-primary' : 'btn-outline');
}

function openCollectPaymentModal(invoiceId, invoiceNo, patientName, dueAmount) {
    const form = document.getElementById('collectPaymentForm');
    form.action = "{{ url('invoices') }}/" + invoiceId + "/record-payment";
    document.getElementById('collectInvNo').textContent = invoiceNo;
    document.getElementById('collectPatientName').textContent = patientName;
    document.getElementById('collectDueDisplay').textContent = dueAmount.toFixed(2);
    const amtInput = document.getElementById('collectAmountInput');
    amtInput.max = dueAmount;
    amtInput.value = dueAmount;
    openModal('collectPaymentModal');
}
</script>
@endpush
@endsection
