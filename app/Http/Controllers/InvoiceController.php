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
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Billing and payment records are restricted to Doctors only.');
        }

        $status = $request->get('status');
        $search = $request->get('search');
        $date = $request->get('date');

        $query = Invoice::with(['patient', 'doctor', 'transactions']);

        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);
        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $docId = $loggedInDoctor->id;
            $clinicIds = $loggedInDoctor->allClinicIds();
            $query->where(function ($q) use ($docId, $clinicIds) {
                $q->where('doctor_id', $docId)
                  ->orWhereHas('patient', fn($pq) => $pq->where('doctor_id', $docId))
                  ->orWhereHas('appointment', function ($aq) use ($docId, $clinicIds) {
                      $aq->where('doctor_id', $docId);
                      if (!empty($clinicIds)) {
                          $aq->orWhereIn('clinic_id', $clinicIds);
                      }
                  });
            });
        }

        if (!empty($status)) {
            $query->where('payment_status', $status);
        }

        if (!empty($search)) {
            $query->where(function ($outer) use ($search) {
                $outer->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('patient_id', 'like', "%{$search}%");
                })->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        if (!empty($date)) {
            $query->where('invoice_date', $date);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate(15)->withQueryString();

        $statsQuery = Invoice::query();
        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $docId = $loggedInDoctor->id;
            $clinicIds = $loggedInDoctor->allClinicIds();
            $statsQuery->where(function ($q) use ($docId, $clinicIds) {
                $q->where('doctor_id', $docId)
                  ->orWhereHas('patient', fn($pq) => $pq->where('doctor_id', $docId))
                  ->orWhereHas('appointment', function ($aq) use ($docId, $clinicIds) {
                      $aq->where('doctor_id', $docId);
                      if (!empty($clinicIds)) {
                          $aq->orWhereIn('clinic_id', $clinicIds);
                      }
                  });
            });
        }

        $totalBilled = (clone $statsQuery)->sum('total_amount');
        $totalCollected = (clone $statsQuery)->sum('paid_amount');
        $totalDue = (clone $statsQuery)->sum('due_amount');
        $unpaidCount = (clone $statsQuery)->whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->count();

        $patients = $loggedInDoctor 
            ? Patient::where('doctor_id', $loggedInDoctor->id)->orWhereNull('doctor_id')->orderBy('first_name')->get()
            : Patient::orderBy('first_name')->get();
        $doctors = $loggedInDoctor ? collect([$loggedInDoctor]) : Doctor::all();

        return view('invoices.index', compact(
            'invoices', 'status', 'search', 'date',
            'totalBilled', 'totalCollected', 'totalDue', 'unpaidCount',
            'patients', 'doctors'
        ));
    }

    public function show($id)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $invoice = Invoice::with(['patient.clinic', 'doctor', 'items', 'transactions', 'appointment.clinic', 'appointments.clinic'])->findOrFail($id);
        $clinic = $invoice->appointment?->clinic
            ?? $invoice->appointments->first()?->clinic
            ?? $invoice->patient?->clinic
            ?? Clinic::first()
            ?? new Clinic();

        return view('invoices.show', compact('invoice', 'clinic'));
    }

    public function print($id)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            abort(403);
        }

        $invoice = Invoice::with(['patient.clinic', 'doctor', 'items', 'transactions', 'appointment.clinic', 'appointments.clinic'])->findOrFail($id);
        $clinic = $invoice->appointment?->clinic
            ?? $invoice->appointments->first()?->clinic
            ?? $invoice->patient?->clinic
            ?? Clinic::first()
            ?? new Clinic();

        return view('invoices.print', compact('invoice', 'clinic'));
    }

    public function create(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

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
            'payment_date' => 'nullable|date',
            'transaction_reference' => 'nullable|string',
            'reference_no' => 'nullable|string',
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
            'payment_date' => $validated['payment_date'] ?? now()->toDateString(),
            'transaction_reference' => $validated['transaction_reference'] ?? $validated['reference_no'] ?? null,
            'collected_by' => auth()->user()->name ?? 'Doctor',
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
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            return redirect()->route('dashboard')->with('error', 'Access denied.');
        }

        $search = $request->get('search');

        $query = Invoice::with(['patient.clinic', 'appointment.clinic', 'appointments.clinic', 'transactions', 'doctor'])
            ->whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])
            ->where('due_amount', '>', 0);

        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);
        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $docId = $loggedInDoctor->id;
            $clinicIds = $loggedInDoctor->allClinicIds();
            $query->where(function ($q) use ($docId, $clinicIds) {
                $q->where('doctor_id', $docId)
                  ->orWhereHas('patient', fn($pq) => $pq->where('doctor_id', $docId))
                  ->orWhereHas('appointment', function ($aq) use ($docId, $clinicIds) {
                      $aq->where('doctor_id', $docId);
                      if (!empty($clinicIds)) {
                          $aq->orWhereIn('clinic_id', $clinicIds);
                      }
                  });
            });
        }

        if (!empty($search)) {
            $query->where(function ($outer) use ($search) {
                $outer->whereHas('patient', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('patient_id', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                })->orWhere('invoice_no', 'like', "%{$search}%");
            });
        }

        $dueInvoices = $query->orderBy('invoice_date', 'asc')->paginate(15)->withQueryString();

        $totalOutstandingDue = (clone $query)->sum('due_amount');
        $patientsWithDuesCount = Patient::whereHas('invoices', function ($q) {
            $q->whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->where('due_amount', '>', 0);
        })->count();

        return view('invoices.dues', compact('dueInvoices', 'totalOutstandingDue', 'patientsWithDuesCount', 'search'));
    }

    public function printReceipt($transactionId)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            abort(403);
        }

        $transaction = PaymentTransaction::with(['invoice.items', 'invoice.doctor', 'patient'])->findOrFail($transactionId);
        $clinic = Clinic::first() ?? new Clinic();

        return view('invoices.receipt', compact('transaction', 'clinic'));
    }

    /**
     * Update Invoice Details (Discount, Charges, Date, Notes)
     */
    public function update(Request $request, $id)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            abort(403);
        }

        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'discount' => 'nullable|numeric|min:0',
            'additional_charges' => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
            'invoice_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['discount'])) {
            $invoice->discount = $validated['discount'];
        }
        if (isset($validated['additional_charges'])) {
            $invoice->additional_charges = $validated['additional_charges'];
        }
        if (isset($validated['subtotal']) && $invoice->items()->count() === 0) {
            $invoice->subtotal = $validated['subtotal'];
        }
        if (!empty($validated['invoice_date'])) {
            $invoice->invoice_date = $validated['invoice_date'];
        }
        if (isset($validated['notes'])) {
            $invoice->notes = $validated['notes'];
        }

        $invoice->recalculate();

        AuditLog::record('Invoice Updated', 'Invoice', $invoice->invoice_no, "Updated invoice #{$invoice->invoice_no} (Discount: {$invoice->discount}, Charges: {$invoice->additional_charges}, Total: ₹" . number_format($invoice->total_amount, 2) . ")");

        return back()->with('success', "Invoice #{$invoice->invoice_no} updated successfully!");
    }

    /**
     * Edit Payment Transaction (Amount, Date, Method, Reference, Notes)
     */
    public function updateTransaction(Request $request, $id)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            abort(403);
        }

        $transaction = PaymentTransaction::with('invoice')->findOrFail($id);
        $invoice = $transaction->invoice;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,UPI,Card,Bank Transfer,Online Payment,Other',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Check if new amount would exceed total allowed (invoice total - other payments)
        $otherPaid = $invoice->transactions()->where('id', '!=', $transaction->id)->sum('amount');
        if (($otherPaid + $validated['amount']) > ($invoice->total_amount + 0.01)) {
            return back()->withErrors(['amount' => "Updated amount cannot make total paid exceed the invoice total of ₹" . number_format($invoice->total_amount, 2)]);
        }

        $oldAmount = $transaction->amount;
        $transaction->update([
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'payment_date' => $validated['payment_date'],
            'transaction_reference' => $validated['transaction_reference'] ?? null,
            'notes' => $validated['notes'] ?? $transaction->notes,
        ]);

        $invoice->recalculate();

        AuditLog::record('Payment Transaction Edited', 'Invoice', $invoice->invoice_no, "Edited transaction #{$transaction->receipt_no} from ₹" . number_format($oldAmount, 2) . " to ₹" . number_format($transaction->amount, 2) . " ({$transaction->payment_method})");

        return back()->with('success', "Payment receipt #{$transaction->receipt_no} updated successfully! Invoice balances recalculated.");
    }

    /**
     * Delete Mistaken Payment Transaction
     */
    public function deleteTransaction($id)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (\App\Models\User::isStaffRole($currentRole)) {
            abort(403);
        }

        $transaction = PaymentTransaction::with('invoice')->findOrFail($id);
        $invoice = $transaction->invoice;
        $recNo = $transaction->receipt_no;
        $amount = $transaction->amount;

        $transaction->delete();
        $invoice->recalculate();

        AuditLog::record('Payment Transaction Deleted', 'Invoice', $invoice->invoice_no, "Deleted transaction #{$recNo} of ₹" . number_format($amount, 2));

        return back()->with('success', "Payment transaction #{$recNo} removed! Invoice balances recalculated.");
    }
}
