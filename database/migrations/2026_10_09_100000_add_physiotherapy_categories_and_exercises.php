<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Treatment Categories table (Physiotherapy Specialties/Treatments)
        if (!Schema::hasTable('treatment_categories')) {
            Schema::create('treatment_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Exercises Library table
        if (!Schema::hasTable('exercises')) {
            Schema::create('exercises', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->foreignId('category_id')->nullable()->constrained('treatment_categories')->nullOnDelete();
                $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
                $table->string('target_body_part')->nullable(); // e.g. Neck, Shoulder, Low Back, Knee
                $table->string('sets')->nullable(); // e.g. 3 sets
                $table->string('reps')->nullable(); // e.g. 10-12 reps
                $table->string('duration')->nullable(); // e.g. 30 sec hold, 5 mins
                $table->text('instructions')->nullable();
                $table->text('precautions')->nullable();
                $table->string('video_url')->nullable();
                $table->string('image_path')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 3. Update Patients table for Physiotherapy workflow
        Schema::table('patients', function (Blueprint $table) {
            if (!Schema::hasColumn('patients', 'clinic_id')) {
                $table->foreignId('clinic_id')->nullable()->after('doctor_id')->constrained('clinics')->nullOnDelete();
            }
            if (!Schema::hasColumn('patients', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('clinic_id')->constrained('treatment_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('patients', 'description')) {
                $table->text('description')->nullable()->after('notes');
            }
        });

        // 4. Update Appointments table for days-based treatment & category
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('clinic_id')->constrained('treatment_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('appointments', 'treatment_days')) {
                $table->integer('treatment_days')->default(1)->after('appointment_date');
            }
            if (!Schema::hasColumn('appointments', 'daily_fee')) {
                $table->decimal('daily_fee', 10, 2)->nullable()->after('treatment_days');
            }
        });

        // Seed initial default Physiotherapy treatment categories
        $now = now();
        $defaultCategories = [
            ['name' => 'Cervical & Neck Rehabilitation', 'slug' => 'cervical-neck-rehab', 'description' => 'Therapy for cervical spondylosis, neck spasm, radiculopathy, and posture correction.'],
            ['name' => 'Lumbar & Low Back Care', 'slug' => 'lumbar-low-back-care', 'description' => 'Targeted spine decompression, core stabilization, sciatica relief, and disc rehabilitation.'],
            ['name' => 'Knee & Joint Mobilization', 'slug' => 'knee-joint-mobilization', 'description' => 'OA Knee management, ACL/PCL recovery, patellar tracking, and meniscus rehab.'],
            ['name' => 'Shoulder & Rotator Cuff Therapy', 'slug' => 'shoulder-rotator-cuff-therapy', 'description' => 'Frozen shoulder (adhesive capsulitis), impingement, and rotator cuff strengthening.'],
            ['name' => 'Post-Surgical Orthopedic Rehab', 'slug' => 'post-surgical-ortho-rehab', 'description' => 'Total knee/hip replacement rehab, fracture recovery, and arthroscopy follow-up care.'],
            ['name' => 'Neurological & Stroke Recovery', 'slug' => 'neurological-stroke-recovery', 'description' => 'Hemiplegia, Parkinsonism, facial palsy (Bell\'s palsy), and gait re-education.'],
            ['name' => 'Sports Injury & Conditioning', 'slug' => 'sports-injury-conditioning', 'description' => 'Ligament tears, ankle sprains, tennis elbow, and athletic performance restoration.'],
            ['name' => 'Pediatric & Postural Correction', 'slug' => 'pediatric-postural-correction', 'description' => 'Scoliosis, kyphosis, flat feet, and developmental milestone motor therapy.'],
        ];

        foreach ($defaultCategories as $cat) {
            $existing = DB::table('treatment_categories')->where('name', $cat['name'])->first();
            if (!$existing) {
                $catId = DB::table('treatment_categories')->insertGetId([
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'description' => $cat['description'],
                    'doctor_id' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // Add starter exercises for this category
                if ($cat['slug'] === 'cervical-neck-rehab') {
                    DB::table('exercises')->insert([
                        [
                            'name' => 'Chin Tucks & Deep Cervical Retraction',
                            'category_id' => $catId,
                            'target_body_part' => 'Cervical Spine / Neck',
                            'sets' => '3 Sets',
                            'reps' => '10 Reps',
                            'duration' => '5 sec hold',
                            'instructions' => 'Sit upright with shoulders relaxed. Look straight ahead, gently draw your chin straight back like making a gentle double chin. Hold 5 seconds and relax.',
                            'precautions' => 'Do not tilt head down; keep eye gaze level.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'name' => 'Isometric Neck Strengthening (4-Way)',
                            'category_id' => $catId,
                            'target_body_part' => 'Neck & Upper Trapezius',
                            'sets' => '2 Sets',
                            'reps' => '8 Reps',
                            'duration' => '10 sec hold each side',
                            'instructions' => 'Place palm on forehead and press gently without moving head. Repeat on back of head and both sides of temples.',
                            'precautions' => 'Maintain steady normal breathing; avoid sudden jerks.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    ]);
                } elseif ($cat['slug'] === 'lumbar-low-back-care') {
                    DB::table('exercises')->insert([
                        [
                            'name' => 'McKenzie Prone Press-Up (Extension)',
                            'category_id' => $catId,
                            'target_body_part' => 'Lumbar Spine / Lower Back',
                            'sets' => '3 Sets',
                            'reps' => '10 Reps',
                            'duration' => '3 sec hold',
                            'instructions' => 'Lie face down. Place palms flat under shoulders and press upward gently while keeping hips on the floor. Sag the lower back.',
                            'precautions' => 'Discontinue if peripheral radiating pain down the leg increases.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'name' => 'Cat & Camel Spinal Mobilization',
                            'category_id' => $catId,
                            'target_body_part' => 'Thoracolumbar Spine',
                            'sets' => '3 Sets',
                            'reps' => '12 Reps',
                            'duration' => 'Slow rhythmic flow',
                            'instructions' => 'Begin on all fours (hands and knees). Arch your spine upward like a cat, tucking chin. Then sag gently downward while lifting head.',
                            'precautions' => 'Keep movement gentle within pain-free active range.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'name' => 'Glute Bridge with Core Engagement',
                            'category_id' => $catId,
                            'target_body_part' => 'Glutes, Hamstrings & Core',
                            'sets' => '3 Sets',
                            'reps' => '12 Reps',
                            'duration' => '5 sec hold at top',
                            'instructions' => 'Lie on your back with knees bent and feet flat on floor. Squeeze glutes and lift pelvis until hips align with knees and shoulders.',
                            'precautions' => 'Avoid hyperextending low back at top of movement.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    ]);
                } elseif ($cat['slug'] === 'knee-joint-mobilization') {
                    DB::table('exercises')->insert([
                        [
                            'name' => 'Static Quadriceps Contraction (Quad Sets)',
                            'category_id' => $catId,
                            'target_body_part' => 'Knee / Vastus Medialis',
                            'sets' => '3 Sets',
                            'reps' => '15 Reps',
                            'duration' => '10 sec hold',
                            'instructions' => 'Sit or lie flat with leg straight. Place a small rolled towel under knee. Tighten thigh muscle and press back of knee downward into towel.',
                            'precautions' => 'Breathe normally; do not hold breath.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'name' => 'Straight Leg Raise (SLR)',
                            'category_id' => $catId,
                            'target_body_part' => 'Quadriceps & Hip Flexors',
                            'sets' => '3 Sets',
                            'reps' => '10 Reps',
                            'duration' => '5 sec hold',
                            'instructions' => 'Lie on back. Bend opposite knee. Keep affected leg locked straight, pull toes up towards face, lift leg 45 degrees, hold 5 sec, lower slowly.',
                            'precautions' => 'Keep knee strictly straight during entire movement.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    ]);
                } elseif ($cat['slug'] === 'shoulder-rotator-cuff-therapy') {
                    DB::table('exercises')->insert([
                        [
                            'name' => 'Codman\'s Pendulum Shoulder Swings',
                            'category_id' => $catId,
                            'target_body_part' => 'Glenohumeral Joint / Shoulder',
                            'sets' => '2 Sets',
                            'reps' => '20 Swings',
                            'duration' => 'Clockwise & counter-clockwise',
                            'instructions' => 'Lean forward supporting non-affected arm on a sturdy table. Let affected arm dangle freely. Use body torso momentum to gently swing arm in circles.',
                            'precautions' => 'Keep arm muscles relaxed; let gravity and body momentum drive swing.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ],
                        [
                            'name' => 'Scapular Wall Slides (Wall Angels)',
                            'category_id' => $catId,
                            'target_body_part' => 'Scapular Stabilizers / Trapezius',
                            'sets' => '3 Sets',
                            'reps' => '10 Reps',
                            'duration' => 'Smooth controlled slide',
                            'instructions' => 'Stand back against wall. Keep elbows and wrists in contact with wall. Slide arms overhead while maintaining wall contact without arching back.',
                            'precautions' => 'Lower arms if sharp anterior shoulder pain occurs.',
                            'is_active' => true,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'daily_fee')) {
                $table->dropColumn('daily_fee');
            }
            if (Schema::hasColumn('appointments', 'treatment_days')) {
                $table->dropColumn('treatment_days');
            }
            if (Schema::hasColumn('appointments', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });

        Schema::table('patients', function (Blueprint $table) {
            if (Schema::hasColumn('patients', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('patients', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
            if (Schema::hasColumn('patients', 'clinic_id')) {
                $table->dropForeign(['clinic_id']);
                $table->dropColumn('clinic_id');
            }
        });

        Schema::dropIfExists('exercises');
        Schema::dropIfExists('treatment_categories');
    }
};
