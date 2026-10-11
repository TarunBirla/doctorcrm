<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\PaymentTransaction;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\TreatmentCategory;
use App\Models\User;

class PaymentController extends Controller
{
    /**
     * Build the base Invoice query scoped to the current Doctor (or all for Super Admin)
     */
    protected function buildScopedInvoiceQuery(?Doctor $loggedInDoctor, string $currentRole)
    {
        $query = Invoice::with([
            'patient',
            'doctor',
            'appointment.clinic',
            'appointment.category',
            'appointments.clinic',
            'appointments.category',
            'items',
            'transactions',
        ]);

        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $clinicIds = $loggedInDoctor->allClinicIds();
            $docId = $loggedInDoctor->id;

            $query->where(function ($q) use ($docId, $clinicIds) {
                $q->where('doctor_id', $docId)
                  ->orWhereHas('patient', function ($pq) use ($docId) {
                      $pq->where('doctor_id', $docId);
                  })
                  ->orWhereHas('appointment', function ($aq) use ($docId, $clinicIds) {
                      $aq->where('doctor_id', $docId);
                      if (!empty($clinicIds)) {
                          $aq->orWhereIn('clinic_id', $clinicIds);
                      }
                  });
            });
        }

        return $query;
    }

    /**
     * Apply request filters (period, date, month, year, range, patient, clinic, category, status, method, search)
     */
    protected function applyFilters($query, Request $request): array
    {
        $period = $request->get('period', 'all');
        $date = $request->get('date');
        $month = $request->get('month', now()->format('Y-m'));
        $year = $request->get('year', now()->format('Y'));
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $patientId = $request->get('patient_id');
        $clinicId = $request->get('clinic_id');
        $categoryId = $request->get('category_id');
        $paymentStatus = $request->get('payment_status', 'all');
        $method = $request->get('method');
        $search = trim((string) $request->get('search', ''));

        $periodLabel = 'All Time Payments';

        // If user explicitly picked a specific date and didn't set period
        if (!empty($date) && $period === 'all') {
            $period = 'custom_date';
        }

        switch ($period) {
            case 'today':
                $query->whereDate('invoice_date', Carbon::today()->toDateString());
                $periodLabel = 'Today (' . Carbon::today()->format('d M Y') . ')';
                break;

            case 'yesterday':
                $query->whereDate('invoice_date', Carbon::yesterday()->toDateString());
                $periodLabel = 'Yesterday (' . Carbon::yesterday()->format('d M Y') . ')';
                break;

            case 'this_week':
                $start = Carbon::now()->startOfWeek()->toDateString();
                $end = Carbon::now()->endOfWeek()->toDateString();
                $query->whereBetween('invoice_date', [$start, $end]);
                $periodLabel = 'This Week (' . Carbon::parse($start)->format('d M') . ' - ' . Carbon::parse($end)->format('d M Y') . ')';
                break;

            case 'last_week':
                $start = Carbon::now()->subWeek()->startOfWeek()->toDateString();
                $end = Carbon::now()->subWeek()->endOfWeek()->toDateString();
                $query->whereBetween('invoice_date', [$start, $end]);
                $periodLabel = 'Last Week (' . Carbon::parse($start)->format('d M') . ' - ' . Carbon::parse($end)->format('d M Y') . ')';
                break;

            case 'this_month':
                $start = Carbon::now()->startOfMonth()->toDateString();
                $end = Carbon::now()->endOfMonth()->toDateString();
                $query->whereBetween('invoice_date', [$start, $end]);
                $periodLabel = 'This Month (' . Carbon::now()->format('F Y') . ')';
                break;

            case 'last_month':
                $start = Carbon::now()->subMonth()->startOfMonth()->toDateString();
                $end = Carbon::now()->subMonth()->endOfMonth()->toDateString();
                $query->whereBetween('invoice_date', [$start, $end]);
                $periodLabel = 'Last Month (' . Carbon::now()->subMonth()->format('F Y') . ')';
                break;

            case 'this_year':
                $start = Carbon::now()->startOfYear()->toDateString();
                $end = Carbon::now()->endOfYear()->toDateString();
                $query->whereBetween('invoice_date', [$start, $end]);
                $periodLabel = 'This Year (' . Carbon::now()->format('Y') . ')';
                break;

            case 'custom_date':
                if (!empty($date)) {
                    $query->whereDate('invoice_date', $date);
                    $periodLabel = 'Date: ' . Carbon::parse($date)->format('d M Y');
                }
                break;

            case 'custom_month':
                if (!empty($month) && preg_match('/^\d{4}-\d{2}$/', $month)) {
                    $parsedMonth = Carbon::createFromFormat('Y-m', $month);
                    $query->whereBetween('invoice_date', [
                        $parsedMonth->copy()->startOfMonth()->toDateString(),
                        $parsedMonth->copy()->endOfMonth()->toDateString(),
                    ]);
                    $periodLabel = 'Month: ' . $parsedMonth->format('F Y');
                }
                break;

            case 'custom_year':
                if (!empty($year) && preg_match('/^\d{4}$/', (string) $year)) {
                    $query->whereBetween('invoice_date', ["{$year}-01-01", "{$year}-12-31"]);
                    $periodLabel = 'Year: ' . $year;
                }
                break;

            case 'custom_range':
                if (!empty($startDate) && !empty($endDate)) {
                    $query->whereBetween('invoice_date', [$startDate, $endDate]);
                    $periodLabel = Carbon::parse($startDate)->format('d M Y') . ' to ' . Carbon::parse($endDate)->format('d M Y');
                } elseif (!empty($startDate)) {
                    $query->whereDate('invoice_date', '>=', $startDate);
                    $periodLabel = 'From ' . Carbon::parse($startDate)->format('d M Y');
                } elseif (!empty($endDate)) {
                    $query->whereDate('invoice_date', '<=', $endDate);
                    $periodLabel = 'Until ' . Carbon::parse($endDate)->format('d M Y');
                }
                break;
        }

        // Patient-wise filter
        if (!empty($patientId)) {
            $query->where('patient_id', $patientId);
        }

        // Clinic-wise filter
        if (!empty($clinicId)) {
            $query->where(function ($q) use ($clinicId) {
                $q->whereHas('appointment', function ($aq) use ($clinicId) {
                    $aq->where('clinic_id', $clinicId);
                })->orWhereHas('appointments', function ($aq) use ($clinicId) {
                    $aq->where('clinic_id', $clinicId);
                });
            });
        }

        // Category-wise filter
        if (!empty($categoryId)) {
            $query->where(function ($q) use ($categoryId) {
                $q->whereHas('appointment', function ($aq) use ($categoryId) {
                    $aq->where('category_id', $categoryId);
                })->orWhereHas('appointments', function ($aq) use ($categoryId) {
                    $aq->where('category_id', $categoryId);
                });
            });
        }

        // Payment Status filter (paid, unpaid, partially_paid, due)
        if (!empty($paymentStatus) && $paymentStatus !== 'all') {
            if ($paymentStatus === 'due') {
                $query->whereIn('payment_status', ['unpaid', 'partially_paid'])->where('due_amount', '>', 0);
            } else {
                $query->where('payment_status', $paymentStatus);
            }
        }

        // Payment Method filter
        if (!empty($method)) {
            $query->where(function ($q) use ($method) {
                $q->where('payment_method', $method)
                  ->orWhereHas('transactions', function ($tq) use ($method) {
                      $tq->where('payment_method', $method);
                  });
            });
        }

        // Search filter (Patient Name, Patient ID, Mobile, Invoice No)
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($pq) use ($search) {
                      $pq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('patient_id', 'like', "%{$search}%")
                        ->orWhere('mobile', 'like', "%{$search}%");
                  });
            });
        }

        return [
            'period' => $period,
            'periodLabel' => $periodLabel,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'patientId' => $patientId,
            'clinicId' => $clinicId,
            'categoryId' => $categoryId,
            'paymentStatus' => $paymentStatus,
            'method' => $method,
            'search' => $search,
        ];
    }

    /**
     * Unified Payment Manage & Comprehensive Reports Hub
     */
    public function index(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (User::isStaffRole($currentRole)) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Payment management is restricted to Doctors only.');
        }

        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);
        $activeTab = $request->get('tab', 'all_payments'); // 'all_payments', 'patient_wise', 'reports'

        // 1. Base Scoped Query (before period/filter, used for Day/Week/Month/Year quick snapshot cards)
        $baseScoped = $this->buildScopedInvoiceQuery($loggedInDoctor, $currentRole);

        $todayStr = Carbon::today()->toDateString();
        $weekStart = Carbon::now()->startOfWeek()->toDateString();
        $weekEnd = Carbon::now()->endOfWeek()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd = Carbon::now()->endOfMonth()->toDateString();
        $yearStart = Carbon::now()->startOfYear()->toDateString();
        $yearEnd = Carbon::now()->endOfYear()->toDateString();

        $periodSnapshots = [
            'today' => [
                'billed' => (float) (clone $baseScoped)->whereDate('invoice_date', $todayStr)->sum('total_amount'),
                'paid' => (float) (clone $baseScoped)->whereDate('invoice_date', $todayStr)->sum('paid_amount'),
                'due' => (float) (clone $baseScoped)->whereDate('invoice_date', $todayStr)->sum('due_amount'),
                'count' => (clone $baseScoped)->whereDate('invoice_date', $todayStr)->count(),
            ],
            'week' => [
                'billed' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$weekStart, $weekEnd])->sum('total_amount'),
                'paid' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$weekStart, $weekEnd])->sum('paid_amount'),
                'due' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$weekStart, $weekEnd])->sum('due_amount'),
                'count' => (clone $baseScoped)->whereBetween('invoice_date', [$weekStart, $weekEnd])->count(),
            ],
            'month' => [
                'billed' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$monthStart, $monthEnd])->sum('total_amount'),
                'paid' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$monthStart, $monthEnd])->sum('paid_amount'),
                'due' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$monthStart, $monthEnd])->sum('due_amount'),
                'count' => (clone $baseScoped)->whereBetween('invoice_date', [$monthStart, $monthEnd])->count(),
            ],
            'year' => [
                'billed' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$yearStart, $yearEnd])->sum('total_amount'),
                'paid' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$yearStart, $yearEnd])->sum('paid_amount'),
                'due' => (float) (clone $baseScoped)->whereBetween('invoice_date', [$yearStart, $yearEnd])->sum('due_amount'),
                'count' => (clone $baseScoped)->whereBetween('invoice_date', [$yearStart, $yearEnd])->count(),
            ],
        ];

        // 2. Apply Filters to Main Query
        $filteredQuery = $this->buildScopedInvoiceQuery($loggedInDoctor, $currentRole);
        $filters = $this->applyFilters($filteredQuery, $request);

        // Fetch all matching invoices for accurate KPI & report aggregation
        $allFilteredInvoices = (clone $filteredQuery)->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->get();

        // Paginated Invoices for the main table
        $invoices = $filteredQuery->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        // 3. Filtered KPI Totals
        $totalBilled = (float) $allFilteredInvoices->sum('total_amount');
        $totalCollected = (float) $allFilteredInvoices->sum('paid_amount');
        $totalDue = (float) $allFilteredInvoices->sum('due_amount');
        $paidCount = $allFilteredInvoices->where('payment_status', 'paid')->count();
        $unpaidCount = $allFilteredInvoices->where('payment_status', 'unpaid')->count();
        $partialCount = $allFilteredInvoices->where('payment_status', 'partially_paid')->count();

        // Payment Method totals across filtered invoices' transactions
        $filteredInvoiceIds = $allFilteredInvoices->pluck('id')->toArray();
        $txnQuery = PaymentTransaction::whereIn('invoice_id', $filteredInvoiceIds);
        $cashTotal = (float) (clone $txnQuery)->where('payment_method', 'Cash')->sum('amount');
        $upiTotal = (float) (clone $txnQuery)->where('payment_method', 'UPI')->sum('amount');
        $cardTotal = (float) (clone $txnQuery)->where('payment_method', 'Card')->sum('amount');
        $bankTotal = (float) (clone $txnQuery)->where('payment_method', 'Bank Transfer')->sum('amount');

        // 4. Patient-Wise Summary (Paid & Unpaid per patient)
        $patientWiseSummary = $allFilteredInvoices->groupBy('patient_id')->map(function ($group) {
            $patient = $group->first()->patient;
            $billed = (float) $group->sum('total_amount');
            $paid = (float) $group->sum('paid_amount');
            $due = (float) $group->sum('due_amount');
            return [
                'patient_id' => $patient?->id,
                'patient_code' => $patient?->patient_id ?? 'N/A',
                'patient_name' => $patient?->full_name ?? 'Walk-in Patient',
                'mobile' => $patient?->mobile ?? '-',
                'invoices_count' => $group->count(),
                'paid_invoices' => $group->where('payment_status', 'paid')->count(),
                'unpaid_invoices' => $group->whereIn('payment_status', ['unpaid', 'partially_paid'])->count(),
                'total_billed' => $billed,
                'total_paid' => $paid,
                'total_due' => $due,
                'last_invoice_date' => $group->max('invoice_date'),
                'status' => $due <= 0.01 ? 'paid' : ($paid <= 0.01 ? 'unpaid' : 'partially_paid'),
            ];
        })->sortByDesc('total_billed')->values();

        // 5. Clinic-Wise Breakdown
        $clinicWiseSummary = $allFilteredInvoices->groupBy(function ($inv) {
            $clinic = $inv->appointment?->clinic ?? $inv->appointments->first()?->clinic;
            return $clinic ? $clinic->name : 'Main / General Clinic';
        })->map(function ($group, $clinicName) {
            return [
                'clinic_name' => $clinicName,
                'invoices_count' => $group->count(),
                'total_billed' => (float) $group->sum('total_amount'),
                'total_paid' => (float) $group->sum('paid_amount'),
                'total_due' => (float) $group->sum('due_amount'),
            ];
        })->sortByDesc('total_billed')->values();

        // 6. Category-Wise Breakdown
        $categoryWiseSummary = $allFilteredInvoices->groupBy(function ($inv) {
            $cat = $inv->appointment?->category ?? $inv->appointments->first()?->category;
            return $cat ? $cat->name : 'General Consultation / Physiotherapy';
        })->map(function ($group, $catName) {
            return [
                'category_name' => $catName,
                'invoices_count' => $group->count(),
                'total_billed' => (float) $group->sum('total_amount'),
                'total_paid' => (float) $group->sum('paid_amount'),
                'total_due' => (float) $group->sum('due_amount'),
            ];
        })->sortByDesc('total_billed')->values();

        // 7. Date-wise & Month-wise Breakdown for Reports Tab
        $dailyReport = $allFilteredInvoices->groupBy(function ($inv) {
            return $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : 'Unknown';
        })->map(function ($group, $dateKey) {
            return [
                'date' => $dateKey,
                'formatted_date' => $dateKey !== 'Unknown' ? Carbon::parse($dateKey)->format('d M Y (D)') : 'Unknown',
                'invoices_count' => $group->count(),
                'total_billed' => (float) $group->sum('total_amount'),
                'total_paid' => (float) $group->sum('paid_amount'),
                'total_due' => (float) $group->sum('due_amount'),
            ];
        })->sortByDesc('date')->values();

        $monthlyReport = $allFilteredInvoices->groupBy(function ($inv) {
            return $inv->invoice_date ? $inv->invoice_date->format('Y-m') : 'Unknown';
        })->map(function ($group, $monthKey) {
            return [
                'month' => $monthKey,
                'formatted_month' => $monthKey !== 'Unknown' ? Carbon::createFromFormat('Y-m', $monthKey)->format('F Y') : 'Unknown',
                'invoices_count' => $group->count(),
                'total_billed' => (float) $group->sum('total_amount'),
                'total_paid' => (float) $group->sum('paid_amount'),
                'total_due' => (float) $group->sum('due_amount'),
            ];
        })->sortByDesc('month')->values();

        // 8. Dropdown Options for Filter Bar
        if ($currentRole === 'doctor' && $loggedInDoctor) {
            $patients = Patient::where('doctor_id', $loggedInDoctor->id)
                ->orWhereIn('id', $allFilteredInvoices->pluck('patient_id')->filter()->unique())
                ->orderBy('first_name')
                ->get();
            $clinicIds = $loggedInDoctor->allClinicIds();
            $clinics = !empty($clinicIds)
                ? Clinic::whereIn('id', $clinicIds)->orderBy('name')->get()
                : Clinic::where('is_active', true)->orderBy('name')->get();
        } else {
            $patients = Patient::orderBy('first_name')->get();
            $clinics = Clinic::orderBy('name')->get();
        }
        $categories = TreatmentCategory::orderBy('name')->get();

        return view('payments.index', array_merge($filters, compact(
            'invoices',
            'allFilteredInvoices',
            'activeTab',
            'periodSnapshots',
            'totalBilled',
            'totalCollected',
            'totalDue',
            'paidCount',
            'unpaidCount',
            'partialCount',
            'cashTotal',
            'upiTotal',
            'cardTotal',
            'bankTotal',
            'patientWiseSummary',
            'clinicWiseSummary',
            'categoryWiseSummary',
            'dailyReport',
            'monthlyReport',
            'patients',
            'clinics',
            'categories',
            'loggedInDoctor',
            'currentRole'
        )));
    }

    /**
     * Printable / Save-as-PDF Comprehensive Payment Report
     */
    public function printReport(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (User::isStaffRole($currentRole)) {
            abort(403);
        }

        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);
        $filteredQuery = $this->buildScopedInvoiceQuery($loggedInDoctor, $currentRole);
        $filters = $this->applyFilters($filteredQuery, $request);

        $invoices = $filteredQuery->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->get();

        $totalBilled = (float) $invoices->sum('total_amount');
        $totalCollected = (float) $invoices->sum('paid_amount');
        $totalDue = (float) $invoices->sum('due_amount');

        $patientWiseSummary = $invoices->groupBy('patient_id')->map(function ($group) {
            $patient = $group->first()->patient;
            return [
                'patient_code' => $patient?->patient_id ?? 'N/A',
                'patient_name' => $patient?->full_name ?? 'Walk-in Patient',
                'mobile' => $patient?->mobile ?? '-',
                'invoices_count' => $group->count(),
                'total_billed' => (float) $group->sum('total_amount'),
                'total_paid' => (float) $group->sum('paid_amount'),
                'total_due' => (float) $group->sum('due_amount'),
            ];
        })->sortByDesc('total_billed')->values();

        $clinicWiseSummary = $invoices->groupBy(function ($inv) {
            $clinic = $inv->appointment?->clinic ?? $inv->appointments->first()?->clinic;
            return $clinic ? $clinic->name : 'Main / General Clinic';
        })->map(function ($group, $clinicName) {
            return [
                'clinic_name' => $clinicName,
                'invoices_count' => $group->count(),
                'total_billed' => (float) $group->sum('total_amount'),
                'total_paid' => (float) $group->sum('paid_amount'),
                'total_due' => (float) $group->sum('due_amount'),
            ];
        })->sortByDesc('total_billed')->values();

        $categoryWiseSummary = $invoices->groupBy(function ($inv) {
            $cat = $inv->appointment?->category ?? $inv->appointments->first()?->category;
            return $cat ? $cat->name : 'General Consultation / Physiotherapy';
        })->map(function ($group, $catName) {
            return [
                'category_name' => $catName,
                'invoices_count' => $group->count(),
                'total_billed' => (float) $group->sum('total_amount'),
                'total_paid' => (float) $group->sum('paid_amount'),
                'total_due' => (float) $group->sum('due_amount'),
            ];
        })->sortByDesc('total_billed')->values();

        return view('payments.print-report', array_merge($filters, compact(
            'invoices',
            'totalBilled',
            'totalCollected',
            'totalDue',
            'patientWiseSummary',
            'clinicWiseSummary',
            'categoryWiseSummary',
            'loggedInDoctor'
        )));
    }

    /**
     * Export Filtered Payment Report as CSV
     */
    public function exportCsv(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        if (User::isStaffRole($currentRole)) {
            abort(403);
        }

        $loggedInDoctor = Doctor::resolveForCurrentUser($currentRole);
        $filteredQuery = $this->buildScopedInvoiceQuery($loggedInDoctor, $currentRole);
        $filters = $this->applyFilters($filteredQuery, $request);

        $invoices = $filteredQuery->orderBy('invoice_date', 'desc')->orderBy('id', 'desc')->get();

        $filename = 'payment_report_' . now()->format('Y_m_d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Invoice No',
                'Invoice Date',
                'Patient ID',
                'Patient Name',
                'Mobile',
                'Clinic',
                'Treatment Category',
                'Doctor',
                'Total Amount (Rs)',
                'Paid Amount (Rs)',
                'Unpaid / Due Amount (Rs)',
                'Payment Status',
                'Payment Method',
            ]);

            foreach ($invoices as $inv) {
                $clinic = $inv->appointment?->clinic ?? $inv->appointments->first()?->clinic;
                $cat = $inv->appointment?->category ?? $inv->appointments->first()?->category;
                fputcsv($file, [
                    $inv->invoice_no,
                    $inv->invoice_date ? $inv->invoice_date->format('Y-m-d') : '',
                    $inv->patient?->patient_id ?? '',
                    $inv->patient?->full_name ?? 'Walk-in',
                    $inv->patient?->mobile ?? '',
                    $clinic?->name ?? 'General Clinic',
                    $cat?->name ?? 'General Consultation',
                    $inv->doctor?->name ?? '',
                    number_format((float) $inv->total_amount, 2, '.', ''),
                    number_format((float) $inv->paid_amount, 2, '.', ''),
                    number_format((float) $inv->due_amount, 2, '.', ''),
                    strtoupper(str_replace('_', ' ', $inv->payment_status)),
                    $inv->payment_method ?? 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
