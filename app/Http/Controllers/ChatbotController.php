<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chat messages for Ezzitouni AI using Google Gemini API.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'array', // previous chat messages
        ]);

        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            return response()->json([
                'reply' => 'أهلاً بك! أنا "الزيتوني"، مساعدك الذكي في منصة ZinToop. الميزة حالياً قيد التفعيل (نحتاج إلى مفتاح API). يرجى المحاولة لاحقاً!'
            ]);
        }

        // Get the system instructions
        $systemInstruction = config('ezzitouni.system_prompt', 'أنت مساعد ذكي لمنصة ZinToop.');

        // Prepare conversation history
        $history = $request->input('history', []);
        
        // Prepend System Instructions as a conversation setup for universal model compatibility
        $contents = [];
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "[System Instructions]:\n" . $systemInstruction . "\n\nEnd of instructions. Acknowledge and wait for user."]]
        ];
        $contents[] = [
            'role' => 'model',
            'parts' => [['text' => "Understood. I am Ezzitouni, ready to assist."]]
        ];

        foreach ($history as $index => $msg) {
            $role = $msg['role'] === 'user' ? 'user' : 'model';
            $text = $msg['content'] ?? '';
            
            // Skip empty messages because Gemini API crashes if text is empty string
            if (empty(trim($text))) {
                continue;
            }
            
            if ($index === 0 && $role === 'model') {
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $text]]
            ];
        }

        // Append current message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $request->input('message')]]
        ];

        try {
            // Google Gemini API Request - using universally supported gemini-flash-latest (2026+ keys)
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'عذراً، لم أتمكن من فهم ذلك.';
                
                return response()->json([
                    'reply' => $reply
                ]);
            }

            $errorBody = $response->body();
            Log::error('Gemini API Error: ' . $errorBody);
            
            // For debugging: extract exactly what Gemini complained about
            $geminiError = $response->json('error.message') ?? substr(strip_tags($errorBody), 0, 150);
            
            // Try to fetch available models to help the user debug
            $modelsList = '';
            try {
                $modelsResponse = Http::get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
                if ($modelsResponse->successful()) {
                    $models = collect($modelsResponse->json('models', []))
                                ->filter(fn($m) => str_contains($m['supportedGenerationMethods'][0] ?? '', 'generateContent'))
                                ->pluck('name')
                                ->join(', ');
                    $modelsList = " | الموديلات المتاحة لمفتاحك: " . ($models ?: 'لا يوجد موديلات مدعومة');
                }
            } catch (\Exception $e) {}
            
            return response()->json([
                'reply' => "عذراً، الخادم رفض الطلب. (الخطأ: {$geminiError}) {$modelsList}"
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'حدث خطأ غير متوقع. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }

}
