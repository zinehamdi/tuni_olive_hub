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
            
            if ($index === 0 && $role === 'model') {
                continue;
            }

            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $msg['content']]]
            ];
        }

        // Append current message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $request->input('message')]]
        ];

        try {
            // Google Gemini API Request - using universally supported gemini-pro
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
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
            $geminiError = $response->json('error.message') ?? 'Unknown Error';
            
            return response()->json([
                'reply' => "عذراً، الخادم رفض الطلب. (تفاصيل الخطأ: {$geminiError})"
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'حدث خطأ غير متوقع. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }

}
