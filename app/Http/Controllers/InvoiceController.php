<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentTransaction;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\AuditLog;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $search = $request->get('search');
        $date = $request->get('date');

        $query = Invoice::with(['patient', 'doctor', 'transactions']);

        if (!empty($status)) {
            $query->where('payment_status', $status);
        }

        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%");
            })->orWhere('invoice_no', 'like', "%{$search}%");
        }

        if (!empty($date)) {
            $query->where('invoice_date', $date);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15)->withQueryString();

        $totalBilled = Invoice::sum('total_amount');
        $totalCollected = Invoice::sum('paid_amount');
        $totalDue = Invoice::sum('due_amount');
        $unpaidCount = Invoice::whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->count();

        $patients = Patient::orderBy('first_name')->get();
        $doctors = Doctor::all();

        return view('invoices.index', compact(
            'invoices', 'status', 'search', 'date',
            'totalBilled', 'totalCollected', 'totalDue', 'unpaidCount',
            'patients', 'doctors'
        ));
    }

    public function show($id)
    {
        $invoice = Invoice::with(['patient', 'doctor', 'items', 'transactions', 'appointment'])->findOrFail($id);
        $clinic = Clinic::first() ?? new Clinic();

        return view('invoices.show', compact('invoice', 'clinic'));
    }

    public function print($id)
    {
        $invoice = Invoice::with(['patient', 'doctor', 'items', 'transactions', 'appointment'])->findOrFail($id);
        $clinic = Clinic::first() ?? new Clinic();

        return view('invoices.print', compact('invoice', 'clinic'));
    }

    public function create(Request $request)
    {
        $patientId = $request->get('patient_id');
        $patients = Patient::orderBy('first_name')->get();
        $doctors = Doctor::all();
        $selectedPatient = $patientId ? Patient::find($patientId) : null;

        return view('invoices.create', compact('patients', 'doctors', 'selectedPatient'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'invoice_date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'additional_charges' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            // Initial payment optional
            'paid_now' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'transaction_reference' => 'nullable|string',
        ]);

        $invoiceNo = Invoice::generateInvoiceNo();

        $invoice = Invoice::create([
            'invoice_no' => $invoiceNo,
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'invoice_date' => $validated['invoice_date'],
            'subtotal' => 0,
            'discount' => $validated['discount'] ?? 0,
            'additional_charges' => $validated['additional_charges'] ?? 0,
            'total_amount' => 0,
            'paid_amount' => 0,
            'due_amount' => 0,
            'payment_status' => 'unpaid',
            'notes' => $validated['notes'] ?? null,
        ]);

        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $total = $item['quantity'] * $item['unit_price'];
            $subtotal += $total;
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $total,
            ]);
        }

        $invoice->subtotal = $subtotal;
        $invoice->save();

        // Process immediate initial payment if collected
        if (!empty($validated['paid_now']) && $validated['paid_now'] > 0) {
            $txnNo = PaymentTransaction::generateTransactionNo();
            $recNo = PaymentTransaction::generateReceiptNo();

            PaymentTransaction::create([
                'invoice_id' => $invoice->id,
                'patient_id' => $invoice->patient_id,
                'transaction_no' => $txnNo,
                'amount' => $validated['paid_now'],
                'payment_method' => $validated['payment_method'] ?? 'Cash',
                'payment_date' => $validated['invoice_date'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'collected_by' => auth()->user()->name ?? 'Front Desk Staff',
                'receipt_no' => $recNo,
                'notes' => 'Initial payment at invoice creation',
            ]);
        }

        // Strictly recalculate totals and payment status
        $invoice->recalculate();

        AuditLog::record('Invoice Generated', 'Invoice', $invoiceNo, "Generated invoice #{$invoiceNo} of ₹" . number_format($invoice->total_amount, 2));

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', "Invoice #{$invoiceNo} generated successfully!");
    }

    public function recordPayment(Request $request, $id)
    {
        $invoice = Invoice::with('patient')->findOrFail($id);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:Cash,UPI,Card,Bank Transfer,Online Payment,Other',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validated['amount'] > ($invoice->due_amount + 0.01)) {
            return back()->withErrors(['amount' => "Payment amount cannot exceed the pending due of ₹" . number_format($invoice->due_amount, 2)]);
        }

        $txnNo = PaymentTransaction::generateTransactionNo();
        $recNo = PaymentTransaction::generateReceiptNo();

        $transaction = PaymentTransaction::create([
            'invoice_id' => $invoice->id,
            'patient_id' => $invoice->patient_id,
            'transaction_no' => $txnNo,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'collected_by' => auth()->user()->name ?? 'Receptionist',
            'receipt_no' => $recNo,
            'notes' => $validated['notes'] ?? 'Due collection',
        ]);

        // Auto-recalculate invoice balances
        $invoice->recalculate();

        AuditLog::record('Payment Received', 'Invoice', $invoice->invoice_no, "Received ₹" . number_format($transaction->amount, 2) . " via {$transaction->payment_method} against invoice #{$invoice->invoice_no}");

        return back()->with('success', "Payment of ₹" . number_format($transaction->amount, 2) . " collected successfully! Receipt #{$recNo} generated.");
    }

    public function duePayments(Request $request)
    {
        $search = $request->get('search');

        $query = Invoice::with(['patient', 'transactions', 'doctor'])
            ->whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])
            ->where('due_amount', '>', 0);

        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            })->orWhere('invoice_no', 'like', "%{$search}%");
        }

        $dueInvoices = $query->orderBy('invoice_date', 'asc')->paginate(15)->withQueryString();

        $totalOutstandingDue = Invoice::whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->sum('due_amount');
        $patientsWithDuesCount = Patient::whereHas('invoices', function ($q) {
            $q->whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->where('due_amount', '>', 0);
        })->count();

        return view('invoices.dues', compact('dueInvoices', 'totalOutstandingDue', 'patientsWithDuesCount', 'search'));
    }

    public function printReceipt($transactionId)
    {
        $transaction = PaymentTransaction::with(['invoice.items', 'invoice.doctor', 'patient'])->findOrFail($transactionId);
        $clinic = Clinic::first() ?? new Clinic();

        return view('invoices.receipt', compact('transaction', 'clinic'));
    }
}
