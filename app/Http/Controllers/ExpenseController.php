<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\AuditLog;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $date = $request->get('date');

        $query = Expense::query();

        if (!empty($category)) {
            $query->where('category', $category);
        }

        if (!empty($date)) {
            $query->where('expense_date', $date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15)->withQueryString();

        $totalExpenses = Expense::sum('amount');
        $thisMonthExpenses = Expense::whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->sum('amount');

        return view('expenses.index', compact('expenses', 'category', 'date', 'totalExpenses', 'thisMonthExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:Rent,Staff Salary,Electricity,Medicine,Equipment,Maintenance,Other',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'vendor' => 'nullable|string|max:200',
            'description' => 'required|string',
        ]);

        $expense = Expense::create(array_merge($validated, [
            'created_by' => auth()->user()->name ?? 'System Admin'
        ]));

        AuditLog::record('Expense Logged', 'Expense', (string) $expense->id, "Recorded expense of ₹" . number_format($expense->amount, 2) . " for {$expense->category}");

        return back()->with('success', "Clinic expense of ₹" . number_format($expense->amount, 2) . " recorded successfully.");
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $amount = $expense->amount;
        $category = $expense->category;
        $expense->delete();

        AuditLog::record('Expense Deleted', 'Expense', (string) $id, "Deleted expense of ₹" . number_format($amount, 2) . " ({$category})");

        return back()->with('success', "Expense record deleted successfully.");
    }
}
