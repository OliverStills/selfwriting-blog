<?php

/**
 * Generate image for STILL phase card using Nano Banana API
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

echo "Generating STILL phase image...\n\n";

$apiKey = NANO_BANANA_API_KEY;
$apiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-001:generate';

$prompt = <<<PROMPT
Create a cinematic, minimalist photograph representing mental stillness and clarity for a brand called "The Fifth State".

Visual concept: STILL - Mental Clarity & Stabilization
- A lone figure standing perfectly still on a misty mountain peak at dawn
- Deep blacks, charcoal grays, hints of deep blue
- Ethereal morning fog creating layers of depth
- Silhouette of person in meditation or contemplative stance
- Dramatic backlighting from rising sun creating rim light
- Minimalist composition with powerful negative space
- Film grain texture, high contrast
- Sense of peace, introspection, and grounding

Style: Similar to Visualize Value aesthetic meets noir cinematography
- Dark, moody, masculine energy
- Professional photography quality
- Shallow depth of field
- No text, no graphics, pure composition
- Represents: calming the noise, finding clarity, mental stillness

16:9 aspect ratio, cinematic framing
PROMPT;

$httpClient = new Client([
    'timeout' => 60,
    'headers' => [
        'Content-Type' => 'application/json',
    ]
]);

try {
    echo "Calling Nano Banana API...\n";
    
    $response = $httpClient->post($apiEndpoint, [
        'query' => [
            'key' => $apiKey
        ],
        'json' => [
            'prompt' => $prompt,
            'number_of_images' => 1,
            'aspect_ratio' => '16:9',
            'negative_prompt' => 'text, watermark, signature, low quality, blurry, amateur, bright colors, colorful',
            'person_generation' => 'allow_adult'
        ]
    ]);

    $body = json_decode($response->getBody()->getContents(), true);
    
    if (isset($body['images'][0]['image'])) {
        $imageData = $body['images'][0]['image'];
        
        // Save image
        $imageContent = base64_decode($imageData);
        $filepath = __DIR__ . '/Layrid-Clone/still-phase.jpg';
        file_put_contents($filepath, $imageContent);
        
        echo "✓ Image generated successfully!\n";
        echo "✓ Saved to: Layrid-Clone/still-phase.jpg\n";
        echo "✓ Ready to use in your site\n";
        
    } else {
        echo "Error: Unexpected API response\n";
        print_r($body);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}



