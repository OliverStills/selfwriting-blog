<?php

/**
 * Test if this is a Google AI Studio API key
 * Try different Google AI services
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      TESTING GOOGLE AI SERVICES                     ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$apiKey = NANO_BANANA_API_KEY;
echo "API Key: " . substr($apiKey, 0, 20) . "...\n";
echo "Key Format: AIzaSy... (Google Cloud/AI Studio format)\n\n";

$httpClient = new Client([
    'timeout' => 30,
    'http_errors' => false
]);

// Test 1: Check if key works with Gemini text generation (to verify key is valid)
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 1: Verify API Key with Gemini Text\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $response = $httpClient->post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent',
        [
            'query' => ['key' => $apiKey],
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => 'Say hello']
                        ]
                    ]
                ]
            ]
        ]
    );
    
    $statusCode = $response->getStatusCode();
    echo "Status: {$statusCode}\n";
    
    if ($statusCode === 200) {
        echo "✅ API key is VALID and works with Gemini!\n";
        $body = json_decode($response->getBody()->getContents(), true);
        if (isset($body['candidates'][0]['content']['parts'][0]['text'])) {
            echo "Response: " . $body['candidates'][0]['content']['parts'][0]['text'] . "\n";
        }
    } elseif ($statusCode === 403 || $statusCode === 401) {
        echo "❌ API key is INVALID or doesn't have permission\n";
        echo "Body: " . $response->getBody()->getContents() . "\n";
    } else {
        echo "⚠️  Unexpected status: {$statusCode}\n";
        echo "Body: " . substr($response->getBody()->getContents(), 0, 300) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: List available models
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST 2: List Available Models\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

try {
    $response = $httpClient->get(
        'https://generativelanguage.googleapis.com/v1beta/models',
        [
            'query' => ['key' => $apiKey]
        ]
    );
    
    $statusCode = $response->getStatusCode();
    echo "Status: {$statusCode}\n";
    
    if ($statusCode === 200) {
        $body = json_decode($response->getBody()->getContents(), true);
        if (isset($body['models'])) {
            echo "✅ Found " . count($body['models']) . " models:\n\n";
            
            echo "IMAGE GENERATION MODELS:\n";
            $imageModels = [];
            foreach ($body['models'] as $model) {
                if (stripos($model['name'], 'imagen') !== false || 
                    stripos($model['displayName'] ?? '', 'image') !== false) {
                    $imageModels[] = $model;
                    echo "  • " . $model['name'] . "\n";
                    echo "    Display: " . ($model['displayName'] ?? 'N/A') . "\n";
                    echo "    Description: " . substr($model['description'] ?? 'N/A', 0, 80) . "\n\n";
                }
            }
            
            if (empty($imageModels)) {
                echo "  ❌ No image generation models found\n";
                echo "\n  This API key may not have access to Imagen/image generation.\n";
                echo "  Available model types:\n";
                foreach (array_slice($body['models'], 0, 5) as $model) {
                    echo "    • " . $model['name'] . "\n";
                }
                echo "    ... and " . (count($body['models']) - 5) . " more\n";
            }
        }
    } else {
        echo "❌ Failed to list models\n";
        echo "Body: " . substr($response->getBody()->getContents(), 0, 300) . "\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "CONCLUSION:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "Based on the tests above:\n";
echo "  1. If the key works with Gemini - the key is valid\n";
echo "  2. If no image models found - key doesn't have Imagen access\n";
echo "  3. If image models found - we'll use the correct endpoint!\n\n";

