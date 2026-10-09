@extends('layouts.app')

@section('title', 'Rehabilitation Videos Library')
@section('breadcrumb', 'Clinical Care / Rehab Videos')
@section('page_title', 'Rehab & Exercise Videos Library')

@section('content')
<div class="space-y-6">

    <!-- TOP HEADER & STATS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <i data-lucide="video" class="w-6 h-6 text-rose-600"></i>
                <span>Physiotherapy & Rehab Video Library</span>
            </h2>
            <p class="text-xs text-slate-500 mt-1">Upload exercise demonstrations, educational guides, and share direct video links with your patients via WhatsApp.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('videos.create') }}" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm hover:shadow">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>+ Upload / Add New Video</span>
            </a>
        </div>
    </div>

    <!-- METRICS STRIP -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="card-custom p-4 bg-white flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i data-lucide="film" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-semibold block">Total Rehab Videos</span>
                <span class="text-2xl font-black text-slate-900">{{ $totalVideos }}</span>
            </div>
        </div>

        <div class="card-custom p-4 bg-white flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <i data-lucide="layers" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-semibold block">Condition Categories</span>
                <span class="text-2xl font-black text-slate-900">{{ $categories->count() }}</span>
            </div>
        </div>

        <div class="card-custom p-4 bg-white flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i data-lucide="eye" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-semibold block">Patient Views</span>
                <span class="text-2xl font-black text-slate-900">{{ number_format($totalViews) }}</span>
            </div>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="card-custom p-4 bg-white">
        <form method="GET" action="{{ route('videos.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="md:col-span-5 relative">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search videos by title, target body part or symptom..." 
                       class="w-full pl-10 pr-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-rose-500 font-semibold text-slate-800">
            </div>

            <!-- Category Filter -->
            <div class="md:col-span-4">
                <select name="category_id" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:bg-white focus:border-rose-500 font-semibold text-slate-800">
                    <option value="">All Categories ({{ $categories->count() }})</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit & Reset -->
            <div class="md:col-span-3 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Apply Filter</span>
                </button>
                @if(request()->hasAny(['search', 'category_id']))
                    <a href="{{ route('videos.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition" title="Clear Filters">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- VIDEOS GALLERY GRID -->
    @if($videos->isEmpty())
        <div class="card-custom p-12 bg-white text-center">
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-500 mx-auto flex items-center justify-center mb-4">
                <i data-lucide="video-off" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-slate-800">No Rehab Videos Found</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto mt-1 mb-6">Start building your clinic's digital rehabilitation video library. Upload YouTube videos, MP4 exercises, and share patient-specific home workout links easily.</p>
            <a href="{{ route('videos.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add First Video Now</span>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($videos as $video)
                <div class="card-custom bg-white overflow-hidden flex flex-col group hover:shadow-md hover:border-rose-200 transition duration-200">
                    <!-- THUMBNAIL / PREVIEW AREA -->
                    <div class="relative aspect-video bg-slate-900 overflow-hidden">
                        @if($video->youtube_id)
                            <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/mqdefault.jpg" 
                                 alt="{{ $video->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @elseif($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" 
                                 alt="{{ $video->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-tr from-slate-800 to-slate-900 text-slate-400">
                                <i data-lucide="film" class="w-10 h-10 text-slate-600 mb-1"></i>
                                <span class="text-[10px] uppercase font-bold tracking-wider">Rehab Exercise</span>
                            </div>
                        @endif

                        <!-- PLAY BUTTON OVERLAY -->
                        <a href="{{ route('videos.show', $video->id) }}" class="absolute inset-0 bg-black/30 group-hover:bg-black/10 flex items-center justify-center transition">
                            <div class="w-12 h-12 rounded-full bg-rose-600/90 text-white flex items-center justify-center shadow-lg group-hover:scale-110 group-hover:bg-rose-600 transition">
                                <i data-lucide="play" class="w-5 h-5 fill-white ml-0.5"></i>
                            </div>
                        </a>

                        <!-- DURATION BADGE -->
                        @if($video->duration)
                            <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-md bg-black/80 text-white text-[10px] font-mono font-bold">
                                {{ $video->duration }}
                            </span>
                        @endif

                        <!-- SOURCE BADGE -->
                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-wider {{ $video->video_type === 'youtube' ? 'bg-red-600 text-white' : 'bg-slate-800/90 text-white' }}">
                            {{ strtoupper($video->video_type) }}
                        </span>
                    </div>

                    <!-- CARD BODY -->
                    <div class="p-4 flex-1 flex flex-col justify-between space-y-3">
                        <div class="space-y-1.5">
                            <!-- Category & Body Part Tags -->
                            <div class="flex flex-wrap items-center gap-1.5 text-[10px]">
                                @if($video->category)
                                    <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 font-bold border border-rose-100">
                                        {{ $video->category->name }}
                                    </span>
                                @endif
                                @if($video->target_body_part)
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-semibold">
                                        {{ $video->target_body_part }}
                                    </span>
                                @endif
                            </div>

                            <!-- Title -->
                            <h4 class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-rose-600 transition">
                                <a href="{{ route('videos.show', $video->id) }}">{{ $video->title }}</a>
                            </h4>

                            <!-- Description Snippet -->
                            @if($video->description)
                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $video->description }}
                                </p>
                            @endif
                        </div>

                        <!-- FOOTER & SHARING ACTIONS -->
                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                            <!-- View Counter -->
                            <span class="text-[11px] text-slate-400 font-semibold flex items-center gap-1">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                <span>{{ $video->views_count }} views</span>
                            </span>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-1">
                                <!-- WhatsApp Share Button -->
                                @php
                                    $shareUrl = route('videos.public', $video->share_token);
                                    $waMsg = urlencode("Hello! Here is your prescribed Physiotherapy exercise video:\n*{$video->title}*\nWatch here: {$shareUrl}");
                                @endphp
                                <a href="https://api.whatsapp.com/send?text={{ $waMsg }}" target="_blank" 
                                   class="p-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-lg transition" title="Share via WhatsApp">
                                    <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                                </a>

                                <!-- Edit -->
                                <a href="{{ route('videos.edit', $video->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition" title="Edit Video">
                                    <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                </a>

                                <!-- Delete -->
                                <form method="POST" action="{{ route('videos.destroy', $video->id) }}" onsubmit="return confirm('Delete this video from library?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg transition" title="Delete Video">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- PAGINATION -->
        <div class="mt-6">
            {{ $videos->links() }}
        </div>
    @endif

</div>
@endsection
