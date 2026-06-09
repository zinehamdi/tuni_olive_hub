<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiYieldController extends Controller
{
    private function callGeminiVision(Request $request, string $prompt)
    {
        $apiKey = config('services.gemini.key');
        // Always try to use the model configured in .env, defaulting to 2.5 flash as we know it's there
        $model = config('services.gemini.model', 'gemini-2.5-flash');
        
        $image = $request->file('image');
        $mimeType = $image->getMimeType();
        $base64 = base64_encode(file_get_contents($image->getRealPath()));

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $base64
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1, // Low temperature for more deterministic facts
                'maxOutputTokens' => 100,
            ]
        ];

        $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

        if ($response->successful()) {
            $data = $response->json();
            return data_get($data, 'candidates.0.content.parts.0.text');
        }

        Log::error('Gemini Vision API Error: ' . $response->body());
        return null;
    }

    public function estimate(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
        ]);

        try {
            $prompt = "You are an expert agronomist. Look at this picture of crushed or whole olives. Estimate the oil yield percentage realistically based on visual ripeness and state. Reply ONLY with the decimal number (e.g. 18.5 or 22.0). Do not include the % sign or any other text.";
            
            $result = $this->callGeminiVision($request, $prompt);

            if ($result) {
                // Extract just the number in case the AI added extra text
                preg_match('/(\d+(\.\d+)?)/', $result, $matches);
                $yield = isset($matches[1]) ? (float) $matches[1] : 18.0;

                return response()->json([
                    'success' => true,
                    'estimated_yield' => $yield,
                    'message' => 'Image analyzed successfully by Ezzitouni Bot.'
                ]);
            }

            throw new \Exception("Failed to get a valid response from Gemini");

        } catch (\Exception $e) {
            Log::error('AI Yield Estimator Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحليل. يرجى المحاولة لاحقاً.'
            ], 500);
        }
    }

    public function detectVariety(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
        ]);

        try {
            $prompt = "You are an expert Tunisian agronomist. Look at this picture of an olive leaf, branch, or fruit. Detect the olive variety. It is most likely one of the following Tunisian or foreign varieties: Chemlali, Chetoui, Oueslati, Zalmati, Zarrazi, Barouni, Meski, Arbequina, Picual, Coratina, Koroneiki, Chemchali, Gerboui. Reply ONLY with the name of the variety in English letters (e.g. 'Chetoui'). Do not add any extra text.";
            
            $result = $this->callGeminiVision($request, $prompt);

            if ($result) {
                // Clean the output
                $detectedVariety = trim($result);
                
                return response()->json([
                    'success' => true,
                    'detected_variety' => $detectedVariety,
                    'message' => 'Image analyzed successfully by Ezzitouni Bot.'
                ]);
            }

            throw new \Exception("Failed to get a valid response from Gemini");

        } catch (\Exception $e) {
            Log::error('AI Variety Estimator Exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التعرف على الصنف. يرجى المحاولة لاحقاً.'
            ], 500);
        }
    }
}
