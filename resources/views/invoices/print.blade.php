<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_no }} - {{ $invoice->patient->full_name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .print-card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="p-6 md:p-12 text-slate-900">

    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('invoices.show', $invoice->id) }}" class="text-xs font-semibold text-blue-700 hover:underline">
            ← Back to Invoice View
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            <span>Print Invoice</span>
        </button>
    </div>

    <!-- INVOICE SHEET -->
    <div class="print-card max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-2xl border border-slate-200 shadow-lg space-y-6">
        
        <!-- HEADER -->
        <div class="border-b-2 border-slate-900 pb-6 flex items-start justify-between">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $clinic->name }}</h1>
                <p class="text-xs text-blue-700 font-semibold uppercase tracking-wider">{{ $clinic->tagline }}</p>
                <div class="text-xs text-slate-500 mt-2 space-y-0.5">
                    <p>{{ $clinic->address }}, {{ $clinic->city }}, {{ $clinic->state }} - {{ $clinic->pincode }}</p>
                    <p>Phone: {{ $clinic->phone }} • Email: {{ $clinic->email }}</p>
                    @if($clinic->gst_number)
                        <p class="font-mono">GSTIN: {{ $clinic->gst_number }}</p>
                    @endif
                </div>
            </div>

            <div class="text-right">
                <span class="text-xl font-black text-slate-900 font-mono block">TAX INVOICE</span>
                <span class="text-xs font-mono font-bold text-blue-700 block mt-1">#{{ $invoice->invoice_no }}</span>
                <span class="text-xs text-slate-500 block">Date: {{ $invoice->invoice_date->format('d M Y') }}</span>
                <span class="inline-block mt-2 px-2.5 py-0.5 rounded text-[10px] font-bold uppercase 
                    {{ $invoice->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    Status: {{ strtoupper(str_replace('_', ' ', $invoice->payment_status)) }}
                </span>
            </div>
        </div>

        <!-- PATIENT INFO -->
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Billed To (Patient):</span>
                <strong class="text-slate-900 font-bold text-sm block">{{ $invoice->patient->full_name }}</strong>
                <span class="text-slate-500 font-mono">{{ $invoice->patient->patient_id }} • Ph: {{ $invoice->patient->mobile }}</span>
                <p class="text-slate-500 mt-0.5">{{ $invoice->patient->address ?? $invoice->patient->city }}</p>
            </div>
            <div class="text-right">
                <span class="text-slate-400 block text-[10px] font-bold uppercase">Attending Doctor:</span>
                <strong class="text-slate-900 font-bold text-sm block">{{ $invoice->doctor->name ?? $clinic->doctor_name }}</strong>
                <span class="text-slate-500 font-mono">Reg No: {{ $invoice->doctor->registration_no ?? $clinic->doctor_reg_no }}</span>
            </div>
        </div>

        <!-- ITEMS TABLE -->
        <table class="w-full text-left text-xs border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-200 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                    <th class="py-2.5">#</th>
                    <th class="py-2.5">Service Description</th>
                    <th class="py-2.5 text-center">Qty</th>
                    <th class="py-2.5 text-right">Unit Rate (₹)</th>
                    <th class="py-2.5 text-right">Amount (₹)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($invoice->items as $idx => $it)
                    <tr>
                        <td class="py-3 text-slate-400 font-bold">{{ $idx + 1 }}</td>
                        <td class="py-3 font-bold text-slate-900">{{ $it->item_description }}</td>
                        <td class="py-3 text-center text-slate-700">{{ $it->quantity }}</td>
                        <td class="py-3 text-right text-slate-600">{{ number_format($it->unit_price, 2) }}</td>
                        <td class="py-3 text-right font-bold text-slate-900">{{ number_format($it->total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- TOTALS BREAKDOWN -->
        <div class="flex justify-end pt-4 border-t border-slate-200">
            <div class="w-64 space-y-2 text-xs">
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
                <div class="flex justify-between py-2 border-t border-slate-300 text-sm font-black text-slate-900">
                    <span>Invoice Total:</span>
                    <span class="text-blue-900">₹{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-emerald-700">
                    <span>Amount Paid:</span>
                    <span>₹{{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-black text-rose-600 text-sm pt-1 border-t border-slate-200">
                    <span>Balance Due:</span>
                    <span>₹{{ number_format($invoice->due_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- FOOTER & SIGNATURE -->
        <div class="pt-8 border-t border-slate-100 flex items-end justify-between text-xs">
            <div class="text-[10px] text-slate-400 max-w-sm">
                <p>{{ $clinic->invoice_footer ?? 'Thank you for choosing CarePoint Clinic. Keep this bill for your medical reimbursement.' }}</p>
            </div>
            <div class="text-center min-w-[180px]">
                <div class="h-10 border-b border-slate-300 mb-1"></div>
                <span class="font-bold text-slate-800 block">Authorized Signatory</span>
                <span class="text-[10px] text-slate-400">CarePoint Accounts Desk</span>
            </div>
        </div>

    </div>

</body>
</html>
