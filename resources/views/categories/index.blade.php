@extends('layouts.app')

@section('title', 'Physiotherapy Treatment Categories')
@section('breadcrumb', 'Categories')
@section('page_title', 'Physiotherapy Treatment & Protocol Categories')

@section('content')
<div class="space-y-6">

    <!-- KPI SUMMARY CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card-custom p-5 bg-white border border-slate-100 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Categories</span>
                <div class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $totalCategories }}</div>
                <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Specialized therapy segments</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white border border-slate-100 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">Active Status</span>
                <div class="text-3xl font-extrabold text-emerald-700 tracking-tight mt-1">{{ $activeCategories }}</div>
                <p class="text-[11px] text-slate-500 font-semibold mt-0.5">Available for patient booking</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="card-custom p-5 bg-white border border-slate-100 flex items-center justify-between shadow-xs">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 block">Quick Action</span>
                <div class="mt-2">
                    <button type="button" onclick="openAddCategoryModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span>Add New Category</span>
                    </button>
                </div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                <i data-lucide="sparkles" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER TOOLBAR -->
    <div class="card-custom p-4 bg-white border border-slate-100 shadow-xs flex flex-col md:flex-row items-center justify-between gap-3">
        <form action="{{ route('categories.index') }}" method="GET" class="flex-1 flex flex-wrap items-center gap-2 w-full">
            <div class="relative flex-1 min-w-[240px]">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search category name or description..." 
                       class="w-full pl-9 pr-3 py-2 text-xs bg-slate-50 hover:bg-slate-100/70 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none transition text-slate-800 font-semibold">
            </div>

            <select name="status" class="px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none text-slate-700 font-semibold">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold rounded-xl transition">
                Filter
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('categories.index') }}" class="px-3 py-2 text-xs text-rose-600 hover:bg-rose-50 rounded-xl font-semibold transition">
                    Clear
                </a>
            @endif
        </form>

        <button type="button" onclick="openAddCategoryModal()" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-sm transition">
            <i data-lucide="folder-plus" class="w-4 h-4"></i>
            <span>Create Category</span>
        </button>
    </div>

    <!-- CATEGORIES TABLE / LIST -->
    <div class="card-custom bg-white border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4">Category Name</th>
                        <th class="py-3.5 px-4">Therapy Description</th>
                        <th class="py-3.5 px-4 text-center">Exercises</th>
                        <th class="py-3.5 px-4 text-center">Patients</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="hover:bg-slate-50/60 transition group">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shrink-0 font-bold text-sm">
                                        {{ strtoupper(substr($category->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-blue-600 transition">{{ $category->name }}</div>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[10px] text-slate-400 font-semibold">{{ $category->slug }}</span>
                                            @if($category->doctor_id)
                                                <span class="text-[9px] px-1.5 py-0.2 bg-purple-50 text-purple-700 font-bold rounded border border-purple-200">Dr. Custom</span>
                                            @else
                                                <span class="text-[9px] px-1.5 py-0.2 bg-slate-100 text-slate-600 font-bold rounded">Standard</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs">
                                <p class="text-slate-600 text-[11px] line-clamp-2 leading-relaxed">
                                    {{ $category->description ?? 'No detailed description provided.' }}
                                </p>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('exercises.index', ['category_id' => $category->id]) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">
                                    <i data-lucide="dumbbell" class="w-3 h-3"></i>
                                    <span>{{ $category->exercises_count }}</span>
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-700">
                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px]">
                                    {{ $category->patients_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($category->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" 
                                            onclick='openEditCategoryModal(@json($category))' 
                                            class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" 
                                            title="Edit Category">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete category \'{{ $category->name }}\'?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Delete Category">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i data-lucide="layers" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="text-sm font-semibold text-slate-600">No treatment categories found.</p>
                                <p class="text-xs text-slate-400 mt-0.5">Click "Add New Category" above to create your first category.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ADD CATEGORY MODAL -->
<div id="addCategoryModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                    <i data-lucide="folder-plus" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Add Treatment Category</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">Define physiotherapy condition/specialty</p>
                </div>
            </div>
            <button type="button" onclick="closeAddCategoryModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Category Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Lumbar & Low Back Care" 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Clinical Description / Indications</label>
                <textarea name="description" rows="3" placeholder="Condition details, common complaints treated under this category..."
                          class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="add_is_active" value="1" checked class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                <label for="add_is_active" class="text-xs font-semibold text-slate-700">Active and available for patient booking</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeAddCategoryModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>

<!-- EDIT CATEGORY MODAL -->
<div id="editCategoryModal" class="fixed inset-0 z-50 bg-slate-900/40 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Edit Treatment Category</h3>
                    <p class="text-[11px] text-slate-500 font-semibold">Modify category details</p>
                </div>
            </div>
            <button type="button" onclick="closeEditCategoryModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="editCategoryForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Category Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="edit_category_name" required 
                       class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Clinical Description / Indications</label>
                <textarea name="description" id="edit_category_description" rows="3"
                          class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" name="is_active" id="edit_category_is_active" value="1" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                <label for="edit_category_is_active" class="text-xs font-semibold text-slate-700">Active and available for patient booking</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-slate-100">
                <button type="button" onclick="closeEditCategoryModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2 text-xs font-semibold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.remove('hidden');
}

function closeAddCategoryModal() {
    document.getElementById('addCategoryModal').classList.add('hidden');
}

function openEditCategoryModal(category) {
    const form = document.getElementById('editCategoryForm');
    form.action = `/categories/${category.id}`;
    document.getElementById('edit_category_name').value = category.name || '';
    document.getElementById('edit_category_description').value = category.description || '';
    document.getElementById('edit_category_is_active').checked = !!category.is_active;
    document.getElementById('editCategoryModal').classList.remove('hidden');
}

function closeEditCategoryModal() {
    document.getElementById('editCategoryModal').classList.add('hidden');
}
</script>
@endsection
