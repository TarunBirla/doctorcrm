@extends('layouts.app')

@section('title', 'Financial Reports & Profit Summary')
@section('breadcrumb', 'Analytics / Financial Reports')
@section('page_title', 'Financial Performance & Revenue Audit')

@section('content')
<div class="space-y-6">

    <!-- DATE FILTER & EXPORT BAR -->
    <div class="card-custom p-5 bg-white flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('reports.financial') }}" method="GET" class="flex flex-wrap items-center gap-2 text-xs">
            <span class="font-bold text-slate-700">Reporting Range:</span>
            <select name="range" onchange="this.form.submit()" class="px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 font-semibold outline-none">
                <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Today</option>
                <option value="yesterday" {{ $range === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                <option value="this_week" {{ $range === 'this_week' ? 'selected' : '' }}>This Week</option>
                <option value="last_week" {{ $range === 'last_week' ? 'selected' : '' }}>Last Week</option>
                <option value="this_month" {{ $range === 'this_month' ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ $range === 'last_month' ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ $range === 'this_year' ? 'selected' : '' }}>This Year</option>
                <option value="custom" {{ $range === 'custom' ? 'selected' : '' }}>Custom Date Range</option>
            </select>

            @if($range === 'custom')
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-2 py-1.5 border rounded-xl bg-slate-50">
                <span>to</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-2 py-1.5 border rounded-xl bg-slate-50">
                <button type="submit" class="px-3 py-1.5 bg-navy-900 text-white font-bold rounded-xl">Apply</button>
            @endif
        </form>

        <a href="{{ route('reports.financial.export', ['range' => $range]) }}" class="px-4 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 shadow-2xs">
            <i data-lucide="download" class="w-3.5 h-3.5 text-slate-400"></i>
            <span>Export Transactions (CSV)</span>
        </a>
    </div>

    <!-- 4 FINANCIAL STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Billed Fees</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($totalBilled, 2) }}</div>
            <span class="text-xs text-slate-400 mt-1 block">Invoiced in period</span>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Gross Collections</span>
            <div class="text-3xl font-extrabold text-emerald-700 tracking-tight mt-1">₹{{ number_format($totalCollected, 2) }}</div>
            <span class="text-xs text-emerald-600 mt-1 block">Actual cash/digital realized</span>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Total Disbursements</span>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">₹{{ number_format($totalExpenses, 2) }}</div>
            <span class="text-xs text-slate-400 mt-1 block">Expenses in period</span>
        </div>

        <div class="card-custom p-5 bg-white border-2 {{ $netProfit >= 0 ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20' }}">
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-rose-700' }} block">Net Clinic Income</span>
            <div class="text-3xl font-black {{ $netProfit >= 0 ? 'text-emerald-800' : 'text-rose-700' }} tracking-tight mt-1">
                ₹{{ number_format($netProfit, 2) }}
            </div>
            <span class="text-xs text-slate-500 mt-1 block">Realized Collections - Expenses</span>
        </div>
    </div>

    <!-- PAYMENT MODES & EXPENSE CATEGORIES SPLIT -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- PAYMENT METHOD PIE/BREAKDOWN -->
        <div class="card-custom p-6 bg-white space-y-4">
            <h3 class="font-bold text-slate-900 text-sm">Collection By Payment Method</h3>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                    <span class="font-semibold text-slate-700 flex items-center gap-2"><i data-lucide="banknote" class="w-4 h-4 text-emerald-600"></i> Cash Payments</span>
                    <strong class="text-slate-900">₹{{ number_format($cashTotal, 2) }}</strong>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                    <span class="font-semibold text-slate-700 flex items-center gap-2"><i data-lucide="smartphone" class="w-4 h-4 text-blue-600"></i> UPI / PhonePe / GPay</span>
                    <strong class="text-slate-900">₹{{ number_format($upiTotal, 2) }}</strong>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                    <span class="font-semibold text-slate-700 flex items-center gap-2"><i data-lucide="credit-card" class="w-4 h-4 text-indigo-600"></i> Card (Debit / Credit)</span>
                    <strong class="text-slate-900">₹{{ number_format($cardTotal, 2) }}</strong>
                </div>
                <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                    <span class="font-semibold text-slate-700 flex items-center gap-2"><i data-lucide="building" class="w-4 h-4 text-purple-600"></i> Bank Transfer / NEFT</span>
                    <strong class="text-slate-900">₹{{ number_format($bankTotal, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- EXPENSES BY CATEGORY -->
        <div class="card-custom p-6 bg-white space-y-4">
            <h3 class="font-bold text-slate-900 text-sm">Expense Disbursements By Category</h3>
            <div class="space-y-3 text-xs">
                @forelse($expensesByCategory as $catName => $catAmount)
                    <div class="flex justify-between items-center p-3 rounded-xl bg-slate-50">
                        <span class="font-semibold text-slate-700">{{ $catName }}</span>
                        <strong class="text-rose-600">₹{{ number_format($catAmount, 2) }}</strong>
                    </div>
                @empty
                    <p class="text-slate-400 py-6 text-center">No expenses recorded for this period.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- DOCTOR REVENUE SUMMARY TABLE -->
    <div class="card-custom bg-white overflow-hidden">
        <div class="p-5 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Doctor Consultation Revenue Breakdown
        </div>
        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3 pl-5">Doctor Name</th>
                        <th class="p-3">Consultations Billed</th>
                        <th class="p-3">Total Invoiced</th>
                        <th class="p-3">Collected Amount</th>
                        <th class="p-3 pr-5">Pending Dues</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($doctorRevenue as $docName => $dStats)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 pl-5 font-bold text-slate-900">{{ $docName ?? 'Primary Physician' }}</td>
                            <td class="p-3 font-semibold text-slate-700">{{ $dStats['count'] }}</td>
                            <td class="p-3 font-bold text-slate-900">₹{{ number_format($dStats['billed'], 2) }}</td>
                            <td class="p-3 font-bold text-emerald-700">₹{{ number_format($dStats['collected'], 2) }}</td>
                            <td class="p-3 pr-5 font-bold text-rose-600">₹{{ number_format($dStats['due'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-slate-400">No doctor revenue data in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
