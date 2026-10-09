<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TreatmentCategory;
use App\Models\Doctor;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of treatment categories.
     */
    public function index(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;

        $query = TreatmentCategory::with(['doctor', 'exercises'])
            ->withCount(['exercises', 'patients', 'appointments']);

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor) {
                // Doctor sees standard system categories (doctor_id is null) AND their own created categories
                $query->where(function ($q) use ($loggedInDoctor) {
                    $q->whereNull('doctor_id')
                      ->orWhere('doctor_id', $loggedInDoctor->id);
                });
            }
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $categories = $query->orderBy('name', 'asc')->paginate(12)->withQueryString();

        $totalCategories = TreatmentCategory::count();
        $activeCategories = TreatmentCategory::where('is_active', true)->count();

        return view('categories.index', compact('categories', 'totalCategories', 'activeCategories', 'loggedInDoctor'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:treatment_categories,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $doctorId = null;

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $doctorId = $loggedInDoctor ? $loggedInDoctor->id : null;
        }

        $category = TreatmentCategory::create([
            'name' => trim($validated['name']),
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'doctor_id' => $doctorId,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::record('Category Created', 'TreatmentCategory', (string)$category->id, "Created treatment category '{$category->name}'");

        return redirect()->route('categories.index')->with('success', "Category '{$category->name}' created successfully!");
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $category = TreatmentCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150|unique:treatment_categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update([
            'name' => trim($validated['name']),
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::record('Category Updated', 'TreatmentCategory', (string)$category->id, "Updated treatment category '{$category->name}'");

        return redirect()->route('categories.index')->with('success', "Category '{$category->name}' updated successfully!");
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy($id)
    {
        $category = TreatmentCategory::withCount(['exercises', 'patients'])->findOrFail($id);

        if ($category->exercises_count > 0 || $category->patients_count > 0) {
            return back()->with('error', "Cannot delete category '{$category->name}' because it contains {$category->exercises_count} exercises and {$category->patients_count} patients. Please reassign them first.");
        }

        $name = $category->name;
        $category->delete();

        AuditLog::record('Category Deleted', 'TreatmentCategory', (string)$id, "Deleted treatment category '{$name}'");

        return redirect()->route('categories.index')->with('success', "Category '{$name}' deleted successfully!");
    }
}
