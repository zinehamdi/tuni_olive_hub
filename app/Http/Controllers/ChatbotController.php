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
        $systemInstruction = config('ezzitouni.system_prompt', $this->getDefaultPrompt());

        // Prepare conversation history
        $history = $request->input('history', []);
        $contents = [];

        foreach ($history as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['content']]]
            ];
        }

        // Append current message
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $request->input('message')]]
        ];

        try {
            // Google Gemini API Request
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ],
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

            Log::error('Gemini API Error: ' . $response->body());
            return response()->json([
                'reply' => 'عذراً، أواجه مشكلة تقنية حالياً في التواصل مع الخادم.'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Chatbot Exception: ' . $e->getMessage());
            return response()->json([
                'reply' => 'حدث خطأ غير متوقع. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }

    private function getDefaultPrompt()
    {
        return "أنت اسمك 'الزيتوني' (Ezzitouni)، وأنت المساعد الذكي والخبير التجاري والقانوني لمنصة 'ZinToop' (سوق زيت الزيتون التونسي). 
أنت خبير في:
1. أنواع الزيتون التونسي (مثل: الشملالي الذي يتميز بطعمه الخفيف والفاكهي، والشتوي الذي يتميز بطعمه القوي والمرارة المفيدة، والوسلاتي، والزلماطي).
2. قوانين التجارة الدولية وتصدير زيت الزيتون من تونس إلى العالم (الديوانة، التراخيص، شروط التصدير).
3. منصة ZinToop: هي منصة مجانية 100% تربط بين الفلاح، صاحب المعصرة، التاجر، والمصدر بدون أي عمولات خفية.
هدف المنصة: رقمنة قطاع الزيتون في تونس وتسهيل المعاملات.
دورك:
- أجب عن أسئلة المستخدمين بلهجة تونسية محترمة أو باللغة العربية الفصحى حسب لغة المستخدم.
- قدّم نصائح تقنية حول جودة زيت الزيتون (الحموضة، العصر على البارد، البيروكسيد).
- إذا أبدى المستخدم اهتماماً بشراء أو تصدير كميات كبيرة، شجعه على ملء نموذج التواصل أو حجز موعد (Appointment) عبر المنصة. 
- كن موجزاً، ذكياً، ودقيقاً. لا تقدم معلومات كاذبة.";
    }
}
