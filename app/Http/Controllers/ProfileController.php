<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the user's profile (dashboard)
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return view('profile.show', compact('user'));
    }
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
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
     * Onboarding dispatcher: routes to correct form or dashboard
     */
    public function onboarding(Request $request)
    {
        $user = $request->user();
        if ($user->onboarding_completed_at) {
            return redirect()->route('dashboard');
        }
        switch ($user->role) {
            case 'farmer':
                return view('onboarding.farmer');
            case 'carrier':
                return view('onboarding.carrier');
            case 'mill':
                return view('onboarding.mill');
            case 'packer':
                return view('onboarding.packer');
            case 'normal':
            default:
                return view('onboarding.normal');
        }
    }

        /**
         * Handle onboarding form submission and mark onboarding as completed
         */
        public function submitOnboarding(Request $request, $role)
        {
            $user = $request->user();
            // Save onboarding info (minimal, can be expanded per role)
            switch ($role) {
                case 'farmer':
                    $user->farm_name_ar = $request->input('farm_name_ar');
                    $user->farm_name_lat = $request->input('farm_name_lat');
                    $user->location = $request->input('location');
                    break;
                case 'carrier':
                    $user->company_name = $request->input('company_name');
                    $user->fleet_size = $request->input('fleet_size');
                    break;
                case 'mill':
                    $user->mill_name = $request->input('mill_name');
                    $user->capacity = $request->input('capacity');
                    break;
                case 'packer':
                    $user->packer_name = $request->input('packer_name');
                    $user->packaging_types = $request->input('packaging_types');
                    break;
                case 'normal':
                default:
                    $user->full_name = $request->input('full_name');
                    break;
            }
            $user->onboarding_completed_at = now();
            $user->save();
            return redirect()->route('dashboard')->with('status', 'onboarding-completed');
        }
}
