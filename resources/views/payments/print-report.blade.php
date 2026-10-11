<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Report - {{ $periodLabel }}</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { margin: 0; padding: 24px; color: #0f172a; background: #f8fafc; font-size: 12.5px; }
        .report-sheet { max-width: 1050px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
        .top-bar { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #0d9488; padding-bottom: 16px; margin-bottom: 20px; }
        .clinic-brand h1 { margin: 0; font-size: 22px; color: #0f766e; }
        .clinic-brand p { margin: 4px 0 0; color: #475569; font-size: 12.5px; }
        .kpi-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 22px; }
        .kpi-box { padding: 14px; border-radius: 10px; border: 1px solid #cbd5e1; background: #f8fafc; }
        .kpi-box .label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; }
        .kpi-box .val { font-size: 19px; font-weight: 800; margin-top: 4px; }
        h3 { margin: 22px 0 10px; font-size: 15px; color: #0f766e; border-left: 4px solid #0d9488; padding-left: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; font-size: 12px; }
        th { background: #f1f5f9; font-weight: 700; color: #334155; }
        .badge-paid { background: #dcfce7; color: #15803d; padding: 2px 7px; border-radius: 4px; font-weight: 700; font-size: 10.5px; }
        .badge-due { background: #fee2e2; color: #b91c1c; padding: 2px 7px; border-radius: 4px; font-weight: 700; font-size: 10.5px; }
        .badge-partial { background: #fef3c7; color: #b45309; padding: 2px 7px; border-radius: 4px; font-weight: 700; font-size: 10.5px; }
        .no-print { margin-bottom: 18px; display: flex; justify-content: flex-end; gap: 10px; max-width: 1050px; margin-left: auto; margin-right: auto; }
        .btn { padding: 9px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 700; font-size: 13px; text-decoration: none; }
        .btn-primary { background: #0d9488; color: #fff; }
        .btn-outline { background: #fff; color: #334155; border: 1px solid #cbd5e1; }
        @media print {
            body { background: #fff; padding: 0; }
            .report-sheet { box-shadow: none; padding: 10px; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">Print / Save as PDF</button>
        <a href="{{ route('payments.index', request()->query()) }}" class="btn btn-outline">Back to Payment Manage</a>
    </div>

    <div class="report-sheet">
        <div class="top-bar">
            <div class="clinic-brand">
                <h1>{{ $loggedInDoctor ? $loggedInDoctor->name . ' — Clinic Payment Report' : 'Physiotherapy & Clinic CRM — Payment Report' }}</h1>
                <p>Period: <strong>{{ $periodLabel }}</strong> | Generated on: {{ now()->format('d M Y, h:i A') }}</p>
            </div>
            <div style="text-align:right;">
                <div style="font-weight:700; font-size:14px; color:#0f766e;">OFFICIAL FINANCIAL SUMMARY</div>
                <div style="font-size:12px; color:#64748b;">Total Invoices: {{ $invoices->count() }}</div>
            </div>
        </div>

        <div class="kpi-row">
            <div class="kpi-box">
                <div class="label">Total Billed Amount</div>
                <div class="val">₹{{ number_format($totalBilled, 2) }}</div>
            </div>
            <div class="kpi-box">
                <div class="label">Total Paid / Collected</div>
                <div class="val" style="color:#15803d;">₹{{ number_format($totalCollected, 2) }}</div>
            </div>
            <div class="kpi-box">
                <div class="label">Total Unpaid / Pending Due</div>
                <div class="val" style="color:#dc2626;">₹{{ number_format($totalDue, 2) }}</div>
            </div>
            <div class="kpi-box">
                <div class="label">Collection Ratio</div>
                <div class="val" style="color:#0284c7;">
                    {{ $totalBilled > 0 ? round(($totalCollected / $totalBilled) * 100, 1) : 100 }}%
                </div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 18px;">
            <div>
                <h3>Clinic-Wise Summary</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Clinic</th>
                            <th>Bills</th>
                            <th>Billed</th>
                            <th>Paid</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clinicWiseSummary as $c)
                            <tr>
                                <td><strong>{{ $c['clinic_name'] }}</strong></td>
                                <td>{{ $c['invoices_count'] }}</td>
                                <td>₹{{ number_format($c['total_billed'], 0) }}</td>
                                <td style="color:#15803d; font-weight:700;">₹{{ number_format($c['total_paid'], 0) }}</td>
                                <td style="color:#dc2626; font-weight:700;">₹{{ number_format($c['total_due'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                <h3>Category-Wise Summary</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Bills</th>
                            <th>Billed</th>
                            <th>Paid</th>
                            <th>Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryWiseSummary as $cat)
                            <tr>
                                <td><strong>{{ $cat['category_name'] }}</strong></td>
                                <td>{{ $cat['invoices_count'] }}</td>
                                <td>₹{{ number_format($cat['total_billed'], 0) }}</td>
                                <td style="color:#15803d; font-weight:700;">₹{{ number_format($cat['total_paid'], 0) }}</td>
                                <td style="color:#dc2626; font-weight:700;">₹{{ number_format($cat['total_due'], 0) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No records</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <h3>Patient-Wise Paid & Unpaid Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Mobile</th>
                    <th>Bills</th>
                    <th>Total Billed (₹)</th>
                    <th>Paid Amount (₹)</th>
                    <th>Unpaid / Due (₹)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patientWiseSummary as $pt)
                    <tr>
                        <td>{{ $pt['patient_code'] }}</td>
                        <td><strong>{{ $pt['patient_name'] }}</strong></td>
                        <td>{{ $pt['mobile'] }}</td>
                        <td>{{ $pt['invoices_count'] }}</td>
                        <td>₹{{ number_format($pt['total_billed'], 2) }}</td>
                        <td style="color:#15803d; font-weight:700;">₹{{ number_format($pt['total_paid'], 2) }}</td>
                        <td style="color:#dc2626; font-weight:700;">₹{{ number_format($pt['total_due'], 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No patient records found</td></tr>
                @endforelse
            </tbody>
        </table>

        <h3>Detailed Invoice & Payment Ledger ({{ $invoices->count() }} Records)</h3>
        <table>
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Clinic / Category</th>
                    <th>Total Bill</th>
                    <th>Paid</th>
                    <th>Unpaid Due</th>
                    <th>Status</th>
                    <th>Mode</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $inv)
                    @php
                        $cl = $inv->appointment?->clinic ?? $inv->appointments->first()?->clinic;
                        $ct = $inv->appointment?->category ?? $inv->appointments->first()?->category;
                    @endphp
                    <tr>
                        <td><strong>{{ $inv->invoice_no }}</strong></td>
                        <td>{{ $inv->invoice_date ? $inv->invoice_date->format('d M Y') : '-' }}</td>
                        <td>{{ $inv->patient?->full_name ?? 'Walk-in' }} ({{ $inv->patient?->patient_id ?? '-' }})</td>
                        <td>{{ $cl?->name ?? 'General' }} / {{ $ct?->name ?? 'Physio' }}</td>
                        <td>₹{{ number_format((float)$inv->total_amount, 2) }}</td>
                        <td style="color:#15803d; font-weight:700;">₹{{ number_format((float)$inv->paid_amount, 2) }}</td>
                        <td style="color:#dc2626; font-weight:700;">₹{{ number_format((float)$inv->due_amount, 2) }}</td>
                        <td>
                            @if($inv->payment_status === 'paid' || $inv->due_amount <= 0)
                                <span class="badge-paid">PAID</span>
                            @elseif($inv->payment_status === 'partially_paid')
                                <span class="badge-partial">PARTIAL</span>
                            @else
                                <span class="badge-due">UNPAID</span>
                            @endif
                        </td>
                        <td>{{ $inv->payment_method ?: '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center;">No invoices found for this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
