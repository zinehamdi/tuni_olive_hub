<?php
$apiKey = "AIzaSyAzMRQvsS1aeJj7wCFOEPynRl7me1J79TU";
$ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-lite:generateContent?key=' . $apiKey);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);

$base64Image = base64_encode(file_get_contents('test.gif'));
$data = json_encode(['contents' => [['parts' => [['text' => 'test'], ['inlineData' => ['mimeType' => 'image/gif', 'data' => $base64Image]]]]]]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);
echo "LITE RESPONSE: " . $response . "\n";

$ch2 = curl_init('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_POSTFIELDS, $data);
$response2 = curl_exec($ch2);
echo "2.0-FLASH RESPONSE: " . $response2 . "\n";
