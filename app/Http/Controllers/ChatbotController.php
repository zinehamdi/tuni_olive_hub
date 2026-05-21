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

        $apiKey = config('services.gemini.key');
        
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

        $rawMessages = [];
        
        foreach ($history as $index => $msg) {
            if ($index === 0 && $msg['role'] === 'model') continue;
            
            $text = trim($msg['content'] ?? '');
            if (empty($text)) continue;
            
            $rawMessages[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'text' => $text
            ];
        }

        // Append current message
        $rawMessages[] = [
            'role' => 'user',
            'text' => trim($request->input('message'))
        ];

        // Ensure alternating roles (Gemini requirement)
        foreach ($rawMessages as $msg) {
            $role = $msg['role'];
            $text = $msg['text'];
            
            $lastIndex = count($contents) - 1;
            if ($lastIndex >= 0 && $contents[$lastIndex]['role'] === $role) {
                // Merge with previous message of the same role
                $contents[$lastIndex]['parts'][0]['text'] .= "\n\n" . $text;
            } else {
                // Add new message
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $text]]
                ];
            }
        }

        try {
            $model = config('services.gemini.model', 'gemini-2.0-flash');
            
            // Request Payload
            $payload = [
                'contents' => $contents,
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ];

            // Google Gemini API Request
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

            // Fallback for 503 Service Unavailable (Model Overloaded)
            if ($response->status() === 503) {
                Log::warning("Gemini API 503 on {$model}, retrying with gemini-2.0-flash-lite");
                $fallbackModel = 'gemini-2.0-flash-lite-preview-02-05';
                $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$fallbackModel}:generateContent?key={$apiKey}", $payload);
                
                // Secondary Fallback to 1.5 flash if 2.0 lite also fails
                if ($response->status() === 503) {
                    $fallbackModel = 'gemini-1.5-flash';
                    $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$fallbackModel}:generateContent?key={$apiKey}", $payload);
                }
            }

            if ($response->successful()) {
                $data = $response->json();
                
                // Safely extract the text to prevent PHP ErrorExceptions
                $reply = data_get($data, 'candidates.0.content.parts.0.text');
                
                if (empty($reply)) {
                    // Handle safety block or finish reason
                    $blockReason = data_get($data, 'promptFeedback.blockReason');
                    $finishReason = data_get($data, 'candidates.0.finishReason');
                    
                    if ($blockReason) {
                        $reply = 'عذراً، لا يمكنني مناقشة هذا الموضوع لأسباب تتعلق بسياسات الأمان.';
                    } else if ($finishReason) {
                        $reply = 'توقف توليد النص. السبب: ' . $finishReason;
                    } else {
                        $reply = 'عذراً، تلقيت استجابة غير مفهومة من الخادم.';
                    }
                }
                
                return response()->json([
                    'reply' => $reply
                ]);
            }

            $errorBody = $response->body();
            Log::error('Gemini API Error: ' . $errorBody);
            
            $status = $response->status();
            $geminiError = $response->json('error.message') ?? '';
            
            if ($status === 429 || str_contains(strtolower($geminiError), 'quota') || str_contains(strtolower($geminiError), 'limit')) {
                $reply = 'عذراً، يبدو أن هناك ضغطاً كبيراً على خدمة الذكاء الاصطناعي حالياً (تجاوز حد الاستخدام المجاني). يرجى الانتظار بضع ثوانٍ ثم المحاولة مجدداً.';
            } elseif ($status === 401 || $status === 403 || str_contains(strtolower($geminiError), 'key') || str_contains(strtolower($geminiError), 'invalid')) {
                $reply = 'عذراً، هناك مشكلة في إعدادات الاتصال بخدمة الذكاء الاصطناعي (مفتاح الـ API غير صالح). يرجى مراجعة إدارة المنصة.';
            } else {
                $reply = 'عذراً، حدث خطأ أثناء الاتصال بالخادم. يرجى المحاولة لاحقاً.';
            }
            
            return response()->json([
                'reply' => $reply
            ], $status);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'حدث خطأ غير متوقع. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }

}
