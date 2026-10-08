<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentTransaction;
use App\Models\Invoice;
use App\Models\Patient;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $method = $request->get('method');
        $search = $request->get('search');
        $date = $request->get('date');

        $query = PaymentTransaction::with(['invoice', 'patient']);

        if (!empty($method)) {
            $query->where('payment_method', $method);
        }

        if (!empty($date)) {
            $query->where('payment_date', $date);
        }

        if (!empty($search)) {
            $query->whereHas('patient', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%");
            })->orWhere('transaction_no', 'like', "%{$search}%")
              ->orWhere('receipt_no', 'like', "%{$search}%");
        }

        $transactions = $query->orderBy('payment_date', 'desc')->paginate(15)->withQueryString();

        $totalCollected = PaymentTransaction::sum('amount');
        $cashTotal = PaymentTransaction::where('payment_method', 'Cash')->sum('amount');
        $upiTotal = PaymentTransaction::where('payment_method', 'UPI')->sum('amount');
        $cardTotal = PaymentTransaction::where('payment_method', 'Card')->sum('amount');

        return view('payments.index', compact(
            'transactions', 'method', 'search', 'date',
            'totalCollected', 'cashTotal', 'upiTotal', 'cardTotal'
        ));
    }
}
