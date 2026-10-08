@extends('layouts.app')

@section('title', 'Payment Transactions History')
@section('breadcrumb', 'Billing / Payments')
@section('page_title', 'Payment Receipts & Transactions Ledger')

@section('content')
<div class="space-y-6">

    <!-- STATS -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Collections</span>
            <div class="text-3xl font-extrabold text-emerald-700 tracking-tight mt-1">₹{{ number_format($totalCollected, 2) }}</div>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Cash Received</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($cashTotal, 2) }}</div>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">UPI / QR Collections</span>
            <div class="text-3xl font-extrabold text-blue-700 tracking-tight mt-1">₹{{ number_format($upiTotal, 2) }}</div>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Card Swipes</span>
            <div class="text-3xl font-extrabold text-indigo-700 tracking-tight mt-1">₹{{ number_format($cardTotal, 2) }}</div>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="card-custom p-5 bg-white">
        <form action="{{ route('payments.index') }}" method="GET" class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <div class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search receipt, txn, or patient..." 
                       class="w-64 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500">
                <select name="method" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white">
                    <option value="">All Payment Methods</option>
                    <option value="Cash" {{ $method === 'Cash' ? 'selected' : '' }}>Cash</option>
                    <option value="UPI" {{ $method === 'UPI' ? 'selected' : '' }}>UPI</option>
                    <option value="Card" {{ $method === 'Card' ? 'selected' : '' }}>Card</option>
                    <option value="Bank Transfer" {{ $method === 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                </select>
                <input type="date" name="date" value="{{ $date }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition">Filter</button>
                <a href="{{ route('payments.index') }}" class="px-3 py-2 border rounded-xl text-slate-600 hover:bg-slate-50 font-medium">Reset</a>
            </div>

            <a href="{{ route('due-payments.index') }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                <span>Collect Due Payment</span>
            </a>
        </form>
    </div>

    <!-- TRANSACTIONS TABLE -->
    <div class="card-custom bg-white overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Receipt Transactions Ledger</h3>
            <span class="text-xs text-slate-400">Total {{ $transactions->total() }} recorded vouchers</span>
        </div>

        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3 pl-5">Receipt No</th>
                        <th class="p-3">Txn ID</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Patient</th>
                        <th class="p-3">Invoice Ref</th>
                        <th class="p-3">Method</th>
                        <th class="p-3">Collected Amount</th>
                        <th class="p-3 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $txn)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3 pl-5 font-mono font-bold text-blue-700">
                                {{ $txn->receipt_no }}
                            </td>
                            <td class="p-3 font-mono text-slate-500">{{ $txn->transaction_no }}</td>
                            <td class="p-3 text-slate-600">{{ $txn->payment_date->format('d M Y') }}</td>
                            <td class="p-3">
                                <a href="{{ route('patients.show', $txn->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700">
                                    {{ $txn->patient->full_name }}
                                </a>
                                <span class="text-[10px] font-mono text-slate-400 block">{{ $txn->patient->patient_id }}</span>
                            </td>
                            <td class="p-3 font-mono text-slate-600">
                                <a href="{{ route('invoices.show', $txn->invoice_id) }}" class="hover:underline">
                                    {{ $txn->invoice->invoice_no ?? '-' }}
                                </a>
                            </td>
                            <td class="p-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $txn->payment_method }}
                                </span>
                            </td>
                            <td class="p-3 font-extrabold text-emerald-700 text-sm">
                                ₹{{ number_format($txn->amount, 2) }}
                            </td>
                            <td class="p-3 pr-5 text-right">
                                <a href="{{ route('receipts.print', $txn->id) }}" target="_blank" class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold rounded-lg inline-flex items-center gap-1">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                    <span>Print Receipt</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="p-12 text-center text-slate-400">No payment transactions found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
