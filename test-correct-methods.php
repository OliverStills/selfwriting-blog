<?php

/**
 * Test with correct methods
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      TESTING WITH CORRECT METHODS                   ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$apiKey = NANO_BANANA_API_KEY;
$httpClient = new Client([
    'timeout' => 60,
    'http_errors' => false
]);

$prompt = "A professional photograph of modern city architecture at sunset, dramatic lighting, dark moody aesthetic";

// Test 1: Gemini 2.0 Flash with generateContent
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Gemini 2.0 Flash (Image Generation)\n";
echo "Method: generateContent\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

try {
    $response = $httpClient->post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp-image-generation:generateContent',
        [
            'query' => ['key' => $apiKey],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Generate an image: ' . $prompt]
                        ]
                    ]
                ]
            ]
        ]
    );
    
    $statusCode = $response->getStatusCode();
    $body = $response->getBody()->getContents();
    
    echo "Status: {$statusCode}\n";
    
    if ($statusCode === 200) {
        echo "✅ SUCCESS!\n\n";
        $data = json_decode($body, true);
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
        
        // Check for image in response
        if (isset($data['candidates'][0]['content']['parts'])) {
            foreach ($data['candidates'][0]['content']['parts'] as $part) {
                if (isset($part['inlineData'])) {
                    echo "🎉 IMAGE FOUND!\n";
                    echo "MIME Type: " . ($part['inlineData']['mimeType'] ?? 'unknown') . "\n";
                    echo "Data Length: " . strlen($part['inlineData']['data'] ?? '') . " bytes\n\n";
                }
            }
        }
    } else {
        echo "❌ Error\n";
        echo substr($body, 0, 500) . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

// Test 2: Imagen 4 with predict
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: Imagen 4 (Preview)\n";
echo "Method: predict\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

try {
    $response = $httpClient->post(
        'https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-preview-06-06:predict',
        [
            'query' => ['key' => $apiKey],
            'json' => [
                'instances' => [
                    ['prompt' => $prompt]
                ],
                'parameters' => [
                    'sampleCount' => 1,
                    'aspectRatio' => '16:9'
                ]
            ]
        ]
    );
    
    $statusCode = $response->getStatusCode();
    $body = $response->getBody()->getContents();
    
    echo "Status: {$statusCode}\n";
    
    if ($statusCode === 200) {
        echo "✅ SUCCESS!\n\n";
        $data = json_decode($body, true);
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
    } else {
        echo "❌ Error\n";
        echo substr($body, 0, 500) . "\n\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";


