@extends('landing.layout')

@section('title', 'Clinic Gallery & Advanced Equipment - SDPC Indore')

@section('content')

<!-- Header Banner -->
<section class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-black uppercase tracking-widest border border-blue-400/30">
            Center Ambience & Technology
        </span>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight">Our Clinic & Equipment Gallery</h1>
        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-medium">
            Explore our state-of-the-art physiotherapy facilities, advanced robotic decompression machines, and private therapy cabins across our Indore centers.
        </p>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">INFRASTRUCTURE & FACILITIES</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Advanced Technology For Optimal Healing</h2>
            <p class="text-xs text-slate-500">World-class rehabilitation amenities in Khatiwala Tank and Mahalaxmi Nagar.</p>
        </div>

        @php
        $galleryItems = [
            ['title' => 'Flexion Distraction Spinal Decompression', 'cat' => 'Spine Center', 'icon' => 'fa-bed', 'color' => 'from-blue-600 to-indigo-700', 'desc' => 'Motorized disc decompression table for non-surgical disc restoration.'],
            ['title' => 'Super Inductive System (SIS)', 'cat' => 'High-Tech Modality', 'icon' => 'fa-magnet', 'color' => 'from-purple-600 to-pink-700', 'desc' => 'High-intensity electromagnetic therapy stimulating deep neuromuscular tissues.'],
            ['title' => 'Class 4 & 3 Deep Tissue Laser', 'cat' => 'Biostimulation', 'icon' => 'fa-wand-magic-sparkles', 'color' => 'from-rose-600 to-red-700', 'desc' => 'Cellular repair laser accelerating tissue regeneration and eliminating joint inflammation.'],
            ['title' => 'Matrix Rhythm Therapy (MaRhyThe)', 'cat' => 'Cellular Oscillation', 'icon' => 'fa-wave-square', 'color' => 'from-amber-600 to-orange-700', 'desc' => 'Biological resonant oscillations restoring metabolic cleansing and muscle elasticity.'],
            ['title' => 'Unweighing Gait Support System', 'cat' => 'Neuro Rehab', 'icon' => 'fa-person-walking', 'color' => 'from-emerald-600 to-teal-700', 'desc' => 'Zero-gravity bodyweight harness for stroke and paralysis gait training.'],
            ['title' => 'Chiropractic Drop Table', 'cat' => 'Spinal Adjustment', 'icon' => 'fa-arrows-up-down', 'color' => 'from-indigo-600 to-blue-800', 'desc' => 'Swedish drop-section mechanism for precise vertebral subluxation realignment.'],
            ['title' => 'Private Consultation & Assessment Chamber', 'cat' => 'Clinical OPD', 'icon' => 'fa-stethoscope', 'color' => 'from-slate-700 to-slate-900', 'desc' => 'Comprehensive physical examination and digital assessment suite.'],
            ['title' => 'Therapeutic Exercise & Posture Area', 'cat' => 'Rehabilitation', 'icon' => 'fa-dumbbell', 'color' => 'from-cyan-600 to-blue-700', 'desc' => 'Equipped with Swiss balls, resistance bands, spine aligners, and core rollers.'],
            ['title' => 'Patient Waiting & Reception Lounge', 'cat' => 'Facility', 'icon' => 'fa-couch', 'color' => 'from-violet-600 to-purple-800', 'desc' => 'Hygienic, comfortable, air-conditioned reception with token queue system.'],
        ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($galleryItems as $item)
            <div class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200 hover:shadow-lg transition group flex flex-col justify-between">
                <div>
                    <div class="h-48 w-full bg-gradient-to-br {{ $item['color'] }} flex flex-col items-center justify-center text-white p-6 relative overflow-hidden group-hover:scale-[1.02] transition">
                        <i class="fa-solid {{ $item['icon'] }} text-5xl opacity-90 mb-2"></i>
                        <span class="text-xs font-black uppercase tracking-wider bg-white/20 px-3 py-0.5 rounded-full backdrop-blur-xs">
                            {{ $item['cat'] }}
                        </span>
                    </div>
                    <div class="p-5">
                        <span class="text-[10px] font-black uppercase text-blue-700 tracking-wider">{{ $item['cat'] }}</span>
                        <h3 class="text-base font-black text-slate-900 mt-0.5">{{ $item['title'] }}</h3>
                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                </div>
                <div class="px-5 pb-5 pt-0">
                    <a href="{{ route('landing.enquiry') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 transition">
                        <span>Book Treatment On This Machine</span>
                        <i class="fa-solid fa-chevron-right text-[9px] text-blue-600"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

<!-- Call to action -->
<section class="py-12 bg-slate-900 text-white text-center">
    <div class="max-w-3xl mx-auto px-4 space-y-3">
        <h3 class="text-2xl font-black">Visit Our Modern Rehabilitation Centers</h3>
        <p class="text-xs text-slate-400">
            Centrally located in Indore at Khatiwala Tank and Mahalaxmi Nagar with parking and full accessibility.
        </p>
        <div class="pt-2">
            <a href="{{ route('landing.enquiry') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-900 bg-white hover:bg-slate-100 transition shadow">
                Schedule An In-Person Visit
            </a>
        </div>
    </div>
</section>

@endsection
