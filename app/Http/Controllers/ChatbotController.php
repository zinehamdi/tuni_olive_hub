<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Bot\EzzitouniBrainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    protected EzzitouniBrainService $brainService;

    public function __construct(EzzitouniBrainService $brainService)
    {
        $this->brainService = $brainService;
    }

    /**
     * Handle incoming chat messages for Ezzitouni AI using multi-turn reasoning and smart matchmaking.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'history' => 'nullable|array',
            'locale' => 'nullable|string|in:ar,en,fr',
        ]);

        $locale = $request->input('locale', app()->getLocale());
        if (!in_array($locale, ['ar', 'en', 'fr'])) {
            $locale = 'ar';
        }
        app()->setLocale($locale);

        $message = trim((string) $request->input('message'));
        $history = (array) $request->input('history', []);
        $authUser = auth()->user();

        try {
            $result = $this->brainService->ask($message, $history, $authUser, $locale);

            return response()->json([
                'status' => 'success',
                'reply' => $result['reply'],
                'buttons' => $result['buttons'],
                'intent' => $result['intent'],
                'user' => $authUser ? [
                    'id' => $authUser->id,
                    'name' => $authUser->name,
                    'role' => $authUser->role,
                ] : null,
            ]);
        } catch (\Throwable $e) {
            Log::error('ChatbotController Error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'reply' => $locale === 'en'
                    ? 'Sorry, a temporary error occurred. Please try again.'
                    : ($locale === 'fr' ? 'Désolé, une erreur temporaire est survenue. Veuillez réessayer.' : 'عذراً، حدث خطأ مؤقت أثناء معالجة الطلب. يرجى إعادة المحاولة.'),
                'buttons' => [
                    ['label' => '🛢️ ' . ($locale === 'en' ? 'Browse Offers' : ($locale === 'fr' ? 'Voir les offres' : 'تصفح عروض السوق')), 'url' => "/{$locale}/#products", 'type' => 'primary'],
                    ['label' => '📈 ' . ($locale === 'en' ? 'Prices' : ($locale === 'fr' ? 'Prix du marché' : 'جدول الأسعار')), 'url' => "/{$locale}/prices", 'type' => 'secondary'],
                ],
                'intent' => 'fallback',
            ]);
        }
    }
}
