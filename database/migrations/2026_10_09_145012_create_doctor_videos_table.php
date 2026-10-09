<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->nullable()->constrained('doctors')->nullOnDelete();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('treatment_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('video_type')->default('youtube'); // youtube, vimeo, upload, external
            $table->text('video_url')->nullable();
            $table->string('video_file')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->string('target_body_part')->nullable();
            $table->string('difficulty_level')->nullable()->default('Beginner');
            $table->string('duration')->nullable();
            $table->string('share_token', 64)->unique()->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_videos');
    }
};
