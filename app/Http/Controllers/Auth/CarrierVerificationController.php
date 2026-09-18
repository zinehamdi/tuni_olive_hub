<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Bot\WhatsAppCloudApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class CarrierVerificationController extends Controller
{
    /**
     * Display the WhatsApp Verification Waiting Screen for Carriers
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'carrier') {
            return redirect()->route('dashboard');
        }

        // If already verified, go straight to dashboard
        if (!empty($user->meta_data['whatsapp_verified'])) {
            return redirect()->route('dashboard')->with('success', __('تم تفعيل حسابك واستقبال الكورسات بنجاح!'));
        }

        return view('auth.verify_whatsapp_carrier', [
            'user' => $user,
        ]);
    }

    /**
     * Check if the carrier has verified their WhatsApp
     */
    public function checkStatus(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['verified' => false], 401);
        }

        $user->refresh();
        $isVerified = !empty($user->meta_data['whatsapp_verified']);

        return response()->json([
            'verified' => $isVerified,
            'redirect' => route('dashboard'),
        ]);
    }

    /**
     * Resend WhatsApp activation message to carrier
     */
    public function resend(WhatsAppCloudApiService $waService): JsonResponse
    {
        $user = Auth::user();
        if (!$user || empty($user->phone)) {
            return response()->json(['success' => false, 'message' => __('رقم الهاتف غير متوفر.')], 422);
        }

        try {
            $waService->sendCarrierWelcomeHandshake($user->phone, $user);
            return response()->json([
                'success' => true,
                'message' => __('تمت إعادة إرسال رسالة التفعيل إلى WhatsApp بنجاح.'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('تعذر إرسال الرسالة، يرجى المحاولة بعد قليل.'),
            ], 500);
        }
    }
}
