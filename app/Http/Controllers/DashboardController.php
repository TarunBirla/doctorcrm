<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\Invoice;
use App\Models\PaymentTransaction;
use App\Models\Expense;
use App\Models\FollowUp;
use App\Models\Doctor;
use App\Models\Clinic;
use App\Models\Notification;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Auto-detect if database is not initialized yet on live server
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('users') || !\Illuminate\Support\Facades\Schema::hasTable('patients')) {
                return redirect()->route('database.setup');
            }
        } catch (\Exception $e) {
            return redirect()->route('database.setup');
        }

        // If not logged in, redirect to login page
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $dateFilter = $request->get('date_filter', 'today');
        $customStart = $request->get('start_date');
        $customEnd = $request->get('end_date');

        // Resolve date boundaries
        $today = Carbon::today();
        switch ($dateFilter) {
            case 'yesterday':
                $startDate = Carbon::yesterday()->startOfDay();
                $endDate = Carbon::yesterday()->endOfDay();
                $filterLabel = 'Yesterday (' . $startDate->format('d M Y') . ')';
                break;
            case 'tomorrow':
                $startDate = Carbon::tomorrow()->startOfDay();
                $endDate = Carbon::tomorrow()->endOfDay();
                $filterLabel = 'Tomorrow (' . $startDate->format('d M Y') . ')';
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $filterLabel = 'This Week (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'last_week':
                $startDate = Carbon::now()->subWeek()->startOfWeek();
                $endDate = Carbon::now()->subWeek()->endOfWeek();
                $filterLabel = 'Last Week (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $filterLabel = 'This Month (' . $startDate->format('F Y') . ')';
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                $filterLabel = 'Last Month (' . $startDate->format('F Y') . ')';
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $filterLabel = 'This Year (' . $startDate->format('Y') . ')';
                break;
            case 'custom':
                $startDate = $customStart ? Carbon::parse($customStart)->startOfDay() : Carbon::today()->startOfDay();
                $endDate = $customEnd ? Carbon::parse($customEnd)->endOfDay() : Carbon::today()->endOfDay();
                $filterLabel = 'Custom Range (' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y') . ')';
                break;
            case 'today':
            default:
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $filterLabel = 'Today (' . $today->format('d M Y') . ')';
                break;
        }

        // Resolve current role and logged-in doctor if role is doctor
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;
        if ($currentRole === 'doctor') {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
        }

        // 1. Appointments Summary (Isolated for doctor)
        $appointmentsQuery = Appointment::whereBetween('appointment_date', [$startDate->toDateString(), $endDate->toDateString()]);
        if ($loggedInDoctor) {
            $appointmentsQuery->where('doctor_id', $loggedInDoctor->id);
        } elseif ($currentRole === 'doctor') {
            $appointmentsQuery->whereRaw('1 = 0'); // Empty if doctor profile not linked
        }

        $totalAppointments = (clone $appointmentsQuery)->count();
        $completedAppointments = (clone $appointmentsQuery)->where('status', 'completed')->count();
        $waitingAppointments = (clone $appointmentsQuery)->where('status', 'waiting')->count();
        $inConsultationAppointments = (clone $appointmentsQuery)->where('status', 'in_consultation')->count();
        $pendingAppointments = (clone $appointmentsQuery)->whereIn('status', ['scheduled', 'confirmed'])->count();
        $cancelledAppointments = (clone $appointmentsQuery)->where('status', 'cancelled')->count();
        $noShowAppointments = (clone $appointmentsQuery)->where('status', 'no_show')->count();

        $newPatientAppointments = (clone $appointmentsQuery)->where('appointment_type', 'new')->count();
        $revisitAppointments = (clone $appointmentsQuery)->whereIn('appointment_type', ['follow_up', 'revisit'])->count();

        // 2. Financial Summary
        $invoicesQuery = Invoice::whereBetween('invoice_date', [$startDate->toDateString(), $endDate->toDateString()]);
        $transactionsQuery = PaymentTransaction::whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()]);

        if ($loggedInDoctor) {
            $invoicesQuery->whereHas('appointment', function ($q) use ($loggedInDoctor) {
                $q->where('doctor_id', $loggedInDoctor->id);
            });
            $transactionsQuery->whereHas('invoice.appointment', function ($q) use ($loggedInDoctor) {
                $q->where('doctor_id', $loggedInDoctor->id);
            });
        } elseif ($currentRole === 'doctor') {
            $invoicesQuery->whereRaw('1 = 0');
            $transactionsQuery->whereRaw('1 = 0');
        }

        $totalBilled = (clone $invoicesQuery)->sum('total_amount');
        $totalCollected = (clone $transactionsQuery)->sum('amount');
        
        // Outstanding dues overall
        $totalOutstandingDuesQuery = Invoice::whereIn('payment_status', ['unpaid', 'partially_paid', 'due']);
        if ($loggedInDoctor) {
            $totalOutstandingDuesQuery->whereHas('appointment', function ($q) use ($loggedInDoctor) {
                $q->where('doctor_id', $loggedInDoctor->id);
            });
        }
        $totalOutstandingDues = $totalOutstandingDuesQuery->sum('due_amount');
        $rangeOutstandingDues = (clone $invoicesQuery)->whereIn('payment_status', ['unpaid', 'partially_paid', 'due'])->sum('due_amount');

        // Payment method breakdown
        $cashCollection = (clone $transactionsQuery)->where('payment_method', 'Cash')->sum('amount');
        $upiCollection = (clone $transactionsQuery)->where('payment_method', 'UPI')->sum('amount');
        $cardCollection = (clone $transactionsQuery)->where('payment_method', 'Card')->sum('amount');
        $bankCollection = (clone $transactionsQuery)->where('payment_method', 'Bank Transfer')->sum('amount');
        $otherCollection = (clone $transactionsQuery)->whereNotIn('payment_method', ['Cash', 'UPI', 'Card', 'Bank Transfer'])->sum('amount');

        // Expenses in range (Only super_admin or clinic wide, 0 for doctor)
        $totalExpenses = ($currentRole === 'doctor') ? 0 : Expense::whereBetween('expense_date', [$startDate->toDateString(), $endDate->toDateString()])->sum('amount');
        $netIncome = $totalCollected - $totalExpenses;

        // 3. Patient Statistics (Isolated for doctor: only patients who consulted with this doctor)
        $patientsBaseQuery = Patient::query();
        if ($loggedInDoctor) {
            $patientsBaseQuery->where(function ($q) use ($loggedInDoctor) {
                $q->whereHas('appointments', function ($aq) use ($loggedInDoctor) {
                    $aq->where('doctor_id', $loggedInDoctor->id);
                })->orWhereHas('visits', function ($vq) use ($loggedInDoctor) {
                    $vq->where('doctor_id', $loggedInDoctor->id);
                });
            });
        } elseif ($currentRole === 'doctor') {
            $patientsBaseQuery->whereRaw('1 = 0');
        }

        $totalPatientsCount = (clone $patientsBaseQuery)->count();
        $newPatientsInRange = (clone $patientsBaseQuery)->whereBetween('created_at', [$startDate, $endDate])->count();
        $patientsWithDuesCount = (clone $patientsBaseQuery)->whereHas('invoices', function($q) {
            $q->whereIn('payment_status', ['unpaid', 'partially_paid', 'due']);
        })->count();

        $followUpsQuery = FollowUp::where('status', 'scheduled')
            ->whereDate('follow_up_date', '>=', now()->toDateString());
        if ($loggedInDoctor) {
            $followUpsQuery->where('doctor_id', $loggedInDoctor->id);
        }
        $patientsNeedingFollowUp = $followUpsQuery->count();

        // 4. Today's Queue (Filtered for Doctor if logged in as doctor)
        $todayQueueQuery = Appointment::with(['patient', 'doctor'])
            ->where('appointment_date', now()->toDateString());
        if ($loggedInDoctor) {
            $todayQueueQuery->where('doctor_id', $loggedInDoctor->id);
        } elseif ($currentRole === 'doctor') {
            $todayQueueQuery->whereRaw('1 = 0');
        }

        $todayQueue = $todayQueueQuery->orderByRaw("CASE 
                WHEN status = 'in_consultation' THEN 1 
                WHEN status = 'waiting' THEN 2 
                WHEN status = 'confirmed' THEN 3 
                WHEN status = 'scheduled' THEN 4 
                WHEN status = 'completed' THEN 5 
                ELSE 6 END")
            ->orderBy('token_number', 'asc')
            ->get();

        // Identify "Who is Next?" for this doctor
        $nextPatientQuery = Appointment::with('patient')
            ->where('appointment_date', now()->toDateString())
            ->where('status', 'waiting');
        if ($loggedInDoctor) {
            $nextPatientQuery->where('doctor_id', $loggedInDoctor->id);
        }
        $nextPatientAppointment = $nextPatientQuery->orderBy('token_number', 'asc')->first();

        // 5. Recent Financial Transactions
        $recentTransactionsQuery = PaymentTransaction::with(['invoice', 'patient']);
        if ($loggedInDoctor) {
            $recentTransactionsQuery->whereHas('invoice.appointment', function ($q) use ($loggedInDoctor) {
                $q->where('doctor_id', $loggedInDoctor->id);
            });
        }
        $recentTransactions = $recentTransactionsQuery->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 6. Recent Consultations / Visits
        $recentVisitsQuery = Visit::with(['patient', 'doctor']);
        if ($loggedInDoctor) {
            $recentVisitsQuery->where('doctor_id', $loggedInDoctor->id);
        } elseif ($currentRole === 'doctor') {
            $recentVisitsQuery->whereRaw('1 = 0');
        }
        $recentVisits = $recentVisitsQuery->orderBy('visit_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 7. Last 7 Days Trend for Charts
        $trendDates = [];
        $trendAppointments = [];
        $trendRevenue = [];
        $trendCollections = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $dayStr = $day->toDateString();
            $trendDates[] = $day->format('d M');

            $appCountQuery = Appointment::where('appointment_date', $dayStr);
            $invRevQuery = Invoice::where('invoice_date', $dayStr);
            $payColQuery = PaymentTransaction::where('payment_date', $dayStr);

            if ($loggedInDoctor) {
                $appCountQuery->where('doctor_id', $loggedInDoctor->id);
                $invRevQuery->whereHas('appointment', fn($q) => $q->where('doctor_id', $loggedInDoctor->id));
                $payColQuery->whereHas('invoice.appointment', fn($q) => $q->where('doctor_id', $loggedInDoctor->id));
            }

            $trendAppointments[] = $appCountQuery->count();
            $trendRevenue[] = (float) $invRevQuery->sum('total_amount');
            $trendCollections[] = (float) $payColQuery->sum('amount');
        }

        $clinic = Clinic::first() ?? new Clinic();
        $doctors = Doctor::all();
        $notifications = Notification::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'dateFilter', 'filterLabel', 'startDate', 'endDate',
            'totalAppointments', 'completedAppointments', 'waitingAppointments',
            'inConsultationAppointments', 'pendingAppointments', 'cancelledAppointments',
            'noShowAppointments', 'newPatientAppointments', 'revisitAppointments',
            'totalBilled', 'totalCollected', 'totalOutstandingDues', 'rangeOutstandingDues',
            'cashCollection', 'upiCollection', 'cardCollection', 'bankCollection', 'otherCollection',
            'totalExpenses', 'netIncome',
            'totalPatientsCount', 'newPatientsInRange', 'patientsWithDuesCount', 'patientsNeedingFollowUp',
            'todayQueue', 'nextPatientAppointment',
            'recentTransactions', 'recentVisits',
            'trendDates', 'trendAppointments', 'trendRevenue', 'trendCollections',
            'clinic', 'doctors', 'notifications'
        ));
    }

    public function search(Request $request)
    {
        $q = trim($request->get('q', ''));
        if (empty($q)) {
            return response()->json(['results' => []]);
        }

        $patients = Patient::where('first_name', 'like', "%{$q}%")
            ->orWhere('last_name', 'like', "%{$q}%")
            ->orWhere('patient_id', 'like', "%{$q}%")
            ->orWhere('mobile', 'like', "%{$q}%")
            ->take(5)
            ->get(['id', 'patient_id', 'first_name', 'last_name', 'mobile', 'gender', 'age']);

        $appointments = Appointment::with('patient')
            ->where('appointment_no', 'like', "%{$q}%")
            ->take(5)
            ->get();

        $invoices = Invoice::with('patient')
            ->where('invoice_no', 'like', "%{$q}%")
            ->take(5)
            ->get();

        return response()->json([
            'patients' => $patients,
            'appointments' => $appointments,
            'invoices' => $invoices,
        ]);
    }
}
