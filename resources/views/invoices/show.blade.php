@extends('layouts.app')

@section('title', 'Invoice #' . $invoice->invoice_no)
@section('breadcrumb', 'Billing / ' . $invoice->invoice_no)
@section('page_title', 'Invoice & Payment Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('invoices.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            <span>Back to Invoices</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('invoices.print', $invoice->id) }}" target="_blank" class="px-4 py-2 bg-navy-900 text-white rounded-xl text-xs font-bold hover:bg-navy-800 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Print Invoice</span>
            </a>
            @if($invoice->due_amount > 0)
                <button onclick="openModal('quickPaymentModal')" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                    <span>Collect Payment (₹{{ number_format($invoice->due_amount, 2) }})</span>
                </button>
            @endif
        </div>
    </div>

    <!-- INVOICE DETAILS CARD -->
    <div class="card-custom p-8 bg-white space-y-6">
        
        <!-- HEADER -->
        <div class="flex items-start justify-between border-b border-slate-100 pb-6">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">{{ $clinic->name }}</h2>
                <p class="text-xs text-blue-700 font-semibold">{{ $clinic->tagline }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $clinic->address }}, {{ $clinic->city }} • Ph: {{ $clinic->phone }}</p>
                @if($clinic->gst_number)
                    <p class="text-[10px] text-slate-400 font-mono">GSTIN: {{ $clinic->gst_number }}</p>
                @endif
            </div>
            <div class="text-right">
                <span class="text-sm font-mono font-black text-slate-900 block">{{ $invoice->invoice_no }}</span>
                <span class="text-xs text-slate-500 block">Date: {{ $invoice->invoice_date->format('d M Y') }}</span>
                <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase 
                    @if($invoice->payment_status === 'paid') bg-emerald-100 text-emerald-800
                    @elseif($invoice->payment_status === 'partially_paid') bg-amber-100 text-amber-800
                    @else bg-rose-100 text-rose-800 @endif">
                    {{ str_replace('_', ' ', $invoice->payment_status) }}
                </span>
            </div>
        </div>

        <!-- BILLED TO PATIENT -->
        <div class="p-4 bg-slate-50 rounded-2xl grid grid-cols-2 md:grid-cols-4 gap-4 text-xs border border-slate-100">
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Billed To:</span>
                <a href="{{ route('patients.show', $invoice->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700">
                    {{ $invoice->patient->full_name }}
                </a>
                <span class="text-[11px] text-slate-500 block">{{ $invoice->patient->patient_id }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Phone Number:</span>
                <span class="font-semibold text-slate-800">{{ $invoice->patient->mobile }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Attending Doctor:</span>
                <span class="font-semibold text-slate-800">{{ $invoice->doctor->name ?? $clinic->doctor_name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Payment Mode:</span>
                <span class="font-bold text-blue-700">{{ $invoice->payment_method ?? 'Pending' }}</span>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3">#</th>
                        <th class="p-3">Service / Procedure Description</th>
                        <th class="p-3 text-center">Qty</th>
                        <th class="p-3 text-right">Unit Price</th>
                        <th class="p-3 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($invoice->items as $idx => $it)
                        <tr>
                            <td class="p-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                            <td class="p-3 font-bold text-slate-900">{{ $it->item_description }}</td>
                            <td class="p-3 text-center">{{ $it->quantity }}</td>
                            <td class="p-3 text-right text-slate-600">₹{{ number_format($it->unit_price, 2) }}</td>
                            <td class="p-3 text-right font-bold text-slate-900">₹{{ number_format($it->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- FINANCIAL SUMMARY TOTALS -->
        <div class="flex justify-end pt-4 border-t border-slate-100">
            <div class="w-72 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span>₹{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                @if($invoice->discount > 0)
                    <div class="flex justify-between text-emerald-700 font-semibold">
                        <span>Discount:</span>
                        <span>-₹{{ number_format($invoice->discount, 2) }}</span>
                    </div>
                @endif
                @if($invoice->additional_charges > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Additional Charges:</span>
                        <span>+₹{{ number_format($invoice->additional_charges, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between py-2 border-t border-slate-200 text-sm font-extrabold text-slate-900">
                    <span>Grand Total:</span>
                    <span class="text-blue-900">₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-emerald-700 font-bold">
                    <span>Total Paid:</span>
                    <span>₹{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-rose-600 font-black text-sm pt-1 border-t border-slate-100">
                    <span>Balance Due:</span>
                    <span>₹{{ number_format($invoice->due_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- PAYMENT TRANSACTIONS LOG -->
        <div class="pt-6 border-t border-slate-100 space-y-3">
            <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider">Payment Transactions</h4>
            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                        <tr class="border-b border-slate-100">
                            <th class="p-2.5">Receipt No</th>
                            <th class="p-2.5">Transaction ID</th>
                            <th class="p-2.5">Date</th>
                            <th class="p-2.5">Method</th>
                            <th class="p-2.5">Amount</th>
                            <th class="p-2.5 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoice->transactions as $txn)
                            <tr>
                                <td class="p-2.5 font-mono font-bold text-blue-700">{{ $txn->receipt_no }}</td>
                                <td class="p-2.5 font-mono text-slate-500">{{ $txn->transaction_no }}</td>
                                <td class="p-2.5 text-slate-600">{{ $txn->payment_date->format('d M Y') }}</td>
                                <td class="p-2.5 font-semibold text-slate-800">{{ $txn->payment_method }}</td>
                                <td class="p-2.5 font-bold text-emerald-700">₹{{ number_format($txn->amount, 2) }}</td>
                                <td class="p-2.5 text-right">
                                    <a href="{{ route('receipts.print', $txn->id) }}" target="_blank" class="text-blue-700 font-bold hover:underline">
                                        Print Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 text-center text-slate-400">No payment transactions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- MODAL: QUICK PAYMENT -->
@if($invoice->due_amount > 0)
<div id="quickPaymentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Collect Due for Invoice #{{ $invoice->invoice_no }}</h3>
            <button onclick="closeModal('quickPaymentModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('invoices.pay', $invoice->id) }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Collection Amount (₹) *</label>
                <input type="number" step="0.01" min="1" max="{{ $invoice->due_amount }}" name="amount" value="{{ $invoice->due_amount }}" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 font-black text-slate-900 text-base outline-none">
                <span class="text-[11px] text-slate-400 mt-1 block">Pending due is ₹{{ number_format($invoice->due_amount, 2) }}. Partial settlement allowed.</span>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Method *</label>
                    <select name="payment_method" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="Card">Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" name="payment_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Reference / Note</label>
                <input type="text" name="transaction_reference" placeholder="e.g. UPI Ref / Receipt note" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('quickPaymentModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl">Confirm Payment</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection
