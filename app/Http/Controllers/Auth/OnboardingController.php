<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OnboardingController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        // If user already has required fields, redirect to dashboard
        if (!empty($user->phone) && !empty($user->role) && $user->role !== 'consumer') {
            return redirect()->route('dashboard');
        }

        return view('auth.onboarding');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'role' => 'required|in:farmer,mill,carrier,packer,normal',
        ]);

        $user = Auth::user();
        $user->update([
            'phone' => $request->phone,
            'role' => $request->role,
        ]);

        return redirect()->route('dashboard')->with('status', __('Onboarding completed successfully!'));
    }
}
