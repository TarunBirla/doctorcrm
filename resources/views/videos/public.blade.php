<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $video->title }} - Physiotherapy Exercise Patient Portal</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased bg-slate-50">

    <!-- CLINIC HEADER BAR -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-4xl mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 flex items-center justify-center text-white font-black text-lg shadow-sm">
                    CP
                </div>
                <div>
                    <h1 class="text-sm font-bold text-slate-900 leading-tight uppercase">
                        {{ $video->clinic->name ?? 'CAREPOINT SUPER CLINIC' }}
                    </h1>
                    <p class="text-[10px] text-blue-600 font-semibold uppercase tracking-wider">
                        Physiotherapy & Rehabilitation Center
                    </p>
                </div>
            </div>

            @if($video->clinic && $video->clinic->phone)
                <a href="tel:{{ $video->clinic->phone }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-bold transition">
                    <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                    <span class="hidden sm:inline">Call Clinic:</span>
                    <span>{{ $video->clinic->phone }}</span>
                </a>
            @endif
        </div>
    </header>

    <!-- MAIN PATIENT VIDEO CONTAINER -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 py-6 sm:py-8 space-y-6">

        <!-- TITLE & DOCTOR BADGE -->
        <div class="space-y-2">
            <div class="flex flex-wrap items-center gap-2 text-xs">
                @if($video->category)
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold border border-rose-200">
                        {{ $video->category->name }}
                    </span>
                @endif
                @if($video->target_body_part)
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-semibold">
                        {{ $video->target_body_part }}
                    </span>
                @endif
                <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-semibold">
                    {{ $video->difficulty_level ?? 'Prescribed Exercise' }}
                </span>
                @if($video->duration)
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 font-semibold">
                        ⏱ {{ $video->duration }}
                    </span>
                @endif
            </div>

            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight leading-snug">
                {{ $video->title }}
            </h2>

            <div class="flex items-center gap-2 text-xs text-slate-600 pt-0.5">
                <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                <span>Prescribed by: <strong>{{ $video->doctor->name ?? 'Dr. Mahesh Sahu PT' }}</strong></span>
                @if($video->doctor && $video->doctor->qualification)
                    <span class="text-slate-400 hidden sm:inline">({{ $video->doctor->qualification }})</span>
                @endif
            </div>
        </div>

        <!-- VIDEO PLAYER BOX -->
        <div class="rounded-2xl bg-black overflow-hidden shadow-xl border border-slate-800">
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
                        <p class="font-bold text-sm">Video stream not available.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- INSTRUCTIONS & PRECAUTIONS -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-4 shadow-xs">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="clipboard-check" class="w-4 h-4 text-emerald-600"></i>
                <span>Doctor's Instructions For This Exercise</span>
            </h3>

            @if($video->description)
                <div class="p-4 bg-slate-50 rounded-xl text-xs sm:text-sm text-slate-700 font-semibold leading-relaxed whitespace-pre-line border border-slate-100">
                    {{ $video->description }}
                </div>
            @else
                <p class="text-xs text-slate-500">Perform the exercise gently and strictly within pain-free range of motion. Do not hold your breath.</p>
            @endif

            <!-- IMPORTANT SAFETY WARNING -->
            <div class="p-4 bg-amber-50/70 border border-amber-200 rounded-xl flex items-start gap-3 text-xs text-amber-900">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5"></i>
                <div class="space-y-1">
                    <span class="font-bold block">Important Safety Advice:</span>
                    <p class="text-amber-800">If you experience sharp pain, numbness, dizziness, or abnormal shortness of breath, please discontinue immediately and contact your physiotherapist.</p>
                </div>
            </div>
        </div>

        <!-- CLINIC CONTACT / ACTION CARD -->
        <div class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-md">
            <div class="space-y-1 text-center sm:text-left">
                <h4 class="text-base font-bold">{{ $video->clinic->name ?? 'CarePoint Physiotherapy Clinic' }}</h4>
                <p class="text-xs text-blue-200">Need personal guidance or follow-up consultation? Contact our clinic desk.</p>
                @if($video->clinic && $video->clinic->address)
                    <p class="text-[11px] text-blue-300 pt-1">📍 {{ $video->clinic->address }}, {{ $video->clinic->city }}</p>
                @endif
            </div>

            @if($video->clinic && $video->clinic->phone)
                <a href="tel:{{ $video->clinic->phone }}" class="px-5 py-3 bg-white hover:bg-slate-100 text-blue-900 rounded-xl text-xs font-black transition flex items-center gap-2 shadow-sm shrink-0">
                    <i data-lucide="phone" class="w-4 h-4 text-blue-600"></i>
                    <span>Contact {{ $video->clinic->phone }}</span>
                </a>
            @endif
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="py-6 border-t border-slate-200 text-center text-xs text-slate-400">
        <p>© {{ date('Y') }} {{ $video->clinic->name ?? 'Physiotherapy Center' }} • All rights reserved</p>
    </footer>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
