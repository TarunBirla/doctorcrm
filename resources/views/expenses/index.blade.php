@extends('layouts.app')

@section('title', 'Clinic Expenses')
@section('breadcrumb', 'Finance / Expenses')
@section('page_title', 'Clinic Operational Expenses Management')

@section('content')
<div class="space-y-6">

    <!-- EXPENSES SUMMARY -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card-custom p-5 bg-white border-rose-100">
            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500 block">Total Overall Expenses</span>
            <div class="text-3xl font-black text-rose-600 tracking-tight mt-1">₹{{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">This Month's Overhead</span>
            <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($thisMonthExpenses, 2) }}</div>
        </div>
        <div class="card-custom p-5 bg-white">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Expense Entries</span>
            <div class="text-3xl font-extrabold text-blue-700 tracking-tight mt-1">{{ $expenses->total() }}</div>
        </div>
    </div>

    <!-- FILTER & RECORD EXPENSE BAR -->
    <div class="card-custom p-5 bg-white">
        <div class="flex flex-wrap items-center justify-between gap-4 text-xs">
            <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="category" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                    <option value="">All Expense Categories</option>
                    @foreach(['Rent', 'Staff Salary', 'Electricity', 'Medicine', 'Equipment', 'Maintenance', 'Other'] as $c)
                        <option value="{{ $c }}" {{ $category === $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>
                <input type="date" name="date" value="{{ $date }}" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl outline-none">
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition">Filter</button>
                <a href="{{ route('expenses.index') }}" class="px-3 py-2 border rounded-xl text-slate-600 hover:bg-slate-50 font-medium">Reset</a>
            </form>

            <button onclick="openModal('addExpenseModal')" class="px-4 py-2 bg-navy-900 text-white font-bold rounded-xl hover:bg-navy-800 transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Record Clinic Expense</span>
            </button>
        </div>
    </div>

    <!-- EXPENSES TABLE -->
    <div class="card-custom bg-white overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Disbursement Ledger</h3>
            <span class="text-xs text-slate-400">Total {{ $expenses->total() }} entries</span>
        </div>

        <div class="overflow-x-auto text-xs">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <tr class="border-b border-slate-100">
                        <th class="p-3.5 pl-5">Date</th>
                        <th class="p-3.5">Category</th>
                        <th class="p-3.5">Description</th>
                        <th class="p-3.5">Vendor / Payee</th>
                        <th class="p-3.5">Payment Method</th>
                        <th class="p-3.5">Amount (₹)</th>
                        <th class="p-3.5 pr-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $exp)
                        <tr class="hover:bg-slate-50">
                            <td class="p-3.5 pl-5 font-semibold text-slate-700">{{ $exp->expense_date->format('d M Y') }}</td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase 
                                    @if($exp->category === 'Rent') bg-blue-100 text-blue-800
                                    @elseif($exp->category === 'Staff Salary') bg-purple-100 text-purple-800
                                    @elseif($exp->category === 'Medicine') bg-emerald-100 text-emerald-800
                                    @else bg-slate-100 text-slate-700 @endif">
                                    {{ $exp->category }}
                                </span>
                            </td>
                            <td class="p-3.5 text-slate-800 font-medium max-w-sm">{{ $exp->description }}</td>
                            <td class="p-3.5 text-slate-600">{{ $exp->vendor ?? 'Commercial' }}</td>
                            <td class="p-3.5 text-slate-600">{{ $exp->payment_method }}</td>
                            <td class="p-3.5 font-bold text-rose-600 text-sm">₹{{ number_format($exp->amount, 2) }}</td>
                            <td class="p-3.5 pr-5 text-right">
                                <form action="{{ route('expenses.destroy', $exp->id) }}" method="POST" onsubmit="return confirm('Delete this expense record?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold p-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-12 text-center text-slate-400">No expense records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($expenses->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL: ADD EXPENSE -->
<div id="addExpenseModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="font-bold text-slate-900 text-sm">Record Clinic Operational Expense</h3>
            <button onclick="closeModal('addExpenseModal')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4 text-xs">
            @csrf
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Expense Category *</label>
                <select name="category" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                    <option value="Rent">Rent</option>
                    <option value="Staff Salary">Staff Salary</option>
                    <option value="Electricity">Electricity</option>
                    <option value="Medicine">Medicine & Consumables</option>
                    <option value="Equipment">Equipment & Tech</option>
                    <option value="Maintenance">Maintenance & Sanitation</option>
                    <option value="Other">Other Expenses</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Amount (₹) *</label>
                    <input type="number" step="0.01" min="1" name="amount" required placeholder="e.g. 5000" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 font-bold outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Date *</label>
                    <input type="date" name="expense_date" value="{{ now()->toDateString() }}" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Payment Method *</label>
                    <select name="payment_method" required class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="UPI">UPI</option>
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                    </select>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Vendor / Payee</label>
                    <input type="text" name="vendor" placeholder="Vendor company" class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none">
                </div>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Description / Memo *</label>
                <textarea name="description" rows="2" required placeholder="Purpose of this disbursement..." class="w-full px-3 py-2 border border-slate-200 rounded-xl bg-slate-50 outline-none"></textarea>
            </div>
            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('addExpenseModal')" class="px-4 py-2 border rounded-xl text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-navy-900 text-white font-bold rounded-xl">Save Expense</button>
            </div>
        </form>
    </div>
</div>
@endsection
