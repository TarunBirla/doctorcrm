@extends('layouts.app')

@section('title', 'Edit Video: ' . $video->title)
@section('breadcrumb', 'Rehab Videos / Edit')
@section('page_title', 'Edit Video Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">

    <!-- BACK BAR -->
    <div class="flex items-center justify-between">
        <a href="{{ route('videos.show', $video->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Video Details</span>
        </a>
        <span class="text-xs text-slate-400 font-semibold">Editing Video #{{ $video->id }}</span>
    </div>

    <!-- MAIN FORM CARD -->
    <div class="card-custom bg-white p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-4 mb-6">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="pencil" class="w-5 h-5 text-rose-600"></i>
                <span>Edit Video: {{ $video->title }}</span>
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">Update exercise directions, target body areas, or change the video source.</p>
        </div>

        <form method="POST" action="{{ route('videos.update', $video->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- BASIC DETAILS -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Video Title *
                    </label>
                    <input type="text" name="title" value="{{ old('title', $video->title) }}" required 
                           class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white focus:border-rose-500 font-bold outline-none text-slate-900">
                </div>

                <!-- Treatment Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Clinical Category
                    </label>
                    <select name="category_id" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white focus:border-rose-500 font-semibold outline-none text-slate-900">
                        <option value="">-- General Physiotherapy --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $video->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Target Body Part -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Target Body Part / Joint
                    </label>
                    <input type="text" name="target_body_part" value="{{ old('target_body_part', $video->target_body_part) }}" 
                           placeholder="e.g. Cervical Spine, Knee, Shoulder"
                           class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white focus:border-rose-500 font-semibold outline-none text-slate-900">
                </div>

                <!-- Difficulty & Duration -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Difficulty Level
                    </label>
                    <select name="difficulty_level" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white focus:border-rose-500 font-semibold outline-none text-slate-900">
                        <option value="Beginner" {{ old('difficulty_level', $video->difficulty_level) == 'Beginner' ? 'selected' : '' }}>Beginner / Acute Phase</option>
                        <option value="Intermediate" {{ old('difficulty_level', $video->difficulty_level) == 'Intermediate' ? 'selected' : '' }}>Intermediate / Sub-acute</option>
                        <option value="Advanced" {{ old('difficulty_level', $video->difficulty_level) == 'Advanced' ? 'selected' : '' }}>Advanced / Strengthening</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Duration / Time
                    </label>
                    <input type="text" name="duration" value="{{ old('duration', $video->duration) }}" 
                           class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white focus:border-rose-500 font-semibold outline-none text-slate-900">
                </div>

                <!-- Clinic Affiliation -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Clinic / Branch (Optional)
                    </label>
                    <select name="clinic_id" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white focus:border-rose-500 font-semibold outline-none text-slate-900">
                        <option value="">-- Available to all clinic patients --</option>
                        @foreach($clinics as $c)
                            <option value="{{ $c->id }}" {{ old('clinic_id', $video->clinic_id) == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->city }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- VIDEO SOURCE SECTION -->
            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-4">
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                    Video Source Type *
                </label>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-rose-300 transition">
                        <input type="radio" name="video_type" value="youtube" {{ old('video_type', $video->video_type) === 'youtube' ? 'checked' : '' }} 
                               onchange="toggleVideoInputs(this.value)" class="text-rose-600 focus:ring-rose-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">YouTube Video</span>
                            <span class="text-[10px] text-slate-400">Paste YouTube link</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-rose-300 transition">
                        <input type="radio" name="video_type" value="upload" {{ old('video_type', $video->video_type) === 'upload' ? 'checked' : '' }} 
                               onchange="toggleVideoInputs(this.value)" class="text-rose-600 focus:ring-rose-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Upload Video File</span>
                            <span class="text-[10px] text-slate-400">Replace current video</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3.5 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-rose-300 transition">
                        <input type="radio" name="video_type" value="external" {{ old('video_type', $video->video_type) === 'external' ? 'checked' : '' }} 
                               onchange="toggleVideoInputs(this.value)" class="text-rose-600 focus:ring-rose-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">External URL</span>
                            <span class="text-[10px] text-slate-400">Direct MP4/Vimeo link</span>
                        </div>
                    </label>
                </div>

                <!-- URL INPUT -->
                <div id="videoUrlGroup">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Video URL / YouTube Link
                    </label>
                    <input type="url" name="video_url" id="videoUrlInput" value="{{ old('video_url', $video->video_url) }}" 
                           placeholder="https://www.youtube.com/watch?v=..."
                           class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl bg-white focus:border-rose-500 font-semibold outline-none text-slate-900">
                </div>

                <!-- FILE INPUT -->
                <div id="videoFileGroup" class="hidden">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Select Video File (Leave empty to keep existing)
                    </label>
                    <input type="file" name="video_file" id="videoFileInput" accept="video/mp4,video/webm,video/quicktime"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                    @if($video->video_file)
                        <p class="text-[11px] text-emerald-600 font-semibold mt-1">Current File: {{ basename($video->video_file) }}</p>
                    @endif
                </div>
            </div>

            <!-- DESCRIPTION & CLINICAL DIRECTIONS -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Exercise Instructions & Safety Precautions for Patient
                </label>
                <textarea name="description" rows="4" 
                          class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl bg-slate-50/50 focus:bg-white focus:border-rose-500 font-semibold outline-none text-slate-900 leading-relaxed">{{ old('description', $video->description) }}</textarea>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('videos.show', $video->id) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-sm hover:shadow transition flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Update Video</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleVideoInputs(type) {
        const urlGroup = document.getElementById('videoUrlGroup');
        const fileGroup = document.getElementById('videoFileGroup');
        if (type === 'upload') {
            urlGroup.classList.add('hidden');
            fileGroup.classList.remove('hidden');
        } else {
            urlGroup.classList.remove('hidden');
            fileGroup.classList.add('hidden');
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const checked = document.querySelector('input[name="video_type"]:checked');
        if (checked) {
            toggleVideoInputs(checked.value);
        }
    });
</script>
@endpush
