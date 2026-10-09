@extends('layouts.app')

@section('title', $video->title)
@section('breadcrumb', 'Rehab Videos / ' . Str::limit($video->title, 25))
@section('page_title', 'Exercise Video Player & Share')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto pb-12">

    <!-- ACTION HEADER BAR -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('videos.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800 flex items-center gap-1.5 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Back to Video Library</span>
        </a>

        <!-- QUICK SHARING & EDIT BUTTONS -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- WhatsApp Share -->
            @php
                $shareUrl = route('videos.public', $video->share_token);
                $waText = urlencode("Hello! Here is your prescribed Physiotherapy exercise video:\n*{$video->title}*\n\nWatch here: {$shareUrl}");
            @endphp
            <a href="https://api.whatsapp.com/send?text={{ $waText }}" target="_blank" 
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <i data-lucide="share-2" class="w-4 h-4"></i>
                <span>Share via WhatsApp</span>
            </a>

            <!-- Copy Link Button -->
            <button onclick="copyShareLink('{{ $shareUrl }}')" 
                    class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i data-lucide="copy" class="w-4 h-4"></i>
                <span id="copyBtnText">Copy Public Link</span>
            </button>

            <!-- Edit Button -->
            <a href="{{ route('videos.edit', $video->id) }}" 
               class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
                <i data-lucide="pencil" class="w-4 h-4"></i>
                <span>Edit</span>
            </a>
        </div>
    </div>

    <!-- MAIN PLAYER & DETAILS SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- VIDEO PLAYER & INSTRUCTIONS (LEFT 2 COLS) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- RESPONSIVE VIDEO PLAYER FRAME -->
            <div class="card-custom bg-black overflow-hidden shadow-lg border-0">
                <div class="relative aspect-video w-full bg-black flex items-center justify-center">
                    @if($video->video_type === 'youtube' && $video->youtube_id)
                        <iframe class="w-full h-full" 
                                src="https://www.youtube.com/embed/{{ $video->youtube_id }}?rel=0&autoplay=0" 
                                title="{{ $video->title }}" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                allowfullscreen></iframe>
                    @elseif($video->video_type === 'upload' && $video->video_file)
                        <video class="w-full h-full" controls playsinline preload="metadata">
                            <source src="{{ asset('storage/' . $video->video_file) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @elseif($video->video_url)
                        <iframe class="w-full h-full" src="{{ $video->video_url }}" allowfullscreen frameborder="0"></iframe>
                    @else
                        <div class="text-center p-8 text-slate-400">
                            <i data-lucide="alert-circle" class="w-10 h-10 mx-auto text-amber-500 mb-2"></i>
                            <p class="font-bold text-sm">No valid video stream available.</p>
                            <p class="text-xs text-slate-500 mt-1">Please edit the video to provide a working YouTube link or file upload.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- TITLE & METADATA CARD -->
            <div class="card-custom p-6 bg-white space-y-4">
                <div class="space-y-2">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if($video->category)
                            <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 font-bold border border-rose-200">
                                {{ $video->category->name }}
                            </span>
                        @endif
                        @if($video->target_body_part)
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-semibold">
                                <i data-lucide="target" class="w-3.5 h-3.5 inline mr-1 text-slate-400"></i>{{ $video->target_body_part }}
                            </span>
                        @endif
                        <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 font-semibold">
                            Level: {{ $video->difficulty_level ?? 'Beginner' }}
                        </span>
                        @if($video->duration)
                            <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 font-semibold">
                                <i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1 text-amber-500"></i>{{ $video->duration }}
                            </span>
                        @endif
                    </div>

                    <h2 class="text-xl font-black text-slate-900 tracking-tight">{{ $video->title }}</h2>

                    <div class="flex items-center gap-4 text-xs text-slate-400 pt-1">
                        <span class="flex items-center gap-1">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span>{{ $video->views_count }} patient views</span>
                        </span>
                        <span>•</span>
                        <span>Added on {{ $video->created_at->format('d M Y') }}</span>
                    </div>
                </div>

                <!-- CLINICAL INSTRUCTIONS -->
                @if($video->description)
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <i data-lucide="file-text" class="w-4 h-4 text-rose-500"></i>
                            <span>Rehab Protocol & Instructions for Patient</span>
                        </h4>
                        <div class="p-4 bg-slate-50/80 rounded-xl text-xs text-slate-700 font-semibold leading-relaxed whitespace-pre-line border border-slate-200/60">
                            {{ $video->description }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- SHARE WIDGET & RELATED (RIGHT 1 COL) -->
        <div class="space-y-6">

            <!-- PATIENT SHARE CARD -->
            <div class="card-custom p-5 bg-gradient-to-br from-white to-rose-50/30 border-rose-200 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <i data-lucide="send" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">Share with Patient</h4>
                        <p class="text-[11px] text-slate-500">Patient can view without CRM login</p>
                    </div>
                </div>

                <!-- Public Link Box -->
                <div class="p-2.5 bg-white border border-slate-200 rounded-xl flex items-center justify-between gap-2">
                    <span class="text-[11px] font-mono text-slate-600 truncate select-all">{{ $shareUrl }}</span>
                    <button onclick="copyShareLink('{{ $shareUrl }}')" class="p-1 hover:bg-slate-100 rounded text-slate-500 hover:text-slate-800" title="Copy">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Direct WhatsApp Action Button -->
                <a href="https://api.whatsapp.com/send?text={{ $waText }}" target="_blank" 
                   class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-xs">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Send via WhatsApp</span>
                </a>

                <!-- Preview Link as Patient -->
                <a href="{{ $shareUrl }}" target="_blank" 
                   class="w-full py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                    <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-400"></i>
                    <span>Open Patient View Portal</span>
                </a>
            </div>

            <!-- CLINIC & DOCTOR INFO -->
            <div class="card-custom p-5 bg-white space-y-3">
                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Practice Information</h4>
                <div class="text-xs space-y-2 text-slate-600">
                    <p class="flex items-center gap-2 font-semibold">
                        <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                        <span>Doctor: <strong>{{ $video->doctor->name ?? 'Dr. Mahesh Sahu PT' }}</strong></span>
                    </p>
                    @if($video->clinic)
                        <p class="flex items-center gap-2">
                            <i data-lucide="building" class="w-4 h-4 text-slate-400"></i>
                            <span>Clinic: <strong>{{ $video->clinic->name }}</strong> ({{ $video->clinic->city }})</span>
                        </p>
                    @endif
                </div>
            </div>

            <!-- RELATED VIDEOS IN THIS CATEGORY -->
            @if($relatedVideos->isNotEmpty())
                <div class="card-custom p-5 bg-white space-y-3">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Related Videos</h4>
                    <div class="space-y-2.5">
                        @foreach($relatedVideos as $rel)
                            <a href="{{ route('videos.show', $rel->id) }}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition group">
                                <div class="w-16 h-10 bg-slate-900 rounded-lg overflow-hidden shrink-0">
                                    @if($rel->youtube_id)
                                        <img src="https://img.youtube.com/vi/{{ $rel->youtube_id }}/mqdefault.jpg" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-800 text-slate-500">
                                            <i data-lucide="play" class="w-3 h-3"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h5 class="text-xs font-bold text-slate-800 group-hover:text-rose-600 truncate">{{ $rel->title }}</h5>
                                    <span class="text-[10px] text-slate-400">{{ $rel->duration ?? 'Exercise' }} • {{ $rel->views_count }} views</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    function copyShareLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            const btnText = document.getElementById('copyBtnText');
            if (btnText) {
                const old = btnText.innerText;
                btnText.innerText = 'Copied!';
                setTimeout(() => { btnText.innerText = old; }, 2000);
            }
            alert('Patient video link copied to clipboard: ' + url);
        }).catch(err => {
            prompt('Copy this link:', url);
        });
    }
</script>
@endpush
