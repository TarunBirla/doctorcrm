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
            <button type="button" onclick="openModal('editInvoiceModal')" class="px-3.5 py-2 border border-slate-200 bg-white text-slate-700 rounded-xl text-xs font-bold hover:bg-slate-50 transition flex items-center gap-1.5 shadow-2xs">
                <i data-lucide="edit" class="w-3.5 h-3.5 text-blue-600"></i>
                <span>Edit Bill / Dues</span>
            </button>
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
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-slate-900 text-xs uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="receipt" class="w-4 h-4 text-emerald-600"></i>
                    <span>Payment Transactions History</span>
                </h4>
                <span class="text-[11px] text-slate-400">Paid amount &amp; pending due will auto-recalculate upon editing</span>
            </div>
            <div class="overflow-x-auto text-xs">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                        <tr class="border-b border-slate-100">
                            <th class="p-2.5">Receipt No</th>
                            <th class="p-2.5">Transaction ID</th>
                            <th class="p-2.5">Date</th>
                            <th class="p-2.5">Method</th>
                            <th class="p-2.5">Amount</th>
                            <th class="p-2.5">Reference / Notes</th>
                            <th class="p-2.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($invoice->transactions as $txn)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="p-2.5 font-mono font-bold text-blue-700">{{ $txn->receipt_no }}</td>
                                <td class="p-2.5 font-mono text-slate-500">{{ $txn->transaction_no }}</td>
                                <td class="p-2.5 text-slate-600 font-semibold">{{ $txn->payment_date ? $txn->payment_date->format('d M Y') : '—' }}</td>
                                <td class="p-2.5">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $txn->payment_method }}
                                    </span>
                                </td>
                                <td class="p-2.5 font-black text-emerald-700">₹{{ number_format($txn->amount, 2) }}</td>
                                <td class="p-2.5 text-slate-500 text-[11px]">
                                    {{ $txn->transaction_reference ?: ($txn->notes ?: '—') }}
                                </td>
                                <td class="p-2.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <a href="{{ route('receipts.print', $txn->id) }}" target="_blank" class="px-2 py-1 rounded-lg bg-blue-50 text-blue-700 font-bold hover:bg-blue-100 transition text-[11px]" title="Print Receipt">
                                            Print
                                        </a>
                                        <button type="button" 
                                                onclick="openEditTxnModal({{ $txn->id }}, '{{ $txn->receipt_no }}', {{ (float)$txn->amount }}, '{{ $txn->payment_method }}', '{{ $txn->payment_date ? $txn->payment_date->format('Y-m-d') : now()->toDateString() }}', '{{ addslashes($txn->transaction_reference ?? '') }}', '{{ addslashes($txn->notes ?? '') }}')"
                                                class="p-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 transition" title="Edit Payment Transaction">
                                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <form action="{{ route('payment-transactions.destroy', $txn->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete payment receipt #{{ $txn->receipt_no }} of ₹{{ number_format($txn->amount, 2) }}? Invoice balance will auto-recalculate.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Delete Payment Transaction">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="p-4 text-center text-slate-400">No payment transactions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- MODAL: EDIT PAYMENT TRANSACTION -->
<div id="editTxnModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Edit Payment Record</h3>
                <span id="editTxnReceiptBadge" class="text-[11px] font-mono text-blue-700 font-bold"></span>
            </div>
            <button onclick="closeModal('editTxnModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="editTxnForm" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Correct Paid Amount (₹) *</label>
                <input type="number" step="0.01" min="0.01" name="amount" id="editTxnAmount" required class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 font-black text-slate-900 text-base outline-none focus:border-blue-500">
                <span class="text-[11px] text-slate-400 mt-1 block">Updating amount will automatically recalculate total paid &amp; balance due.</span>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Payment Method *</label>
                    <select name="payment_method" id="editTxnMethod" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500">
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="Card">Card</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Online Payment">Online Payment</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Payment Date *</label>
                    <input type="date" name="payment_date" id="editTxnDate" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">UPI Ref / Transaction Reference</label>
                <input type="text" name="transaction_reference" id="editTxnRef" placeholder="e.g. UTR / UPI / Cheque No" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Notes</label>
                <input type="text" name="notes" id="editTxnNotes" placeholder="Reason for edit / note" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editTxnModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl shadow-xs">Save &amp; Recalculate</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: EDIT INVOICE BILL & DUES -->
<div id="editInvoiceModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Edit Invoice Bill &amp; Dues (#{{ $invoice->invoice_no }})</h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Adjust discount, additional charges, or invoice date</p>
            </div>
            <button onclick="closeModal('editInvoiceModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('invoices.update', $invoice->id) }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Discount (₹)</label>
                    <input type="number" step="0.01" min="0" name="discount" value="{{ $invoice->discount }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 font-bold text-slate-900 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Additional Charges (₹)</label>
                    <input type="number" step="0.01" min="0" name="additional_charges" value="{{ $invoice->additional_charges }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 font-bold text-slate-900 outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Invoice Date</label>
                <input type="date" name="invoice_date" value="{{ $invoice->invoice_date->format('Y-m-d') }}" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Notes / Instructions</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none focus:border-blue-500">{{ $invoice->notes }}</textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('editInvoiceModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-navy-900 hover:bg-navy-800 text-white font-bold rounded-xl shadow-xs">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditTxnModal(txnId, receiptNo, amount, method, date, ref, notes) {
    const form = document.getElementById('editTxnForm');
    form.action = '/payment-transactions/' + txnId;
    document.getElementById('editTxnReceiptBadge').innerText = 'Receipt: #' + receiptNo;
    document.getElementById('editTxnAmount').value = amount;
    document.getElementById('editTxnMethod').value = method;
    document.getElementById('editTxnDate').value = date;
    document.getElementById('editTxnRef').value = ref || '';
    document.getElementById('editTxnNotes').value = notes || '';
    openModal('editTxnModal');
}
</script>

<!-- MODAL: QUICK PAYMENT -->
@if($invoice->due_amount > 0)
<div id="quickPaymentModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden my-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Collect Due for Invoice #{{ $invoice->invoice_no }}</h3>
                @if($clinic && $clinic->name)
                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-200">
                    <i data-lucide="building-2" class="w-3 h-3"></i>
                    <span>{{ $clinic->name }}</span>
                </span>
                @endif
            </div>
            <button onclick="closeModal('quickPaymentModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form action="{{ route('invoices.pay', $invoice->id) }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf

            @if($clinic && $clinic->qr_code)
            <div class="p-3.5 rounded-xl bg-emerald-50/70 border border-emerald-200 text-center space-y-2">
                <div class="flex items-center justify-center gap-1.5 text-emerald-800 font-bold text-xs">
                    <i data-lucide="qr-code" class="w-4 h-4 text-emerald-600"></i>
                    <span>Scan Clinic QR to Pay ({{ $clinic->name }})</span>
                </div>
                <div class="bg-white p-2.5 rounded-xl border border-emerald-200 inline-block shadow-xs">
                    <img src="{{ asset($clinic->qr_code) }}" alt="{{ $clinic->name }} QR" class="w-44 h-44 object-contain mx-auto">
                </div>
                <p class="text-[11px] text-emerald-700 font-semibold">Scan via GPay, PhonePe, Paytm or any UPI App</p>
            </div>
            @endif

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
