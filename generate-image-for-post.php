<?php

/**
 * Generate a specific image for The Leverage Ladder post
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use GuzzleHttp\Client;

$db = new Database();
$post = $db->getPostById(12);

if (!$post) {
    die("Post not found\n");
}

echo "\n╔═══════════════════════════════════════════════════╗\n";
echo "║  GENERATING IMAGE FOR POST #12                  ║\n";
echo "╚═══════════════════════════════════════════════════╝\n\n";

echo "Post Title: {$post['title']}\n";
echo "Post Excerpt: " . substr($post['excerpt'], 0, 100) . "...\n\n";

// Create prompt based on content
$prompt = <<<PROMPT
Create a cinematic, professional photograph representing career leverage and strategic growth for men in their 30s.

Visual concept: "The Leverage Ladder - Trading Time for Impact"
- A dramatic upward angle shot of modern architecture or stairs
- Strong diagonal lines suggesting upward progression
- Deep blacks, dark blues, and hints of gold/orange
- Professional business aesthetic meets modern minimalism
- Sense of ambition, growth, and strategic thinking
- Film photography quality with grain texture
- High contrast, moody lighting
- Architectural precision, clean lines

Style: 
- Dark, masculine, aspirational
- Corporate meets entrepreneurial
- Premium business photography
- No people, focus on environment and lines
- Represents: scaling impact, strategic positioning, leverage

Mood: Ambitious, focused, professional, ascending
16:9 aspect ratio for blog header
PROMPT;

echo "→ Calling Nano Banana API...\n";

$apiKey = NANO_BANANA_API_KEY;
$apiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-001:generate';

$httpClient = new Client([
    'timeout' => 60,
    'headers' => ['Content-Type' => 'application/json']
]);

try {
    $response = $httpClient->post($apiEndpoint, [
        'query' => ['key' => $apiKey],
        'json' => [
            'prompt' => $prompt,
            'number_of_images' => 1,
            'aspect_ratio' => '16:9',
            'negative_prompt' => 'text, watermark, signature, people, faces, low quality, blurry, bright colors',
            'person_generation' => 'dont_allow'
        ]
    ]);

    $body = json_decode($response->getBody()->getContents(), true);
    
    if (isset($body['images'][0]['image'])) {
        $imageData = $body['images'][0]['image'];
        $imageContent = base64_decode($imageData);
        
        // Save to public images folder
        $filename = 'leverage-ladder-banner.jpg';
        $filepath = __DIR__ . '/public/images/posts/' . $filename;
        
        if (!is_dir(__DIR__ . '/public/images/posts')) {
            mkdir(__DIR__ . '/public/images/posts', 0777, true);
        }
        
        file_put_contents($filepath, $imageContent);
        
        echo "\n✅ Image generated successfully!\n";
        echo "✓ Saved to: public/images/posts/{$filename}\n";
        echo "✓ URL: /images/posts/{$filename}\n\n";
        
        echo "Image will be displayed as hero banner on post page.\n\n";
        
    } else {
        echo "\n❌ Error: Unexpected API response\n";
        print_r($body);
    }
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n\n";
    
    // Use fallback image
    echo "Using fallback approach: High-quality Unsplash image\n";
    $fallbackUrl = "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&q=80&fit=crop";
    echo "Suggested fallback: {$fallbackUrl}\n\n";
}

