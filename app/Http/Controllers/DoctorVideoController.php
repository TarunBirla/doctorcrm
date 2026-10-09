<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorVideo;
use App\Models\TreatmentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoctorVideoController extends Controller
{
    /**
     * Display a listing of the videos.
     */
    public function index(Request $request)
    {
        $query = DoctorVideo::with(['category', 'doctor', 'clinic'])->latest();

        // Filter by Category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by Title or Body Part
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('target_body_part', 'like', "%{$search}%");
            });
        }

        $videos = $query->paginate(12)->withQueryString();
        $categories = TreatmentCategory::active()->orderBy('name')->get();

        $totalVideos = DoctorVideo::count();
        $totalViews = DoctorVideo::sum('views_count');

        return view('videos.index', compact('videos', 'categories', 'totalVideos', 'totalViews'));
    }

    /**
     * Show the form for creating a new video.
     */
    public function create()
    {
        $categories = TreatmentCategory::active()->orderBy('name')->get();
        $clinics = Clinic::where('is_active', true)->orderBy('name')->get();

        $loggedInDoctor = null;
        if (auth()->check()) {
            $loggedInDoctor = Doctor::where('user_id', auth()->id())->first();
        }

        return view('videos.create', compact('categories', 'clinics', 'loggedInDoctor'));
    }

    /**
     * Store a newly created video in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:treatment_categories,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'video_type' => 'required|in:youtube,upload,external',
            'video_url' => 'nullable|required_if:video_type,youtube,external|string|max:1000',
            'video_file' => 'nullable|required_if:video_type,upload|file|mimes:mp4,mov,webm,ogg,mkv|max:102400', // 100MB max
            'thumbnail_url' => 'nullable|url|max:500',
            'target_body_part' => 'nullable|string|max:100',
            'difficulty_level' => 'nullable|string|max:50',
            'duration' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:2000',
        ]);

        $filePath = null;
        if ($request->hasFile('video_file')) {
            $filePath = $request->file('video_file')->store('videos', 'public');
        }

        $doctorId = null;
        if (auth()->check()) {
            $doc = Doctor::where('user_id', auth()->id())->first();
            if ($doc) {
                $doctorId = $doc->id;
            }
        }

        $video = DoctorVideo::create([
            'doctor_id' => $doctorId,
            'clinic_id' => $validated['clinic_id'] ?? null,
            'category_id' => $validated['category_id'] ?? null,
            'title' => $validated['title'],
            'video_type' => $validated['video_type'],
            'video_url' => $validated['video_url'] ?? null,
            'video_file' => $filePath,
            'thumbnail_url' => $validated['thumbnail_url'] ?? null,
            'target_body_part' => $validated['target_body_part'] ?? null,
            'difficulty_level' => $validated['difficulty_level'] ?? 'Beginner',
            'duration' => $validated['duration'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('videos.show', $video->id)
            ->with('success', 'Therapy Exercise Video added successfully! You can preview and share it now.');
    }

    /**
     * Display the specified video player and share actions.
     */
    public function show(DoctorVideo $video)
    {
        $video->load(['category', 'doctor', 'clinic']);
        $relatedVideos = DoctorVideo::where('id', '!=', $video->id)
            ->where(function($q) use ($video) {
                if ($video->category_id) {
                    $q->where('category_id', $video->category_id);
                }
            })
            ->take(4)
            ->get();

        return view('videos.show', compact('video', 'relatedVideos'));
    }

    /**
     * Show the form for editing the specified video.
     */
    public function edit(DoctorVideo $video)
    {
        $categories = TreatmentCategory::active()->orderBy('name')->get();
        $clinics = Clinic::where('is_active', true)->orderBy('name')->get();

        return view('videos.edit', compact('video', 'categories', 'clinics'));
    }

    /**
     * Update the specified video in storage.
     */
    public function update(Request $request, DoctorVideo $video)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:treatment_categories,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'video_type' => 'required|in:youtube,upload,external',
            'video_url' => 'nullable|string|max:1000',
            'video_file' => 'nullable|file|mimes:mp4,mov,webm,ogg,mkv|max:102400',
            'thumbnail_url' => 'nullable|url|max:500',
            'target_body_part' => 'nullable|string|max:100',
            'difficulty_level' => 'nullable|string|max:50',
            'duration' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:2000',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('video_file')) {
            if ($video->video_file && Storage::disk('public')->exists($video->video_file)) {
                Storage::disk('public')->delete($video->video_file);
            }
            $video->video_file = $request->file('video_file')->store('videos', 'public');
        }

        $video->update([
            'title' => $validated['title'],
            'clinic_id' => $validated['clinic_id'] ?? $video->clinic_id,
            'category_id' => $validated['category_id'] ?? null,
            'video_type' => $validated['video_type'],
            'video_url' => $validated['video_url'] ?? $video->video_url,
            'thumbnail_url' => $validated['thumbnail_url'] ?? $video->thumbnail_url,
            'target_body_part' => $validated['target_body_part'] ?? null,
            'difficulty_level' => $validated['difficulty_level'] ?? 'Beginner',
            'duration' => $validated['duration'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('videos.show', $video->id)
            ->with('success', 'Video details updated successfully.');
    }

    /**
     * Remove the specified video from storage.
     */
    public function destroy(DoctorVideo $video)
    {
        if ($video->video_file && Storage::disk('public')->exists($video->video_file)) {
            Storage::disk('public')->delete($video->video_file);
        }

        $video->delete();

        return redirect()->route('videos.index')
            ->with('success', 'Video removed successfully from library.');
    }

    /**
     * Public video watch page accessible by patients without login.
     */
    public function publicShare(string $token)
    {
        $video = DoctorVideo::where('share_token', $token)->firstOrFail();
        
        // Increment view count
        $video->increment('views_count');
        $video->load(['doctor', 'clinic', 'category']);

        return view('videos.public', compact('video'));
    }
}
