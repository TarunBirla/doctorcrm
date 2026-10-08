@extends('layouts.app')

@section('title', 'Generate New Invoice')
@section('breadcrumb', 'Billing / Create Invoice')
@section('page_title', 'Create Clinic Invoice')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="card-custom p-8 bg-white space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h3 class="font-bold text-slate-900 text-base">Generate Patient Invoice & Bill</h3>
            <p class="text-xs text-slate-400">Add services, fees, procedures, and collect advance or full payment</p>
        </div>

        <form action="{{ route('invoices.store') }}" method="POST" id="invoiceForm" class="space-y-6 text-xs">
            @csrf

            <!-- Patient & Doctor Selection -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Patient *</label>
                    <select name="patient_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        <option value="">-- Choose Patient --</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ $selectedPatient && $selectedPatient->id === $p->id ? 'selected' : '' }}>
                                {{ $p->full_name }} ({{ $p->patient_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Doctor *</label>
                    <select name="doctor_id" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                        @foreach($doctors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Invoice Date *</label>
                    <input type="date" name="invoice_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 focus:bg-white outline-none">
                </div>
            </div>

            <!-- Line Items Table -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-xs uppercase tracking-wider text-slate-400">Billable Services & Procedures</label>
                    <button type="button" onclick="addInvoiceItemRow()" class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg font-bold hover:bg-blue-100">
                        + Add Service Item
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="invoiceItemsTable">
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase text-slate-400">
                            <tr class="border-b border-slate-100">
                                <th class="p-2.5">Service Description</th>
                                <th class="p-2.5 w-24">Qty</th>
                                <th class="p-2.5 w-32">Unit Price (₹)</th>
                                <th class="p-2.5 w-32">Total (₹)</th>
                                <th class="p-2.5 w-12"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="itemsTableBody">
                            <tr class="item-row">
                                <td class="p-2">
                                    <input type="text" name="items[0][description]" value="Specialist Physician Consultation Fee" required class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none font-semibold">
                                </td>
                                <td class="p-2">
                                    <input type="number" min="1" value="1" name="items[0][quantity]" oninput="recalcInvoice()" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none text-center item-qty">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.01" min="0" value="800" name="items[0][unit_price]" oninput="recalcInvoice()" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none text-right font-bold item-price">
                                </td>
                                <td class="p-2 font-bold text-slate-900 text-right item-total">
                                    ₹800.00
                                </td>
                                <td class="p-2 text-center">
                                    <button type="button" onclick="this.closest('.item-row').remove(); recalcInvoice();" class="text-rose-500 hover:text-rose-700">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Totals & Payment Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div class="space-y-3">
                    <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Initial Payment (Optional)</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-500 mb-1">Paid Now (₹)</label>
                            <input type="number" step="0.01" min="0" name="paid_now" id="paidNowInput" value="800" oninput="recalcDue()" class="w-full px-3 py-2 border border-slate-200 rounded-xl font-bold bg-slate-50 outline-none">
                        </div>
                        <div>
                            <label class="block text-slate-500 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="Card">Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-500 mb-1">Payment Reference</label>
                        <input type="text" name="transaction_reference" placeholder="e.g. UPI Ref / Cheque No" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    </div>
                </div>

                <div class="space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500">Subtotal:</span>
                        <strong class="text-slate-900" id="subtotalDisplay">₹800.00</strong>
                    </div>
                    <div class="flex justify-between py-1 items-center">
                        <span class="text-slate-500">Discount (₹):</span>
                        <input type="number" min="0" value="0" name="discount" id="discountInput" oninput="recalcInvoice()" class="w-24 px-2 py-1 text-right border border-slate-200 rounded-lg bg-white">
                    </div>
                    <div class="flex justify-between py-1 items-center">
                        <span class="text-slate-500">Additional Charges (₹):</span>
                        <input type="number" min="0" value="0" name="additional_charges" id="addChargesInput" oninput="recalcInvoice()" class="w-24 px-2 py-1 text-right border border-slate-200 rounded-lg bg-white">
                    </div>
                    <div class="flex justify-between py-2 border-t border-slate-200 text-sm font-extrabold">
                        <span class="text-slate-800">Total Billed:</span>
                        <span class="text-blue-900" id="totalDisplay">₹800.00</span>
                    </div>
                    <div class="flex justify-between py-1 text-xs font-bold text-rose-600">
                        <span>Remaining Due:</span>
                        <span id="dueDisplay">₹0.00</span>
                    </div>
                </div>
            </div>

            <!-- SUBMIT -->
            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('invoices.index') }}" class="px-4 py-2 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 font-semibold">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-navy-900 hover:bg-navy-800 text-white rounded-xl font-bold transition shadow-sm">
                    Generate Invoice & Receipt
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    let itemIdx = 1;
    function addInvoiceItemRow() {
        const tbody = document.getElementById('itemsTableBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="p-2">
                <input type="text" name="items[${itemIdx}][description]" placeholder="e.g. ECG Test / Dressing" required class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none font-semibold">
            </td>
            <td class="p-2">
                <input type="number" min="1" value="1" name="items[${itemIdx}][quantity]" oninput="recalcInvoice()" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none text-center item-qty">
            </td>
            <td class="p-2">
                <input type="number" step="0.01" min="0" value="500" name="items[${itemIdx}][unit_price]" oninput="recalcInvoice()" class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg outline-none text-right font-bold item-price">
            </td>
            <td class="p-2 font-bold text-slate-900 text-right item-total">
                ₹500.00
            </td>
            <td class="p-2 text-center">
                <button type="button" onclick="this.closest('.item-row').remove(); recalcInvoice();" class="text-rose-500 hover:text-rose-700">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        tbody.appendChild(tr);
        lucide.createIcons();
        itemIdx++;
        recalcInvoice();
    }

    function recalcInvoice() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const total = qty * price;
            row.querySelector('.item-total').innerText = '₹' + total.toFixed(2);
            subtotal += total;
        });

        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const addCharges = parseFloat(document.getElementById('addChargesInput').value) || 0;
        const grandTotal = Math.max(0, (subtotal + addCharges) - discount);

        document.getElementById('subtotalDisplay').innerText = '₹' + subtotal.toFixed(2);
        document.getElementById('totalDisplay').innerText = '₹' + grandTotal.toFixed(2);

        recalcDue(grandTotal);
    }

    function recalcDue(totalVal) {
        if (!totalVal) {
            const subtotal = parseFloat(document.getElementById('subtotalDisplay').innerText.replace('₹', '')) || 0;
            const discount = parseFloat(document.getElementById('discountInput').value) || 0;
            const addCharges = parseFloat(document.getElementById('addChargesInput').value) || 0;
            totalVal = Math.max(0, (subtotal + addCharges) - discount);
        }
        const paidNow = parseFloat(document.getElementById('paidNowInput').value) || 0;
        const due = Math.max(0, totalVal - paidNow);
        document.getElementById('dueDisplay').innerText = '₹' + due.toFixed(2);
    }
</script>
@endsection
