<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * GET /dashboard
     */
    public function show(Request $request)
    {
        $user = $request->user();
        // compute coverUrl safely
        $coverUrl = null;
        try {
            $flat = collect($user->cover_photos ?? [])->flatten()->filter(function($v){ return is_string($v) && trim($v) !== ""; })->values();
            $firstPath = $flat->first();
            if ($firstPath) {
                $coverUrl = \Illuminate\Support\Facades\Storage::disk("public")->url($firstPath);
            }
        } catch (\Throwable $e) {
            $coverUrl = null;
        }

        $listings = $user->listings()->with('product')->latest()->paginate(10);
        $activeListings  = $user->listings()->where('status', 'active')->count();
        $pendingListings = $user->listings()->where('status', 'pending')->count();
        $profileCompletion = $this->calculateProfileCompletion($user);

        return view('dashboard_new', compact('user','listings','activeListings','pendingListings','profileCompletion','coverUrl'));
    }
    public function viewPublicProfile(\App\Models\User $user)
    {
        // compute coverUrl safely
        $coverUrl = null;
        try {
            $flat = collect($user->cover_photos ?? [])->flatten()->filter(function($v){ return is_string($v) && trim($v) !== ""; })->values();
            $first = $flat->first();
            if ($first) {
                $coverUrl = \Illuminate\Support\Facades\Storage::disk("public")->url($first);
            }
        } catch (\Throwable $e) {
            $coverUrl = null;
        }
        // Normalize media paths and filter missing files for public display
        $normalizePath = function ($path) {
            if (!$path) return null;
            if (str_starts_with($path, 'http')) return $path;
            $cleaned = ltrim(preg_replace('/^storage\//', '', $path), '/');
            return \Illuminate\Support\Facades\Storage::disk('public')->url($cleaned);
        };
        $existsOnDisk = function ($path) {
            if (!$path || str_starts_with($path, 'http')) return true;
            $cleaned = ltrim(preg_replace('/^storage\//', '', $path), '/');
            return \Illuminate\Support\Facades\Storage::disk('public')->exists($cleaned);
        };

        $rawCover = collect($user->cover_photos ?? []);
        $coverPhotos = $rawCover->map(function ($p) use ($normalizePath, $existsOnDisk) {
            $candidate = is_array($p) ? ($p['path'] ?? $p['url'] ?? ($p[0] ?? null)) : $p;
            if (!$existsOnDisk($candidate)) return null;
            return $normalizePath($candidate);
        })->filter()->values();

        $profilePhotoUrl = null;
        if ($user->profile_picture) {
            if (!$existsOnDisk($user->profile_picture)) {
                // Check raw relative path as stored (fallback)
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)) {
                    $profilePhotoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_picture);
                }
            } else {
                $profilePhotoUrl = $normalizePath($user->profile_picture);
            }
        }

        // Addresses and role-specific info
        $addresses = $user->addresses()->get();
        $roleInfo = [];
        if ($user->role === 'farmer') {
            $roleInfo = [
                'olive_type' => $user->olive_type,
                'farm_location' => $user->farm_location,
                'tree_number' => $user->tree_number,
            ];
        } elseif ($user->role === 'carrier') {
            $roleInfo = [
                'camion_capacity' => $user->camion_capacity,
            ];
        } elseif ($user->role === 'mill') {
            $roleInfo = [
                'mill_name' => $user->mill_name,
            ];
        }
        // Paginate active listings for public view to support links()
        $listings = $user->listings()->with('product')->where('status','active')->latest()->paginate(10);
        $totalListings   = $user->listings()->count();
        $activeListings  = $user->listings()->where('status', 'active')->count();
        $pendingListings = $user->listings()->where('status', 'pending')->count();
        $profileCompletion = $this->calculateProfileCompletion($user);

        return view('profile.public', compact('user','coverUrl','coverPhotos','profilePhotoUrl','addresses','roleInfo','listings','totalListings','activeListings','pendingListings','profileCompletion'));
    }
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * PATCH /profile
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * DELETE /profile
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * نسبة اكتمال البروفايل
     */
    private function calculateProfileCompletion(User $user): int
    {
        $baseFields = ['name', 'email', 'phone', 'profile_picture'];
        $have = 0;
        $total = count($baseFields);

        foreach ($baseFields as $f) {
            $v = data_get($user, $f);
            if (is_string($v)) $v = trim($v);
            if (!empty($v)) $have++;
        }

        return (int) floor(($have / max(1, $total)) * 100);
    }
}
