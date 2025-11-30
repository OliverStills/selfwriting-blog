<?php

/**
 * Test multiple possible Imagen API endpoints
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      TESTING MULTIPLE IMAGEN ENDPOINTS              ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$apiKey = NANO_BANANA_API_KEY;
$prompt = "A professional photograph of modern city architecture";

// Try different endpoint variations
$endpoints = [
    '1. imagen-3.0-generate-001' => 'https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-001:generate',
    '2. imagen-3.0-generate' => 'https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate:generate',
    '3. imagegeneration' => 'https://generativelanguage.googleapis.com/v1beta/models/imagegeneration:generate',
    '4. v1 version' => 'https://generativelanguage.googleapis.com/v1/models/imagen-3.0-generate-001:generate',
    '5. gemini vision' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent',
    '6. generate-images' => 'https://generativelanguage.googleapis.com/v1beta/images:generate',
];

$httpClient = new Client([
    'timeout' => 10,
    'headers' => ['Content-Type' => 'application/json'],
    'http_errors' => false
]);

foreach ($endpoints as $name => $endpoint) {
    echo "Testing: {$name}\n";
    echo "URL: {$endpoint}\n";
    
    try {
        $response = $httpClient->post($endpoint, [
            'query' => ['key' => $apiKey],
            'json' => [
                'prompt' => $prompt,
                'number_of_images' => 1
            ]
        ]);
        
        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        
        if ($statusCode === 200) {
            echo "✅ SUCCESS! Status: {$statusCode}\n";
            echo "Response: " . substr($body, 0, 200) . "...\n";
            echo "\n🎉 FOUND WORKING ENDPOINT: {$endpoint}\n\n";
            break;
        } elseif ($statusCode === 404) {
            echo "❌ 404 Not Found\n";
        } elseif ($statusCode === 400) {
            echo "⚠️  400 Bad Request (endpoint exists but wrong params)\n";
            echo "Body: " . substr($body, 0, 200) . "\n";
        } elseif ($statusCode === 403) {
            echo "⚠️  403 Forbidden (endpoint exists but no permission)\n";
        } else {
            echo "⚠️  Status: {$statusCode}\n";
            echo "Body: " . substr($body, 0, 200) . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Exception: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "CONCLUSION:\n";
echo "The 'Nano Banana' service you mentioned may be using a different\n";
echo "API endpoint than standard Google Imagen APIs.\n\n";
echo "Please check:\n";
echo "  1. Nano Banana's actual API documentation\n";
echo "  2. The service URL they provide\n";
echo "  3. Any setup instructions they gave with the API key\n\n";


