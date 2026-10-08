<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $transaction->receipt_no }} - CarePoint Clinic</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        @media print {
            body { background: white !important; }
            .no-print { display: none !important; }
            .receipt-card { border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="p-6 md:p-12 text-slate-900">

    <div class="max-w-md mx-auto mb-6 flex items-center justify-between no-print">
        <button onclick="window.history.back()" class="text-xs font-semibold text-blue-700 hover:underline">
            ← Back
        </button>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-sm">
            Print Money Receipt
        </button>
    </div>

    <!-- MONEY RECEIPT CARD -->
    <div class="receipt-card max-w-md mx-auto bg-white p-6 rounded-2xl border border-slate-200 shadow-md space-y-4">
        
        <!-- HEADER -->
        <div class="text-center border-b border-dashed border-slate-200 pb-4">
            <h2 class="text-lg font-black text-slate-900">{{ $clinic->name }}</h2>
            <p class="text-[11px] text-blue-700 font-semibold uppercase">{{ $clinic->tagline }}</p>
            <p class="text-[11px] text-slate-500 mt-1">{{ $clinic->address }}, {{ $clinic->city }} • Ph: {{ $clinic->phone }}</p>
            <div class="mt-2 inline-block px-3 py-0.5 rounded-full bg-slate-100 text-slate-800 font-bold text-[11px] uppercase tracking-wider">
                Payment Receipt
            </div>
        </div>

        <!-- RECEIPT META -->
        <div class="space-y-2 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-400">Receipt No:</span>
                <span class="font-mono font-bold text-blue-800">{{ $transaction->receipt_no }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Invoice Ref:</span>
                <span class="font-mono font-semibold text-slate-700">{{ $transaction->invoice->invoice_no ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Date:</span>
                <span class="font-semibold text-slate-800">{{ $transaction->payment_date->format('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Patient:</span>
                <span class="font-bold text-slate-900">{{ $transaction->patient->full_name }} ({{ $transaction->patient->patient_id }})</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-400">Payment Mode:</span>
                <span class="font-semibold text-slate-800">{{ $transaction->payment_method }}</span>
            </div>
            @if($transaction->transaction_reference)
                <div class="flex justify-between">
                    <span class="text-slate-400">Ref / Txn ID:</span>
                    <span class="font-mono text-slate-600">{{ $transaction->transaction_reference }}</span>
                </div>
            @endif
        </div>

        <!-- AMOUNT PAID HIGHLIGHT -->
        <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 text-center my-3">
            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-800 block">Amount Collected</span>
            <div class="text-2xl font-black text-emerald-700 mt-0.5">₹{{ number_format($transaction->amount, 2) }}</div>
        </div>

        <!-- BALANCES -->
        @if($transaction->invoice)
            <div class="space-y-1.5 text-[11px] border-t border-dashed border-slate-200 pt-3 text-slate-600">
                <div class="flex justify-between">
                    <span>Total Invoice Amount:</span>
                    <span>₹{{ number_format($transaction->invoice->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold {{ $transaction->invoice->due_amount > 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                    <span>Remaining Balance:</span>
                    <span>₹{{ number_format($transaction->invoice->due_amount, 2) }}</span>
                </div>
            </div>
        @endif

        <!-- SIGNATURE -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-between text-[11px]">
            <span class="text-slate-400">Collected by: {{ $transaction->collected_by ?? 'Reception Desk' }}</span>
            <div class="text-center">
                <div class="h-6 border-b border-slate-400 w-24"></div>
                <span class="text-[9px] text-slate-400 mt-0.5 block">Authorized Stamp</span>
            </div>
        </div>

    </div>

</body>
</html>
