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
            $facebookUser = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => __('Facebook login failed. Please try again.')]);
        }

        // Find existing user by facebook_id or email
        $user = User::where('facebook_id', $facebookUser->id)->first();

        if (!$user && $facebookUser->email) {
            $user = User::where('email', $facebookUser->email)->first();
        }

        if ($user) {
            // Update facebook_id if it was matched by email
            if (!$user->facebook_id) {
                $user->update([
                    'facebook_id' => $facebookUser->id,
                    'meta_data' => array_merge($user->meta_data ?? [], ['facebook' => $facebookUser->user]),
                ]);
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
