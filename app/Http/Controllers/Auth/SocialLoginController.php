<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function callback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')
                ->fields(['name', 'email', 'picture.width(800).height(800)', 'cover'])
                ->user();
                
            \Illuminate\Support\Facades\Log::info('Facebook User Data:', $facebookUser->user);
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => __('Facebook login failed. Please try again.')]);
        }

        // Try to download profile picture and cover
        $profilePicturePath = null;
        $coverPhotosPath = null;
        
        $avatarUrl = $facebookUser->avatar_original ?? $facebookUser->avatar ?? $facebookUser->user['picture']['data']['url'] ?? null;
        if ($avatarUrl) {
            try {
                $response = \Illuminate\Support\Facades\Http::get($avatarUrl);
                if ($response->successful()) {
                    $filename = 'profile-pictures/' . Str::random(40) . '.jpg';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $response->body());
                    $profilePicturePath = $filename;
                }
            } catch (\Exception $e) {}
        }

        $coverUrl = $facebookUser->user['cover']['source'] ?? null;
        if ($coverUrl) {
            try {
                $response = \Illuminate\Support\Facades\Http::get($coverUrl);
                if ($response->successful()) {
                    $filename = 'cover-photos/' . Str::random(40) . '.jpg';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $response->body());
                    $coverPhotosPath = [$filename];
                }
            } catch (\Exception $e) {}
        }

        // Find existing user by facebook_id or email
        $user = User::where('facebook_id', $facebookUser->id)->first();

        if (!$user && $facebookUser->email) {
            $user = User::where('email', $facebookUser->email)->first();
        }

        if ($user) {
            $updates = [];
            // Update facebook_id if it was matched by email
            if (!$user->facebook_id) {
                $updates['facebook_id'] = $facebookUser->id;
                $updates['meta_data'] = array_merge($user->meta_data ?? [], ['facebook' => $facebookUser->user]);
            }
            
            // Update profile picture if missing
            if (!$user->profile_picture && $profilePicturePath) {
                $updates['profile_picture'] = $profilePicturePath;
            }
            
            // Update cover photo if missing
            if (empty($user->cover_photos) && $coverPhotosPath) {
                $updates['cover_photos'] = $coverPhotosPath;
            }
            
            if (!empty($updates)) {
                $user->update($updates);
            }
        } else {
            // Create a new user silently
            $user = User::create([
                'name' => $facebookUser->name,
                'email' => $facebookUser->email ?? $facebookUser->id . '@facebook.placeholder',
                'facebook_id' => $facebookUser->id,
                'password' => bcrypt(Str::random(24)),
                'meta_data' => ['facebook' => $facebookUser->user],
                'phone' => '', // This bypasses the SQL error and forces the user into the onboarding flow
                'profile_picture' => $profilePicturePath,
                'cover_photos' => $coverPhotosPath,
                // role is empty by default, enforced by middleware
            ]);
        }

        Auth::login($user, true);

        // Redirect handled by EnsureOnboardingIsComplete middleware automatically
        // but we can be explicit if we want:
        if (empty($user->phone) || empty($user->role) || $user->role === 'consumer') {
            return redirect()->route('onboarding.complete');
        }

        return redirect()->intended('/dashboard');
    }
}
