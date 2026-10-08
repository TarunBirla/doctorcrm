<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\PaymentTransaction;
use App\Models\Expense;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\FollowUp;
use App\Models\Doctor;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    private function resolveDateRange(Request $request): array
    {
        $range = $request->get('range', 'this_month');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        switch ($range) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
                $label = 'Today (' . $start->format('d M Y') . ')';
                break;
            case 'yesterday':
                $start = Carbon::yesterday()->startOfDay();
                $end = Carbon::yesterday()->endOfDay();
                $label = 'Yesterday (' . $start->format('d M Y') . ')';
                break;
            case 'this_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $label = 'This Week (' . $start->format('d M') . ' - ' . $end->format('d M Y') . ')';
                break;
            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek();
                $end = Carbon::now()->subWeek()->endOfWeek();
                $label = 'Last Week (' . $start->format('d M') . ' - ' . $end->format('d M Y') . ')';
                break;
            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth();
                $end = Carbon::now()->subMonth()->endOfMonth();
                $label = 'Last Month (' . $start->format('F Y') . ')';
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $label = 'This Year (' . $start->format('Y') . ')';
                break;
            case 'custom':
                $start = $customStart ? Carbon::parse($customStart)->startOfDay() : Carbon::today()->startOfDay();
                $end = $customEnd ? Carbon::parse($customEnd)->endOfDay() : Carbon::today()->endOfDay();
                $label = 'Custom Range (' . $start->format('d M Y') . ' - ' . $end->format('d M Y') . ')';
                break;
            case 'this_month':
            default:
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $label = 'This Month (' . $start->format('F Y') . ')';
                break;
        }

        return [$start, $end, $range, $label];
    }

    public function financial(Request $request)
    {
        [$start, $end, $range, $label] = $this->resolveDateRange($request);

        $transactions = PaymentTransaction::with(['invoice.patient', 'invoice.doctor'])
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $totalCollected = $transactions->sum('amount');
        $cashTotal = $transactions->where('payment_method', 'Cash')->sum('amount');
        $upiTotal = $transactions->where('payment_method', 'UPI')->sum('amount');
        $cardTotal = $transactions->where('payment_method', 'Card')->sum('amount');
        $bankTotal = $transactions->where('payment_method', 'Bank Transfer')->sum('amount');
        $otherTotal = $transactions->whereNotIn('payment_method', ['Cash', 'UPI', 'Card', 'Bank Transfer'])->sum('amount');

        $invoices = Invoice::whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])->get();
        $totalBilled = $invoices->sum('total_amount');
        $totalDue = $invoices->sum('due_amount');

        $expenses = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])->get();
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalCollected - $totalExpenses;

        // Group expenses by category
        $expensesByCategory = $expenses->groupBy('category')->map(fn($group) => $group->sum('amount'));

        // Doctor Revenue breakdown
        $doctorRevenue = Invoice::with('doctor')
            ->whereBetween('invoice_date', [$start->toDateString(), $end->toDateString()])
            ->get()
            ->groupBy('doctor.name')
            ->map(function ($invs) {
                return [
                    'billed' => $invs->sum('total_amount'),
                    'collected' => $invs->sum('paid_amount'),
                    'due' => $invs->sum('due_amount'),
                    'count' => $invs->count(),
                ];
            });

        return view('reports.financial', compact(
            'start', 'end', 'range', 'label',
            'totalBilled', 'totalCollected', 'totalDue', 'totalExpenses', 'netProfit',
            'cashTotal', 'upiTotal', 'cardTotal', 'bankTotal', 'otherTotal',
            'expensesByCategory', 'doctorRevenue', 'transactions', 'expenses'
        ));
    }

    public function patients(Request $request)
    {
        $totalPatients = Patient::count();
        $maleCount = Patient::where('gender', 'Male')->count();
        $femaleCount = Patient::where('gender', 'Female')->count();
        $otherCount = Patient::where('gender', 'Other')->count();

        // Age groups
        $ageGroups = [
            'Under 18' => Patient::where('age', '<', 18)->count(),
            '18 - 35' => Patient::whereBetween('age', [18, 35])->count(),
            '36 - 50' => Patient::whereBetween('age', [36, 50])->count(),
            '51 - 65' => Patient::whereBetween('age', [51, 65])->count(),
            'Over 65' => Patient::where('age', '>', 65)->count(),
        ];

        // Blood groups
        $bloodGroups = Patient::selectRaw('blood_group, count(*) as count')
            ->whereNotNull('blood_group')
            ->groupBy('blood_group')
            ->pluck('count', 'blood_group')
            ->toArray();

        // Registration trend (last 6 months)
        $registrationMonths = [];
        $registrationCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $registrationMonths[] = $m->format('M Y');
            $registrationCounts[] = Patient::whereMonth('created_at', $m->month)->whereYear('created_at', $m->year)->count();
        }

        return view('reports.patients', compact(
            'totalPatients', 'maleCount', 'femaleCount', 'otherCount',
            'ageGroups', 'bloodGroups', 'registrationMonths', 'registrationCounts'
        ));
    }

    public function appointments(Request $request)
    {
        [$start, $end, $range, $label] = $this->resolveDateRange($request);

        $appointments = Appointment::with(['patient', 'doctor'])
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $totalAppointments = $appointments->count();
        $completedCount = $appointments->where('status', 'completed')->count();
        $cancelledCount = $appointments->where('status', 'cancelled')->count();
        $noShowCount = $appointments->where('status', 'no_show')->count();
        $waitingCount = $appointments->where('status', 'waiting')->count();

        $newTypeCount = $appointments->where('appointment_type', 'new')->count();
        $followUpTypeCount = $appointments->where('appointment_type', 'follow_up')->count();
        $revisitTypeCount = $appointments->where('appointment_type', 'revisit')->count();
        $emergencyTypeCount = $appointments->where('appointment_type', 'emergency')->count();

        return view('reports.appointments', compact(
            'start', 'end', 'range', 'label',
            'appointments', 'totalAppointments', 'completedCount',
            'cancelledCount', 'noShowCount', 'waitingCount',
            'newTypeCount', 'followUpTypeCount', 'revisitTypeCount', 'emergencyTypeCount'
        ));
    }

    public function exportFinancial(Request $request)
    {
        [$start, $end] = $this->resolveDateRange($request);

        $transactions = PaymentTransaction::with(['invoice.patient'])
            ->whereBetween('payment_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $response = new StreamedResponse(function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Receipt No', 'Transaction No', 'Invoice No', 'Patient ID', 'Patient Name', 'Date', 'Method', 'Amount (INR)', 'Reference']);

            foreach ($transactions as $txn) {
                fputcsv($handle, [
                    $txn->receipt_no,
                    $txn->transaction_no,
                    $txn->invoice->invoice_no ?? '',
                    $txn->patient->patient_id ?? '',
                    $txn->patient->full_name ?? '',
                    $txn->payment_date->format('Y-m-d'),
                    $txn->payment_method,
                    $txn->amount,
                    $txn->transaction_reference ?? '',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="financial_collections_' . date('Ymd_His') . '.csv"');

        return $response;
    }
}
