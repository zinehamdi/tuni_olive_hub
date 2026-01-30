<?php

namespace App\Http\Controllers;

use App\Models\Story;
use App\Models\User;
use App\Services\ImageOptimizationService;
use App\Services\VideoOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StoryController extends Controller
{
    public function __construct(
        private ImageOptimizationService $imageService,
        private VideoOptimizationService $videoService
    ) {
    }

    /**
     * List active stories for a user (public JSON)
     */
    public function index(User $user): JsonResponse
    {
        $stories = Story::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->get()
            ->map(function (Story $story) {
                return [
                    'id' => $story->id,
                    'media_type' => $story->media_type,
                    'caption' => $story->caption,
                    'expires_at' => $story->expires_at,
                    'url' => Storage::url($story->media_path),
                    'created_at' => $story->created_at,
                ];
            });

        return response()->json($stories);
    }

    /**
     * Store a new story (auth required)
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'media' => 'required|file|mimes:jpeg,jpg,png,webp,mp4,mov,quicktime|max:30000',
            'caption' => 'nullable|string|max:200',
        ]);

        $user = $request->user();
        $file = $request->file('media');
        $mime = $file->getMimeType();

        // Optional: cap active stories (drop oldest)
        $maxActive = 10;
        $activeCount = Story::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->count();
        if ($activeCount >= $maxActive) {
            $oldest = Story::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->oldest()
                ->first();
            if ($oldest) {
                $oldest->update(['status' => 'expired']);
            }
        }

        if (str_starts_with($mime, 'image/')) {
            $mediaPath = $this->imageService->optimizeCoverPhoto($file);
            $mediaType = 'image';
        } else {
            $mediaPath = $this->videoService->optimizeStoryVideo($file);
            $mediaType = 'video';
        }

        $story = Story::create([
            'user_id' => $user->id,
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'caption' => $request->input('caption'),
            'expires_at' => now()->addHours(48),
            'status' => 'active',
        ]);

        $payload = [
            'id' => $story->id,
            'media_type' => $story->media_type,
            'caption' => $story->caption,
            'expires_at' => $story->expires_at,
            'url' => Storage::url($story->media_path),
            'created_at' => $story->created_at,
        ];

        if ($request->wantsJson()) {
            return response()->json($payload, 201);
        }

        return redirect()->back()->with('status', __('Story posted'));
    }
}
