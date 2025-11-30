<?php

/**
 * Detailed Nano Banana API Test
 * Shows exactly what's happening with the API request
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      NANO BANANA API - DETAILED TEST                ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

// Show configuration
echo "1. CONFIGURATION CHECK\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "API Key: " . (defined('NANO_BANANA_API_KEY') ? substr(NANO_BANANA_API_KEY, 0, 20) . '...' : 'NOT DEFINED') . "\n";
echo "API Key Length: " . (defined('NANO_BANANA_API_KEY') ? strlen(NANO_BANANA_API_KEY) : 0) . " characters\n";
echo "Full Key: " . NANO_BANANA_API_KEY . "\n\n";

// API endpoint
$apiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-001:generate';
echo "2. API ENDPOINT\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Endpoint: {$apiEndpoint}\n";
echo "Method: POST\n";
echo "Key Parameter: ?key=" . NANO_BANANA_API_KEY . "\n\n";

// Prepare request
$prompt = "A professional photograph of modern city architecture at sunset, dramatic lighting, dark moody aesthetic, 16:9 ratio";

echo "3. REQUEST PAYLOAD\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$payload = [
    'prompt' => $prompt,
    'number_of_images' => 1,
    'aspect_ratio' => '16:9',
    'negative_prompt' => 'text, watermark, low quality',
    'person_generation' => 'dont_allow'
];
echo json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";

// Make request
echo "4. MAKING API REQUEST\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Sending request...\n\n";

$httpClient = new Client([
    'timeout' => 60,
    'headers' => [
        'Content-Type' => 'application/json',
    ],
    'http_errors' => false // Don't throw exceptions, get response
]);

try {
    $fullUrl = $apiEndpoint . '?key=' . NANO_BANANA_API_KEY;
    echo "Full URL: {$fullUrl}\n\n";
    
    $response = $httpClient->post($apiEndpoint, [
        'query' => [
            'key' => NANO_BANANA_API_KEY
        ],
        'json' => $payload
    ]);

    $statusCode = $response->getStatusCode();
    $body = $response->getBody()->getContents();
    
    echo "5. API RESPONSE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Status Code: {$statusCode}\n";
    echo "Status: " . ($statusCode === 200 ? "✅ SUCCESS" : "❌ ERROR") . "\n\n";
    
    echo "Response Headers:\n";
    foreach ($response->getHeaders() as $name => $values) {
        echo "  {$name}: " . implode(', ', $values) . "\n";
    }
    echo "\n";
    
    echo "Response Body:\n";
    $bodyDecoded = json_decode($body, true);
    if ($bodyDecoded) {
        echo json_encode($bodyDecoded, JSON_PRETTY_PRINT) . "\n\n";
    } else {
        echo $body . "\n\n";
    }
    
    // Analyze the error
    echo "6. ERROR ANALYSIS\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    if ($statusCode === 404) {
        echo "❌ 404 Not Found\n\n";
        echo "Possible Reasons:\n";
        echo "  1. Wrong API endpoint URL\n";
        echo "  2. Model name incorrect (imagen-3.0-generate-001)\n";
        echo "  3. API key doesn't have access to this model\n";
        echo "  4. Nano Banana service changed their API\n\n";
    } elseif ($statusCode === 403) {
        echo "❌ 403 Forbidden\n\n";
        echo "Possible Reasons:\n";
        echo "  1. Invalid API key\n";
        echo "  2. API key doesn't have permission\n";
        echo "  3. Rate limit exceeded\n\n";
    } elseif ($statusCode === 400) {
        echo "❌ 400 Bad Request\n\n";
        echo "Possible Reasons:\n";
        echo "  1. Invalid request format\n";
        echo "  2. Missing required parameters\n";
        echo "  3. Invalid parameter values\n\n";
    } elseif ($statusCode === 200) {
        echo "✅ SUCCESS!\n\n";
        if (isset($bodyDecoded['images'][0]['image'])) {
            echo "Image data received! Length: " . strlen($bodyDecoded['images'][0]['image']) . " bytes\n";
        } else {
            echo "Response structure unexpected. Keys: " . implode(', ', array_keys($bodyDecoded)) . "\n";
        }
    }
    
    echo "\n7. RECOMMENDATIONS\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    if ($statusCode === 404) {
        echo "→ Check Nano Banana documentation for correct endpoint\n";
        echo "→ Verify the model name is correct\n";
        echo "→ Try alternative Google AI endpoints\n";
        echo "→ Consider using a different image generation service\n\n";
        
        echo "Alternative Services:\n";
        echo "  • DALL-E 3 (OpenAI)\n";
        echo "  • Stable Diffusion (Stability AI)\n";
        echo "  • Midjourney API\n";
        echo "  • Replicate (multiple models)\n\n";
    }
    
} catch (Exception $e) {
    echo "\n❌ EXCEPTION THROWN\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Type: " . get_class($e) . "\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST COMPLETE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

