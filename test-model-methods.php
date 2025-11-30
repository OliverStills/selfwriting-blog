<?php

/**
 * Check supported methods for each model
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      MODEL CAPABILITIES CHECK                       ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$apiKey = NANO_BANANA_API_KEY;
$httpClient = new Client([
    'timeout' => 30,
    'http_errors' => false
]);

// Get full model details
$response = $httpClient->get(
    'https://generativelanguage.googleapis.com/v1beta/models',
    ['query' => ['key' => $apiKey]]
);

if ($response->getStatusCode() === 200) {
    $data = json_decode($response->getBody()->getContents(), true);
    
    foreach ($data['models'] as $model) {
        $name = $model['name'] ?? 'Unknown';
        
        // Only show image-related models
        if (stripos($name, 'imagen') !== false || stripos($name, 'image-generation') !== false) {
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "Model: {$name}\n";
            echo "Display Name: " . ($model['displayName'] ?? 'N/A') . "\n";
            echo "Description: " . ($model['description'] ?? 'N/A') . "\n";
            echo "\nSupported Methods:\n";
            
            if (isset($model['supportedGenerationMethods'])) {
                foreach ($model['supportedGenerationMethods'] as $method) {
                    echo "  ✓ {$method}\n";
                }
            } else {
                echo "  ❌ No supported methods listed\n";
            }
            
            if (isset($model['inputTokenLimit'])) {
                echo "\nInput Token Limit: " . $model['inputTokenLimit'] . "\n";
            }
            if (isset($model['outputTokenLimit'])) {
                echo "Output Token Limit: " . $model['outputTokenLimit'] . "\n";
            }
            
            echo "\nFull Model Info:\n";
            echo json_encode($model, JSON_PRETTY_PRINT) . "\n";
            echo "\n";
        }
    }
} else {
    echo "❌ Failed to get model list\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";


