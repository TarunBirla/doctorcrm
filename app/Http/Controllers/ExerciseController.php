<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Models\TreatmentCategory;
use App\Models\Doctor;
use App\Models\AuditLog;

class ExerciseController extends Controller
{
    /**
     * Display a listing of exercises.
     */
    public function index(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $loggedInDoctor = null;

        $query = Exercise::with(['category', 'doctor']);

        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            if ($loggedInDoctor) {
                // Doctor sees system exercises (doctor_id is null) AND their own created exercises
                $query->where(function ($q) use ($loggedInDoctor) {
                    $q->whereNull('doctor_id')
                      ->orWhere('doctor_id', $loggedInDoctor->id);
                });
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('body_part')) {
            $query->where('target_body_part', 'like', "%{$request->input('body_part')}%");
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('instructions', 'like', "%{$search}%")
                  ->orWhere('target_body_part', 'like', "%{$search}%");
            });
        }

        $exercises = $query->latest()->paginate(15)->withQueryString();

        $categories = TreatmentCategory::active()->orderBy('name')->get();
        $totalExercises = Exercise::count();
        $bodyParts = Exercise::whereNotNull('target_body_part')->distinct()->pluck('target_body_part');

        return view('exercises.index', compact('exercises', 'categories', 'totalExercises', 'bodyParts', 'loggedInDoctor'));
    }

    /**
     * Show the form for creating new exercises (supports multiple/bulk add).
     */
    public function create()
    {
        $categories = TreatmentCategory::active()->orderBy('name')->get();
        return view('exercises.create', compact('categories'));
    }

    /**
     * Store newly created exercise(s). Supports both single and bulk/multiple additions.
     */
    public function store(Request $request)
    {
        $currentRole = session('current_role', auth()->user()->role ?? 'super_admin');
        $doctorId = null;
        if ($currentRole === 'doctor' && auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
            $doctorId = $loggedInDoctor ? $loggedInDoctor->id : null;
        }

        // Check if multiple exercises are submitted via dynamic rows
        if ($request->has('items') && is_array($request->input('items'))) {
            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.name' => 'required|string|max:200',
                'items.*.category_id' => 'nullable|exists:treatment_categories,id',
                'items.*.target_body_part' => 'nullable|string|max:100',
                'items.*.sets' => 'nullable|string|max:50',
                'items.*.reps' => 'nullable|string|max:50',
                'items.*.duration' => 'nullable|string|max:50',
                'items.*.instructions' => 'nullable|string',
                'items.*.precautions' => 'nullable|string',
                'items.*.video_url' => 'nullable|url|max:255',
            ]);

            $count = 0;
            foreach ($request->input('items') as $item) {
                if (!empty(trim($item['name'] ?? ''))) {
                    Exercise::create([
                        'name' => trim($item['name']),
                        'category_id' => !empty($item['category_id']) ? $item['category_id'] : null,
                        'doctor_id' => $doctorId,
                        'target_body_part' => $item['target_body_part'] ?? null,
                        'sets' => $item['sets'] ?? null,
                        'reps' => $item['reps'] ?? null,
                        'duration' => $item['duration'] ?? null,
                        'instructions' => $item['instructions'] ?? null,
                        'precautions' => $item['precautions'] ?? null,
                        'video_url' => $item['video_url'] ?? null,
                        'is_active' => true,
                    ]);
                    $count++;
                }
            }

            AuditLog::record('Exercises Bulk Added', 'Exercise', 'Multiple', "Bulk added {$count} physiotherapy exercises.");

            return redirect()->route('exercises.index')->with('success', "{$count} exercises added successfully!");
        }

        // Single exercise add fallback
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'nullable|exists:treatment_categories,id',
            'target_body_part' => 'nullable|string|max:100',
            'sets' => 'nullable|string|max:50',
            'reps' => 'nullable|string|max:50',
            'duration' => 'nullable|string|max:50',
            'instructions' => 'nullable|string',
            'precautions' => 'nullable|string',
            'video_url' => 'nullable|url|max:255',
        ]);

        $exercise = Exercise::create([
            'name' => trim($validated['name']),
            'category_id' => $validated['category_id'] ?? null,
            'doctor_id' => $doctorId,
            'target_body_part' => $validated['target_body_part'] ?? null,
            'sets' => $validated['sets'] ?? null,
            'reps' => $validated['reps'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'precautions' => $validated['precautions'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::record('Exercise Created', 'Exercise', (string)$exercise->id, "Created exercise '{$exercise->name}'");

        return redirect()->route('exercises.index')->with('success', "Exercise '{$exercise->name}' added successfully!");
    }

    /**
     * Show the form for editing an exercise.
     */
    public function edit($id)
    {
        $exercise = Exercise::findOrFail($id);
        $categories = TreatmentCategory::active()->orderBy('name')->get();
        return view('exercises.edit', compact('exercise', 'categories'));
    }

    /**
     * Update the specified exercise in storage.
     */
    public function update(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'nullable|exists:treatment_categories,id',
            'target_body_part' => 'nullable|string|max:100',
            'sets' => 'nullable|string|max:50',
            'reps' => 'nullable|string|max:50',
            'duration' => 'nullable|string|max:50',
            'instructions' => 'nullable|string',
            'precautions' => 'nullable|string',
            'video_url' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $exercise->update([
            'name' => trim($validated['name']),
            'category_id' => $validated['category_id'] ?? null,
            'target_body_part' => $validated['target_body_part'] ?? null,
            'sets' => $validated['sets'] ?? null,
            'reps' => $validated['reps'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'precautions' => $validated['precautions'] ?? null,
            'video_url' => $validated['video_url'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::record('Exercise Updated', 'Exercise', (string)$exercise->id, "Updated exercise '{$exercise->name}'");

        return redirect()->route('exercises.index')->with('success', "Exercise '{$exercise->name}' updated successfully!");
    }

    /**
     * Remove the specified exercise from storage.
     */
    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        $name = $exercise->name;
        $exercise->delete();

        AuditLog::record('Exercise Deleted', 'Exercise', (string)$id, "Deleted exercise '{$name}'");

        return redirect()->route('exercises.index')->with('success', "Exercise '{$name}' removed successfully!");
    }
}
