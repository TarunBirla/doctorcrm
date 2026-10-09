@extends('layouts.app')

@section('title', 'Edit Exercise Protocol')
@section('breadcrumb', 'Exercises / Edit')
@section('page_title', 'Edit Physiotherapy Exercise Protocol')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-extrabold text-slate-900">Edit Exercise: {{ $exercise->name }}</h3>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">Update rehab protocol, sets, reps, and instructions</p>
        </div>
        <a href="{{ route('exercises.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl transition">
            Back to Library
        </a>
    </div>

    <div class="card-custom p-6 bg-white border border-slate-100 shadow-xs">
        <form action="{{ route('exercises.update', $exercise->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Exercise Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $exercise->name) }}" required 
                       class="w-full px-3.5 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Treatment Category</label>
                    <select name="category_id" class="w-full px-3 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-700">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $exercise->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Anatomy / Body Part</label>
                    <input type="text" name="target_body_part" value="{{ old('target_body_part', $exercise->target_body_part) }}" placeholder="e.g. Cervical / Spine" 
                           class="w-full px-3.5 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Sets</label>
                    <input type="text" name="sets" value="{{ old('sets', $exercise->sets) }}" placeholder="e.g. 3 Sets" 
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Reps</label>
                    <input type="text" name="reps" value="{{ old('reps', $exercise->reps) }}" placeholder="e.g. 10 Reps" 
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Hold / Duration</label>
                    <input type="text" name="duration" value="{{ old('duration', $exercise->duration) }}" placeholder="e.g. 5 sec hold" 
                           class="w-full px-3.5 py-2 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Instructions / Execution Technique</label>
                <textarea name="instructions" rows="3" placeholder="Step-by-step guidance for the patient..."
                          class="w-full px-3.5 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">{{ old('instructions', $exercise->instructions) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Precautions & Contraindications</label>
                <textarea name="precautions" rows="2" placeholder="Signs of pain to avoid, restrictions..."
                          class="w-full px-3.5 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">{{ old('precautions', $exercise->precautions) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Video Demonstration URL (YouTube, Vimeo, etc.)</label>
                <input type="url" name="video_url" value="{{ old('video_url', $exercise->video_url) }}" placeholder="https://..." 
                       class="w-full px-3.5 py-2.5 text-xs bg-slate-50 focus:bg-white border border-slate-200 focus:border-blue-500 rounded-xl outline-none font-semibold text-slate-800 transition">
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="edit_is_active" value="1" {{ old('is_active', $exercise->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                <label for="edit_is_active" class="text-xs font-semibold text-slate-700">Active and visible in prescriptions</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('exercises.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition">
                    Update Exercise
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
