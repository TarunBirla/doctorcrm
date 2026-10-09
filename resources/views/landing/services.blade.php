@extends('landing.layout')

@section('title', 'Services & Musculoskeletal Conditions - SDPC Indore')

@section('content')

<!-- Header Banner -->
<section class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 text-xs font-black uppercase tracking-widest border border-blue-400/30">
            Clinical Services & Treatments
        </span>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight">Musculoskeletal & Neurological Conditions</h1>
        <p class="text-sm sm:text-base text-slate-300 max-w-2xl mx-auto font-medium">
            Seamlessly blending Physiotherapy, Osteopathy, and Chiropractic care to treat the root causes of musculoskeletal and neurological health problems.
        </p>
    </div>
</section>

<!-- 3 Treatment Modalities -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto space-y-2 mb-12">
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">HOLISTIC TRIPLE APPROACH</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Physiotherapy &bull; Osteopathy &bull; Chiropractic</h2>
            <p class="text-xs sm:text-sm text-slate-600">
                Over 80% of health issues arise due to mechanical imbalances that respond best to physical interventions rather than chronic medicine or surgery.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-slate-50 p-7 rounded-2xl border border-slate-200 hover:border-blue-500 transition">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-stethoscope"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Modern Physiotherapy</h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Targeted neuromuscular re-education, electrotherapy, deep heat ultrasound, strengthening protocols, and pain-relieving therapeutic exercises.
                </p>
                <ul class="text-xs text-slate-600 space-y-1.5">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Post-surgical rehab & TKR/THR</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Muscle spasm & trigger point release</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Neurological gait & balance training</li>
                </ul>
            </div>

            <div class="bg-slate-50 p-7 rounded-2xl border border-slate-200 hover:border-blue-500 transition">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purplePrimary flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-hand-holding-medical"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Clinical Osteopathy</h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Gentle manual manipulation focused on body framework harmony, fascial release, visceral mobility, and circulatory fluid optimization.
                </p>
                <ul class="text-xs text-slate-600 space-y-1.5">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Craniosacral & spinal rhythm release</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Acidity, constipation & pelvic floor relief</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Myofascial decompression & strain correction</li>
                </ul>
            </div>

            <div class="bg-slate-50 p-7 rounded-2xl border border-slate-200 hover:border-blue-500 transition">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-bone"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 mb-2">Swedish Chiropractic</h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    High-precision spinal adjustments using specialised drop tables to restore subluxated vertebrae, taking pressure off compressed nerves.
                </p>
                <ul class="text-xs text-slate-600 space-y-1.5">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Slip disc & sciatica decompression</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Cervical alignment & headache relief</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-emerald-600 text-[10px]"></i> Sacroiliac (SI) joint realignment</li>
                </ul>
            </div>
        </div>

    </div>
</section>

