@extends('landing.layout')

@section('title', 'Patient Testimonials & Video Reviews - SDPC Indore')

@section('content')

<!-- Header Banner -->
<section class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-black uppercase tracking-widest border border-blue-400/30">
            Voices of Satisfaction
        </span>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight">Happy Clients, Healthy Spines</h1>
        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-medium">
            Testimonials speak for themselves! Hear real patients describe their rapid recovery from severe spine, slip disc, joint pain, and paralysis.
        </p>
    </div>
</section>

<!-- Video Testimonials Section (The exact 9 videos from user's provided code) -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">VIDEO EXPERIENCES</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">Watch Our Patient Recovery Stories</h2>
            <p class="text-xs text-slate-500">Documented real results without painful surgeries or lifelong dependency on medicines.</p>
        </div>

        @php
        $videos = [
            ['id' => 'WD-mbm3yjAE', 'title' => 'Severe Spine & Disc Bulge Recovery', 'patient' => 'Recovery Story 1', 'desc' => 'Patient experienced massive sciatica relief and regained ability to walk freely.'],
            ['id' => '6uAsD6R8Q3o', 'title' => 'Cervical & Postural Neck Pain Relieved', 'patient' => 'Recovery Story 2', 'desc' => 'Chiropractic and physiotherapy restored neck mobility in just 7 days.'],
            ['id' => 'WgJSyO15u7c', 'title' => 'Knee Pain & Osteoarthritis Mobilization', 'patient' => 'Recovery Story 3', 'desc' => 'Strengthening and joint mobilization avoided planned knee surgery.'],
            ['id' => 'a65xo38gi2w', 'title' => 'Frozen Shoulder Complete Range Restored', 'patient' => 'Recovery Story 4', 'desc' => 'Patient regained full arm overhead elevation without painful injections.'],
            ['id' => '3_7Z7CECZdU', 'title' => 'Spinal Decompression & Sciatica Care', 'patient' => 'Recovery Story 5', 'desc' => 'Longstanding numbness and burning leg pain resolved through therapy.'],
            ['id' => '-KFYzTcmA-Q', 'title' => 'Neuro Rehabilitation & Hemiplegia Care', 'patient' => 'Recovery Story 6', 'desc' => 'Restored movement and independence following stroke recovery.'],
            ['id' => 'RZcmC6vy-BM', 'title' => 'Chronic Lumbago & Muscle Spasm Relief', 'patient' => 'Recovery Story 7', 'desc' => 'Targeted manual therapy and core stabilization provided lasting relief.'],
            ['id' => 'ET1FI26hLMc', 'title' => 'Tailbone (Coccydynia) & Pelvic Recovery', 'patient' => 'Recovery Story 8', 'desc' => 'Gentle osteopathic correction eliminated agonizing sitting pain.'],
            ['id' => 'aqv7dKRMBJg', 'title' => 'Complex Neuromuscular Restoration', 'patient' => 'Recovery Story 9', 'desc' => 'Regained posture and neuromuscular control with Dr. Mahesh Sahu PT.'],
        ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($videos as $v)
            <div class="bg-slate-50 rounded-2xl overflow-hidden border border-slate-200 hover:shadow-lg transition flex flex-col justify-between">
                <div>
                    <div class="aspect-video w-full bg-slate-900">
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $v['id'] }}" title="{{ $v['title'] }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center gap-1 text-amber-400 text-xs mb-1.5">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <span class="text-slate-400 text-[10px] ml-1">5.0 Verified</span>
                        </div>
                        <h3 class="text-base font-black text-slate-900">{{ $v['title'] }}</h3>
                        <p class="text-xs text-slate-600 mt-1.5 leading-relaxed">{{ $v['desc'] }}</p>
                    </div>
                </div>
                <div class="px-5 pb-5 pt-0">
                    <a href="{{ route('landing.enquiry') }}" class="w-full block text-center py-2 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 transition">
                        Consult For Similar Problem
                    </a>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

<!-- Call to action -->
<section class="py-12 bg-blue-700 text-white text-center">
    <div class="max-w-3xl mx-auto px-4 space-y-3">
        <h3 class="text-2xl font-black">Experience The Same Relief</h3>
        <p class="text-xs text-blue-100">
            Join over 50,000+ satisfied patients who regained active, pain-free lives under Dr. Mahesh Sahu PT.
        </p>
        <div class="pt-2">
            <a href="{{ route('landing.enquiry') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-black text-xs uppercase tracking-wider text-blue-900 bg-white hover:bg-slate-100 transition shadow">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Book Your Appointment Today</span>
            </a>
        </div>
    </div>
</section>

@endsection
