<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use App\Mail\OnboardingGuide;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:email,whatsapp',
            'contact_value' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Additional validation based on type
        if ($request->type === 'email') {
            $validator = Validator::make($request->all(), [
                'contact_value' => 'email',
            ]);
        } else {
            // Very basic phone validation (can be improved)
            $validator = Validator::make($request->all(), [
                'contact_value' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:8',
            ]);
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Avoid duplicate subscriptions
        $exists = Subscriber::where('contact_value', $request->contact_value)
                            ->where('type', $request->type)
                            ->exists();

        if (!$exists) {
            $subscriber = Subscriber::create([
                'type' => $request->type,
                'contact_value' => $request->contact_value,
                'ip_address' => $request->ip(),
            ]);

            // If it's an email, send the onboarding guide
            if ($subscriber->type === 'email') {
                try {
                    Mail::to($subscriber->contact_value)->send(new OnboardingGuide());
                } catch (\Exception $e) {
                    \Log::error('Failed to send onboarding guide: ' . $e->getMessage());
                }
            }
        }

        return response()->json(['message' => 'Subscribed successfully.']);
    }
}