<!-- Detailed Musculoskeletal Conditions Accordion (From Prompt) -->
<section class="py-16 bg-slate-50 border-t border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center space-y-2 mb-10">
            <span class="text-xs font-black text-blue-700 uppercase tracking-widest">CONDITIONS DIRECTORY</span>
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900">Musculoskeletal & Neurological Conditions</h2>
            <p class="text-xs text-slate-500">Click any anatomical category below to review treated conditions</p>
        </div>

        <div class="space-y-3">
            
            <!-- Accordion 1: Shoulder -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                <button type="button" onclick="toggleAcc(this)" class="w-full p-5 text-left flex items-center justify-between font-black text-sm text-slate-900 hover:bg-slate-50 transition">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-child-reaching text-blue-600"></i>
                        <span>Shoulder Conditions</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 transition-transform"></i>
                </button>
                <div class="hidden px-5 pb-5 pt-2 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50">
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-blue-600"></i> Adhesive Capsulitis (Frozen Shoulder)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-blue-600"></i> Rotator Cuff Tendonitis & Partial Tears</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-blue-600"></i> Subacromial Impingement Syndrome</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-blue-600"></i> Deltoid Muscle Atrophy & Weakness</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-blue-600"></i> Biceps Tendonitis & Calcific Tendinopathy</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-blue-600"></i> Infraspinatus & Supraspinatus Spasm</li>
                    </ul>
                </div>
            </div>

            <!-- Accordion 2: Arm & Elbow -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                <button type="button" onclick="toggleAcc(this)" class="w-full p-5 text-left flex items-center justify-between font-black text-sm text-slate-900 hover:bg-slate-50 transition">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-hand-fist text-purplePrimary"></i>
                        <span>Arm & Elbow Conditions</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 transition-transform"></i>
                </button>
                <div class="hidden px-5 pb-5 pt-2 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50">
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-purplePrimary"></i> Tennis Elbow (Lateral Epicondylitis)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-purplePrimary"></i> Golfer's Elbow (Medial Epicondylitis)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-purplePrimary"></i> Arm Muscle Cramps & Fascial Tightness</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-purplePrimary"></i> Arm Numbness & Radicular Paresthesias</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-purplePrimary"></i> Carpal Tunnel Syndrome & Median Nerve Entrapment</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-purplePrimary"></i> Ulnar Nerve Compression & Cubital Tunnel</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-purplePrimary"></i> Post-Fracture Arm Stiffness & Healing</li>
                    </ul>
                </div>
            </div>

            <!-- Accordion 3: Hand & Wrist -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                <button type="button" onclick="toggleAcc(this)" class="w-full p-5 text-left flex items-center justify-between font-black text-sm text-slate-900 hover:bg-slate-50 transition">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-hand text-emerald-600"></i>
                        <span>Hand & Wrist Conditions</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 transition-transform"></i>
                </button>
                <div class="hidden px-5 pb-5 pt-2 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50">
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-emerald-600"></i> Chronic Wrist Pain & Joint Instability</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-emerald-600"></i> Wrist Ligament Sprain & TFCC Tears</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-emerald-600"></i> De Quervain's Tenosynovitis (Thumb Tendonitis)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-emerald-600"></i> Trigger Finger & Palmar Fasciitis</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-emerald-600"></i> Osteoarthritis of Hand & Finger Joints</li>
                    </ul>
                </div>
            </div>

            <!-- Accordion 4: Back & Spine -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                <button type="button" onclick="toggleAcc(this)" class="w-full p-5 text-left flex items-center justify-between font-black text-sm text-slate-900 hover:bg-slate-50 transition">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-bone text-rose-600"></i>
                        <span>Back & Spine Conditions</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 transition-transform"></i>
                </button>
                <div class="hidden px-5 pb-5 pt-2 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50">
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-rose-600"></i> Lower Back Pain & Acute Lumbago</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-rose-600"></i> Lumbar Spondylosis & Facet Arthropathy</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-rose-600"></i> Herniated / Bulging Disc (L4-L5, L5-S1)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-rose-600"></i> Sciatica Nerve Compression</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-rose-600"></i> Spinal Canal Stenosis</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-rose-600"></i> Spondylolisthesis (Vertebral Slippage)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-rose-600"></i> Scoliosis & Kyphosis Spinal Deformities</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-rose-600"></i> Coccydynia (Tailbone Pain)</li>
                    </ul>
                </div>
            </div>

            <!-- Accordion 5: Hip & Leg -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                <button type="button" onclick="toggleAcc(this)" class="w-full p-5 text-left flex items-center justify-between font-black text-sm text-slate-900 hover:bg-slate-50 transition">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-person-walking text-amber-600"></i>
                        <span>Hip, Knee & Leg Conditions</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 transition-transform"></i>
                </button>
                <div class="hidden px-5 pb-5 pt-2 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50">
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-amber-600"></i> Knee Osteoarthritis & Cartilage Wear</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-amber-600"></i> Meniscus Tear & Joint Effusion</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-amber-600"></i> Ligament Injury (ACL, PCL, MCL, LCL)</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-amber-600"></i> Trochanteric Bursitis & Hip Joint Impingement</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-amber-600"></i> Avascular Necrosis (AVN) of Hip</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-amber-600"></i> Hamstring Strain & Quadriceps Muscle Tears</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-amber-600"></i> Achilles Tendonitis & Calf Muscle Cramps</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-amber-600"></i> Plantar Fasciitis & Calcaneal Heel Spurs</li>
                    </ul>
                </div>
            </div>

            <!-- Accordion 6: Neurological & Systemic -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xs">
                <button type="button" onclick="toggleAcc(this)" class="w-full p-5 text-left flex items-center justify-between font-black text-sm text-slate-900 hover:bg-slate-50 transition">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-brain text-indigo-600"></i>
                        <span>Neurological & Other Conditions</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-slate-400 transition-transform"></i>
                </button>
                <div class="hidden px-5 pb-5 pt-2 text-xs text-slate-600 border-t border-slate-100 bg-slate-50/50">
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-600"></i> Hemiplegia & Post-Stroke Recovery</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-600"></i> Bell's Palsy & Facial Nerve Dysfunction</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-600"></i> Parkinson's Disease Movement Therapy</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-600"></i> Cerebral Ataxia & Balance Impairment</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-600"></i> Guillain-Barré Syndrome (GBS) Recovery</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-600"></i> Motor Neuron Disease (MND) Conditioning</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-600"></i> Piriformis Muscle Syndrome & Sciatic Trapping</li>
                        <li class="flex items-center gap-2"><i class="fa-solid fa-circle text-[6px] text-indigo-600"></i> Fibromyalgia & Chronic Myofascial Pain</li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="text-center mt-10">
            <a href="{{ route('landing.enquiry') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-wider text-white bg-blue-700 hover:bg-blue-800 shadow-md transition">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Book Doctor Evaluation For Your Condition</span>
            </a>
        </div>

    </div>
</section>

@endsection

@push('scripts')
<script>
    function toggleAcc(btn) {
        const panel = btn.nextElementSibling;
        const icon = btn.querySelector('.fa-chevron-down');
        const isHidden = panel.classList.contains('hidden');

        // close other open accordions
        document.querySelectorAll('.space-y-3 > div > div').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.space-y-3 > div button .fa-chevron-down').forEach(i => i.classList.remove('rotate-180'));

        if (isHidden) {
            panel.classList.remove('hidden');
            icon.classList.add('rotate-180');
        }
    }
</script>
@endpush
