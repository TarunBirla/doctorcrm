@extends('layouts.app')

@section('title', 'Invoices & Clinic Billing')
@section('breadcrumb', 'Billing')
@section('page_title', 'Invoices & Financial Billing')

@section('content')
<div class="space-y-6">

    <!-- FINANCIAL STATS CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Invoiced</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($totalBilled, 2) }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Collected</span>
            <div class="text-3xl font-extrabold text-emerald-700 tracking-tight mt-1">₹{{ number_format($totalCollected, 2) }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Pending Outstanding Dues</span>
            <div class="text-3xl font-extrabold text-rose-600 tracking-tight mt-1">₹{{ number_format($totalDue, 2) }}</div>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Invoices with Due</span>
            <div class="text-3xl font-extrabold text-amber-700 tracking-tight mt-1">{{ $unpaidCount }}</div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card-custom p-5 bg-white">
        <form action="{{ route('invoices.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search invoice no or patient..." 
                       class="w-64 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500">
                <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white">
                    <option value="">All Payment Statuses</option>
                    <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="partially_paid" {{ $status === 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="due" {{ $status === 'due' ? 'selected' : '' }}>Due</option>
                </select>
                <input type="date" name="date" value="{{ $date }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition">Filter</button>
                <a href="{{ route('invoices.index') }}" class="px-3 py-2 border rounded-xl text-slate-600 hover:bg-slate-50 font-medium">Reset</a>
            </div>

            <a href="{{ route('invoices.create') }}" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Generate Invoice</span>
            </a>
        </form>
    </div>

    <!-- INVOICES TABLE CARD -->
    <div class="card-custom bg-white overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Invoice Records & Billing Ledger</h3>
            <span class="text-xs text-slate-400">Total {{ $invoices->total() }} invoices</span>
        </div>

        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3 pl-5">Invoice No</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Patient</th>
                        <th class="p-3">Total Amount</th>
                        <th class="p-3">Paid Amount</th>
                        <th class="p-3">Due Balance</th>
                        <th class="p-3">Method</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 pr-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 pl-5 font-mono font-bold text-blue-700">
                                {{ $inv->invoice_no }}
                            </td>
                            <td class="p-3 text-slate-600">{{ $inv->invoice_date->format('d M Y') }}</td>
                            <td class="p-3">
                                <a href="{{ route('patients.show', $inv->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700">
                                    {{ $inv->patient->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400 block">{{ $inv->patient->patient_id }}</span>
                            </td>
                            <td class="p-3 font-bold text-slate-900">₹{{ number_format($inv->total_amount, 2) }}</td>
                            <td class="p-3 font-semibold text-emerald-700">₹{{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="p-3 font-bold {{ $inv->due_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                                ₹{{ number_format($inv->due_amount, 2) }}
                            </td>
                            <td class="p-3 text-slate-600 font-medium">
                                {{ $inv->payment_method ?? 'Not Paid' }}
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase 
                                    @if($inv->payment_status === 'paid') bg-emerald-100 text-emerald-800
                                    @elseif($inv->payment_status === 'partially_paid') bg-amber-100 text-amber-800
                                    @else bg-rose-100 text-rose-800 @endif">
                                    {{ str_replace('_', ' ', $inv->payment_status) }}
                                </span>
                            </td>
                            <td class="p-3 pr-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('invoices.print', $inv->id) }}" target="_blank" title="Print" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">
                                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('invoices.show', $inv->id) }}" title="View" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="p-12 text-center text-slate-400">No invoices found.</td></tr>
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
@endsection
