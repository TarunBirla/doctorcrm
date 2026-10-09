@extends('layouts.app')

@section('title', 'Physiotherapy Exercise Library')
@section('breadcrumb', 'Exercises')
@section('page_title', 'Physiotherapy Exercise Protocol Library')

@section('content')
<div class="space-y-6">

    <!-- TOP HEADER ACTIONS & STATS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card-custom p-5 bg-white border border-slate-100 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Exercises</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $totalExercises }}</div>
                <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Clinical rehab protocols</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <i data-lucide="dumbbell" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white border border-slate-100 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block">Categories Covered</span>
                <div class="text-3xl font-extrabold text-blue-700 tracking-tight mt-1">{{ $categories->count() }}</div>
                <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Condition specific programs</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white border border-slate-100 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Add Exercises</span>
                <div class="mt-2 flex items-center gap-2">
                    <a href="{{ route('exercises.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Multiple / Bulk Add</span>
                    </a>
                    <button type="button" onclick="openSingleAddModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold rounded-xl transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Single</span>
                    </button>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="card-custom p-4 bg-white border border-slate-100 shadow-xs">
        <form action="{{ route('exercises.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[240px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search exercise name, target body part, instructions..." 
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none transition text-slate-800 font-semibold">
            </div>

            <select name="category_id" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-700 font-semibold">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'category_id']))
                <a href="{{ route('exercises.index') }}" class="px-3 py-2 text-xs text-rose-600 hover:bg-rose-50 rounded-xl font-semibold transition">
                    Clear
                </a>
            @endif

            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('exercises.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                    <i data-lucide="copy-plus" class="w-4 h-4"></i>
                    <span>Multiple Add Exercises</span>
                </a>
            </div>
        </form>
    </div>

    <!-- EXERCISES LIST TABLE / CARDS -->
    <div class="card-custom bg-white border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Exercise Name & Target</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Prescription (Sets × Reps)</th>
                        <th class="py-3.5 px-4">Hold / Duration</th>
                        <th class="py-3.5 px-4">Instructions Summary</th>
                        <th class="py-3.5 px-4 text-center">Video / Media</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($exercises as $exercise)
                        <tr class="hover:bg-slate-50/60 transition group">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shrink-0">
                                        <i data-lucide="activity" class="w-4 h-4"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-blue-600 transition">{{ $exercise->name }}</div>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            @if($exercise->target_body_part)
                                                <span class="text-[10px] px-1.5 py-0.2 bg-slate-100 text-slate-600 rounded font-semibold">
                                                    {{ $exercise->target_body_part }}
                                                </span>
                                            @endif
                                            @if($exercise->doctor_id)
                                                <span class="text-[9px] px-1.5 py-0.2 bg-purple-50 text-purple-700 font-bold rounded border border-purple-200">Dr. Protocol</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($exercise->category)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ $exercise->category->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">General Rehab</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $exercise->sets ?? '-' }} × {{ $exercise->reps ?? '-' }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-600 font-semibold">
                                    {{ $exercise->duration ?? 'Standard' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-slate-600 text-[11px] line-clamp-2 leading-relaxed">
                                    {{ $exercise->instructions ?? 'No specific instructions.' }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($exercise->video_url)
                                    <a href="{{ $exercise->video_url }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition" title="Watch Demo Video">
                                        <i data-lucide="play-circle" class="w-3 h-3"></i>
                                        <span>Video</span>
                                    </a>
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            onclick='openViewExerciseModal(@json($exercise))' 
                                            class="p-1.5 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" 
                                            title="View Exercise Protocol">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    <a href="{{ route('exercises.edit', $exercise->id) }}" 
                                       class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                       title="Edit Exercise">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('exercises.destroy', $exercise->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete \'{{ $exercise->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Exercise">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <i data-lucide="dumbbell" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="text-sm font-semibold text-slate-600">No physiotherapy exercises found.</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click "Multiple / Bulk Add" above to quickly populate exercises.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exercises->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $exercises->links() }}
            </div>
        @endif
    </div>

</div>

<!-- QUICK SINGLE ADD MODAL -->
<div id="singleAddModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="dumbbell" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Add Exercise Protocol</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">Physiotherapy exercise details</p>
                </div>
            </div>
            <button type="button" onclick="closeSingleAddModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('exercises.store') }}" method="POST" class="p-6 space-y-3.5">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Exercise Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Chin Tucks & Neck Retraction" 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-700">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Body Part</label>
                    <input type="text" name="target_body_part" placeholder="e.g. Cervical / Neck" 
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Sets</label>
                    <input type="text" name="sets" placeholder="e.g. 3 Sets" 
                           class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Reps</label>
                    <input type="text" name="reps" placeholder="e.g. 10 Reps" 
                           class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Hold / Duration</label>
                    <input type="text" name="duration" placeholder="e.g. 5 sec hold" 
                           class="w-full px-3 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Instructions / Technique</label>
                <textarea name="instructions" rows="2" placeholder="Step-by-step patient execution procedure..."
                          class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Video Reference URL (Optional)</label>
                <input type="url" name="video_url" placeholder="https://youtube.com/watch?v=..." 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>

            <div class="pt-4 flex items-center justify-between border-t border-slate-100">
                <a href="{{ route('exercises.create') }}" class="text-xs text-blue-600 font-semibold hover:underline">
                    Need multiple? Switch to Bulk Add
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeSingleAddModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition">
                        Save Exercise
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- VIEW EXERCISE MODAL -->
<div id="viewExerciseModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                    <i data-lucide="activity" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 id="view_modal_title" class="text-sm font-bold text-slate-900">Exercise Protocol Details</h3>
                    <p id="view_modal_category" class="text-[11px] text-slate-500 font-semibold"></p>
                </div>
            </div>
            <button type="button" onclick="closeViewExerciseModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div class="grid grid-cols-3 gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100 text-center">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Sets</span>
                    <span id="view_modal_sets" class="text-sm font-extrabold text-slate-800 mt-0.5 block">-</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Reps</span>
                    <span id="view_modal_reps" class="text-sm font-extrabold text-slate-800 mt-0.5 block">-</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Hold</span>
                    <span id="view_modal_duration" class="text-sm font-extrabold text-slate-800 mt-0.5 block">-</span>
                </div>
            </div>

            <div>
                <h4 class="text-xs font-bold text-slate-800 mb-1">Target Anatomy</h4>
                <p id="view_modal_body_part" class="text-xs text-slate-600 font-semibold"></p>
            </div>

            <div>
                <h4 class="text-xs font-bold text-slate-800 mb-1">Execution Steps & Instructions</h4>
                <p id="view_modal_instructions" class="text-xs text-slate-600 leading-relaxed font-semibold bg-slate-50/70 p-3 rounded-xl border border-slate-100"></p>
            </div>

            <div id="view_modal_precautions_container" class="hidden">
                <h4 class="text-xs font-bold text-rose-700 mb-1">Precautions & Contraindications</h4>
                <p id="view_modal_precautions" class="text-xs text-rose-600 leading-relaxed font-semibold bg-rose-50/60 p-3 rounded-xl border border-rose-100"></p>
            </div>

            <div id="view_modal_video_container" class="hidden pt-2">
                <a id="view_modal_video_link" href="#" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-xs transition">
                    <i data-lucide="play" class="w-3.5 h-3.5"></i>
                    <span>Watch Video Demonstration</span>
                </a>
            </div>
        </div>

        <div class="px-6 py-3 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end">
            <button type="button" onclick="closeViewExerciseModal()" class="px-4 py-2 text-xs font-semibold bg-slate-800 hover:bg-slate-900 text-white rounded-xl transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function openSingleAddModal() {
    document.getElementById('singleAddModal').classList.remove('hidden');
}

function closeSingleAddModal() {
    document.getElementById('singleAddModal').classList.add('hidden');
}

function openViewExerciseModal(ex) {
    document.getElementById('view_modal_title').innerText = ex.name || 'Exercise Details';
    document.getElementById('view_modal_category').innerText = ex.category ? ex.category.name : 'General Physiotherapy Protocol';
    document.getElementById('view_modal_sets').innerText = ex.sets || '-';
    document.getElementById('view_modal_reps').innerText = ex.reps || '-';
    document.getElementById('view_modal_duration').innerText = ex.duration || '-';
    document.getElementById('view_modal_body_part').innerText = ex.target_body_part || 'Full body / not specified';
    document.getElementById('view_modal_instructions').innerText = ex.instructions || 'No specific instructions logged.';

    const precCont = document.getElementById('view_modal_precautions_container');
    if (ex.precautions) {
        document.getElementById('view_modal_precautions').innerText = ex.precautions;
        precCont.classList.remove('hidden');
    } else {
        precCont.classList.add('hidden');
    }

    const vidCont = document.getElementById('view_modal_video_container');
    const vidLink = document.getElementById('view_modal_video_link');
    if (ex.video_url) {
        vidLink.href = ex.video_url;
        vidCont.classList.remove('hidden');
    } else {
        vidCont.classList.add('hidden');
    }

    document.getElementById('viewExerciseModal').classList.remove('hidden');
    lucide.createIcons();
}

function closeViewExerciseModal() {
    document.getElementById('viewExerciseModal').classList.add('hidden');
}
</script>
@endsection
