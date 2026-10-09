<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DoctorVideo extends Model
{
    use HasFactory;

    protected $table = 'doctor_videos';

    protected $fillable = [
        'doctor_id',
        'clinic_id',
        'category_id',
        'title',
        'slug',
        'description',
        'video_type',
        'video_url',
        'video_file',
        'thumbnail_url',
        'target_body_part',
        'difficulty_level',
        'duration',
        'share_token',
        'views_count',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'views_count' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($video) {
            if (empty($video->slug)) {
                $video->slug = Str::slug($video->title) . '-' . Str::random(5);
            }
            if (empty($video->share_token)) {
                $video->share_token = Str::random(24);
            }
        });
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function category()
    {
        return $this->belongsTo(TreatmentCategory::class, 'category_id');
    }

    /**
     * Extract YouTube ID if it is a YouTube video
     */
    public function getYoutubeIdAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = $this->video_url;
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            return $match[1];
        }

        return null;
    }

    /**
     * Get Embed URL for iframe or HTML5 video
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->video_type === 'youtube' || $this->youtube_id) {
            $id = $this->youtube_id;
            return $id ? "https://www.youtube.com/embed/{$id}?rel=0" : $this->video_url;
        }

        if ($this->video_type === 'upload' && $this->video_file) {
            return asset('storage/' . $this->video_file);
        }

        return $this->video_url;
    }

    /**
     * Get Thumbnail Image
     */
    public function getThumbnailAttribute(): string
    {
        if (!empty($this->thumbnail_url)) {
            return $this->thumbnail_url;
        }

        if ($this->youtube_id) {
            return "https://img.youtube.com/vi/{$this->youtube_id}/hqdefault.jpg";
        }

        return asset('images/default-video-thumb.jpg');
    }

    /**
     * Public shareable URL
     */
    public function getShareUrlAttribute(): string
    {
        return route('videos.public', $this->share_token);
    }
}
