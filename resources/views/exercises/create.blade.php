@extends('layouts.app')

@section('title', 'Add Multiple Exercises')
@section('breadcrumb', 'Exercises / Multiple Add')
@section('page_title', 'Bulk Add Physiotherapy Exercises')

@section('content')
<div class="space-y-6">

    <!-- HEADER CARD -->
    <div class="card-custom p-6 bg-gradient-to-r from-blue-700 to-indigo-800 text-white rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-white/10 text-white text-xs font-semibold backdrop-blur-xs mb-2">
                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                <span>Fast Batch Entry</span>
            </div>
            <h2 class="text-xl font-extrabold tracking-tight">Add Multiple Exercises at Once</h2>
            <p class="text-xs text-blue-100 font-semibold mt-1">Create multiple rehab protocols and assign them to treatment categories in one go.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('exercises.index') }}" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-xl transition">
                Back to Library
            </a>
            <button type="button" onclick="addExerciseRow()" class="px-4 py-2 bg-white text-blue-700 hover:bg-blue-50 text-xs font-bold rounded-xl shadow-xs transition flex items-center gap-1.5">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add Row</span>
            </button>
        </div>
    </div>

    <!-- MAIN BULK FORM -->
    <form action="{{ route('exercises.store') }}" method="POST" id="bulkExercisesForm">
        @csrf

        <div class="space-y-4" id="exerciseRowsContainer">
            <!-- Row 1 -->
            <div class="card-custom p-5 bg-white border border-slate-200 shadow-xs exercise-row relative transition" data-row="0">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center row-number">1</span>
                        <h4 class="text-xs font-bold text-slate-800">Exercise Details</h4>
                    </div>
                    <button type="button" onclick="removeExerciseRow(this)" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg hover:bg-rose-50 transition" title="Remove Row">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Exercise Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="items[0][name]" required placeholder="e.g. Scapular Retraction" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Treatment Category</label>
                        <select name="items[0][category_id]" class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-700">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Anatomy / Body Part</label>
                        <input type="text" name="items[0][target_body_part]" placeholder="e.g. Upper Back / Shoulders" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sets</label>
                        <input type="text" name="items[0][sets]" placeholder="e.g. 3 Sets" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Reps</label>
                        <input type="text" name="items[0][reps]" placeholder="e.g. 12 Reps" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Hold / Duration</label>
                        <input type="text" name="items[0][duration]" placeholder="e.g. 5 sec hold" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Video Demonstration URL</label>
                        <input type="url" name="items[0][video_url]" placeholder="https://..." 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Instructions / Execution Steps</label>
                        <textarea name="items[0][instructions]" rows="2" placeholder="Step-by-step guidance for the patient..." 
                                  class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Precautions & Cautions</label>
                        <textarea name="items[0][precautions]" rows="2" placeholder="Signs of pain to avoid, contraindications..." 
                                  class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
                    </div>
                </div>
            </div>

            <!-- Row 2 -->
            <div class="card-custom p-5 bg-white border border-slate-200 shadow-xs exercise-row relative transition" data-row="1">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center row-number">2</span>
                        <h4 class="text-xs font-bold text-slate-800">Exercise Details</h4>
                    </div>
                    <button type="button" onclick="removeExerciseRow(this)" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg hover:bg-rose-50 transition" title="Remove Row">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Exercise Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="items[1][name]" placeholder="e.g. Pelvic Bridging" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Treatment Category</label>
                        <select name="items[1][category_id]" class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-700">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Target Anatomy / Body Part</label>
                        <input type="text" name="items[1][target_body_part]" placeholder="e.g. Lumbar & Core" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sets</label>
                        <input type="text" name="items[1][sets]" placeholder="e.g. 3 Sets" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Reps</label>
                        <input type="text" name="items[1][reps]" placeholder="e.g. 10 Reps" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Hold / Duration</label>
                        <input type="text" name="items[1][duration]" placeholder="e.g. 5 sec hold" 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Video Demonstration URL</label>
                        <input type="url" name="items[1][video_url]" placeholder="https://..." 
                               class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Instructions / Execution Steps</label>
                        <textarea name="items[1][instructions]" rows="2" placeholder="Step-by-step guidance for the patient..." 
                                  class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Precautions & Cautions</label>
                        <textarea name="items[1][precautions]" rows="2" placeholder="Signs of pain to avoid, contraindications..." 
                                  class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4 card-custom p-4 bg-white border border-slate-200 shadow-xs">
            <button type="button" onclick="addExerciseRow()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl transition">
                <i data-lucide="plus-circle" class="w-4 h-4 text-blue-600"></i>
                <span>Add Another Exercise Row</span>
            </button>

            <div class="flex items-center gap-3">
                <a href="{{ route('exercises.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Save All Exercises</span>
                </button>
            </div>
        </div>
    </form>

</div>

<script>
let rowIndex = 2;

const categoriesOptions = `
    <option value="">Select Category</option>
    @foreach($categories as $cat)
        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
    @endforeach
`;

function addExerciseRow() {
    const container = document.getElementById('exerciseRowsContainer');
    const rowDiv = document.createElement('div');
    rowDiv.className = 'card-custom p-5 bg-white border border-slate-200 shadow-xs exercise-row relative transition animate-in fade-in slide-in-from-top-2 duration-200';
    rowDiv.dataset.row = rowIndex;

    rowDiv.innerHTML = `
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center row-number">0</span>
                <h4 class="text-xs font-bold text-slate-800">Exercise Details</h4>
            </div>
            <button type="button" onclick="removeExerciseRow(this)" class="text-slate-400 hover:text-rose-600 p-1 rounded-lg hover:bg-rose-50 transition" title="Remove Row">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-slate-700 mb-1">Exercise Name <span class="text-rose-500">*</span></label>
                <input type="text" name="items[${rowIndex}][name]" required placeholder="e.g. Exercise Name" 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Treatment Category</label>
                <select name="items[${rowIndex}][category_id]" class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-700">
                    ${categoriesOptions}
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Target Anatomy / Body Part</label>
                <input type="text" name="items[${rowIndex}][target_body_part]" placeholder="e.g. Knee / Quadriceps" 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Sets</label>
                <input type="text" name="items[${rowIndex}][sets]" placeholder="e.g. 3 Sets" 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Reps</label>
                <input type="text" name="items[${rowIndex}][reps]" placeholder="e.g. 10 Reps" 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Hold / Duration</label>
                <input type="text" name="items[${rowIndex}][duration]" placeholder="e.g. 10 sec hold" 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Video Demonstration URL</label>
                <input type="url" name="items[${rowIndex}][video_url]" placeholder="https://..." 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Instructions / Execution Steps</label>
                <textarea name="items[${rowIndex}][instructions]" rows="2" placeholder="Step-by-step guidance for the patient..." 
                          class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Precautions & Cautions</label>
                <textarea name="items[${rowIndex}][precautions]" rows="2" placeholder="Signs of pain to avoid, contraindications..." 
                          class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
            </div>
        </div>
    `;

    container.appendChild(rowDiv);
    rowIndex++;
    updateRowNumbers();
    lucide.createIcons();
}

function removeExerciseRow(button) {
    const rows = document.querySelectorAll('.exercise-row');
    if (rows.length <= 1) {
        alert('You must have at least one exercise row.');
        return;
    }
    const row = button.closest('.exercise-row');
    row.remove();
    updateRowNumbers();
}

function updateRowNumbers() {
    const numbers = document.querySelectorAll('.row-number');
    numbers.forEach((badge, idx) => {
        badge.innerText = idx + 1;
    });
}
</script>
@endsection
