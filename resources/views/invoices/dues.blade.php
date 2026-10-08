@extends('layouts.app')

@section('title', 'Due Payments Collection')
@section('breadcrumb', 'Billing / Due Payments')
@section('page_title', 'Due Payments & Outstanding Recovery')

@section('content')
<div class="space-y-6">

    <!-- OVERVIEW STATS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-custom p-5 bg-white border-rose-200">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Total Outstanding Balance</span>
            <div class="text-3xl font-black text-rose-600 tracking-tight mt-1">₹{{ number_format($totalOutstandingDue, 2) }}</div>
            <span class="text-xs text-slate-400 mt-1 block">Receivable across all patients</span>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Patients with Overdue Dues</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $patientsWithDuesCount }}</div>
            <span class="text-xs text-slate-400 mt-1 block">Active patients requiring collection</span>
        </div>

        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Unsettled Invoices Count</span>
            <div class="text-3xl font-extrabold text-amber-700 tracking-tight mt-1">{{ $dueInvoices->total() }}</div>
            <span class="text-xs text-slate-400 mt-1 block">Partially paid or completely unpaid bills</span>
        </div>
    </div>

    <!-- DUE PAYMENTS TABLE CARD -->
    <div class="card-custom bg-white overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Outstanding Invoices Ledger</h3>
                <p class="text-xs text-slate-400">Collect partial or full dues directly with instant receipt generation</p>
            </div>

            <form action="{{ route('due-payments.index') }}" method="GET" class="flex items-center gap-2 text-xs">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search patient or invoice..." 
                       class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-blue-500 w-64">
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3.5 pl-5">Patient Details</th>
                        <th class="p-3.5">Invoice No</th>
                        <th class="p-3.5">Invoice Date</th>
                        <th class="p-3.5">Days Overdue</th>
                        <th class="p-3.5">Total Billed</th>
                        <th class="p-3.5">Paid So Far</th>
                        <th class="p-3.5">Remaining Due</th>
                        <th class="p-3.5 pr-5 text-right">Collect Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dueInvoices as $inv)
                        @php
                            $daysOverdue = max(0, (int) now()->diffInDays($inv->invoice_date));
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 pl-5">
                                <a href="{{ route('patients.show', $inv->patient_id) }}" class="font-bold text-slate-900 hover:text-blue-700 block">
                                    {{ $inv->patient->full_name }}
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $inv->patient->patient_id }} • Ph: {{ $inv->patient->mobile }}</span>
                            </td>
                            <td class="p-3.5 font-mono font-bold text-blue-700">
                                {{ $inv->invoice_no }}
                            </td>
                            <td class="p-3.5 text-slate-600 font-medium">
                                {{ $inv->invoice_date->format('d M Y') }}
                            </td>
                            <td class="p-3.5">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $daysOverdue > 7 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $daysOverdue }} days
                                </span>
                            </td>
                            <td class="p-3.5 font-semibold text-slate-800">
                                ₹{{ number_format($inv->total_amount, 2) }}
                            </td>
                            <td class="p-3.5 font-semibold text-emerald-700">
                                ₹{{ number_format($inv->paid_amount, 2) }}
                            </td>
                            <td class="p-3.5">
                                <span class="font-black text-rose-600 text-sm">
                                    ₹{{ number_format($inv->due_amount, 2) }}
                                </span>
                            </td>
                            <td class="p-3.5 pr-5 text-right">
                                <button onclick="openCollectModal('{{ $inv->id }}', '{{ $inv->invoice_no }}', '{{ $inv->patient->full_name }}', {{ $inv->due_amount }})" 
                                        class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-xs">
                                    Collect Payment
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">
                                <i data-lucide="check-circle" class="w-8 h-8 mx-auto text-emerald-400 mb-2"></i>
                                Great! No overdue patient payments pending right now.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dueInvoices->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $dueInvoices->links() }}
            </div>
        @endif

    </div>

</div>

<!-- MODAL: DUES COLLECTION -->
<div id="collectModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Collect Due Payment</h3>
                <p class="text-xs text-slate-500" id="modalPatientText">Patient</p>
            </div>
            <button onclick="closeModal('collectModal')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="collectForm" action="" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Collection Amount (₹) *</label>
                <input type="number" step="0.01" min="1" id="collectAmountInput" name="amount" required 
                       class="w-full px-3 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white text-base font-extrabold text-slate-900 outline-none">
                <p class="text-[11px] text-slate-400 mt-1" id="modalMaxDueText">Maximum payable due: ₹0.00</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Payment Method *</label>
                    <select name="payment_method" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI / QR Code</option>
                        <option value="Card">Card Swipe</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Collection Date *</label>
                    <input type="date" name="payment_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Transaction Ref / Cheque No</label>
                <input type="text" name="transaction_reference" placeholder="e.g. UPI/5839201" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('collectModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-xs">
                    Confirm & Record Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCollectModal(invId, invNo, patientName, dueAmount) {
        document.getElementById('collectForm').action = `/invoices/${invId}/pay`;
        document.getElementById('modalPatientText').innerText = `${patientName} • Invoice #${invNo}`;
        document.getElementById('collectAmountInput').value = dueAmount;
        document.getElementById('collectAmountInput').max = dueAmount;
        document.getElementById('modalMaxDueText').innerText = `Maximum outstanding due: ₹${dueAmount.toFixed(2)}. Partial payment supported.`;
        openModal('collectModal');
    }
</script>
@endsection
