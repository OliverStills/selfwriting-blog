<?php

/**
 * Test Imagen 4 image generation with correct model names
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      TESTING IMAGEN 4 IMAGE GENERATION              ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$apiKey = NANO_BANANA_API_KEY;
$httpClient = new Client([
    'timeout' => 60,
    'http_errors' => false
]);

$models = [
    'Imagen 4 Preview' => 'models/imagen-4.0-generate-preview-06-06',
    'Gemini 2.0 Flash Image' => 'models/gemini-2.0-flash-exp-image-generation',
];

$prompt = "A professional photograph of modern city architecture at sunset, dramatic lighting, dark moody aesthetic";

foreach ($models as $name => $modelId) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Testing: {$name}\n";
    echo "Model: {$modelId}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    // Try different payload formats
    $payloads = [
        'Format 1: Simple prompt' => [
            'prompt' => $prompt
        ],
        'Format 2: With parameters' => [
            'prompt' => $prompt,
            'number_of_images' => 1,
            'aspect_ratio' => '16:9'
        ],
        'Format 3: Gemini style' => [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ]
    ];
    
    foreach ($payloads as $payloadName => $payload) {
        echo "\nTrying {$payloadName}...\n";
        
        try {
            $response = $httpClient->post(
                "https://generativelanguage.googleapis.com/v1beta/{$modelId}:generate",
                [
                    'query' => ['key' => $apiKey],
                    'json' => $payload
                ]
            );
            
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            
            echo "Status: {$statusCode} ";
            
            if ($statusCode === 200) {
                echo "✅ SUCCESS!\n";
                $data = json_decode($body, true);
                echo "Response keys: " . implode(', ', array_keys($data)) . "\n";
                
                // Check for image data
                if (isset($data['images'])) {
                    echo "✅ Found 'images' array with " . count($data['images']) . " image(s)\n";
                    if (isset($data['images'][0]['image'])) {
                        echo "✅ Image data present! Length: " . strlen($data['images'][0]['image']) . " bytes\n";
                        echo "\n🎉🎉🎉 IMAGE GENERATION WORKING! 🎉🎉🎉\n";
                        echo "\nUsing:\n";
                        echo "  Model: {$modelId}\n";
                        echo "  Format: {$payloadName}\n\n";
                        break 2; // Exit both loops
                    }
                }
                
                echo "Full response:\n" . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            } elseif ($statusCode === 400) {
                $error = json_decode($body, true);
                echo "❌ Bad Request\n";
                echo "Error: " . ($error['error']['message'] ?? 'Unknown') . "\n";
            } elseif ($statusCode === 404) {
                echo "❌ Not Found\n";
            } else {
                echo "⚠️  Unexpected\n";
                echo "Body: " . substr($body, 0, 200) . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Exception: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST COMPLETE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

