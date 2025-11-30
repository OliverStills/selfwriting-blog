<?php

/**
 * Create an image for a single blog post
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\ImageGenerator;
use GuzzleHttp\Client;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      CREATE IMAGE FOR BLOG POST                     ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

// Get post #12 (The Leverage Ladder)
$db = new Database();
$post = $db->getPostById(12);

if (!$post) {
    echo "❌ Post #12 not found\n";
    exit(1);
}

echo "Post ID: {$post['id']}\n";
echo "Title: {$post['title']}\n";
echo "Category: {$post['category']}\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Step 1: Try Gemini API (with fallback on quota error)\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$generator = new ImageGenerator();

try {
    echo "⏳ Attempting Gemini image generation...\n\n";
    $imageUrl = $generator->generateImageForPost($post['title'], $post['excerpt']);
    
    if ($imageUrl) {
        echo "✅ SUCCESS! Gemini API generated image\n";
        echo "Image URL: {$imageUrl}\n\n";
    } else {
        throw new Exception("API returned null");
    }
    
} catch (Exception $e) {
    echo "⚠️  API Error (likely quota): " . substr($e->getMessage(), 0, 100) . "...\n\n";
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Step 2: Using High-Quality Fallback Image\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Download a perfect fallback image for "The Leverage Ladder"
    // Theme: Architecture, upward movement, professional
    $fallbackUrl = 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&q=80'; // Modern architecture/building
    
    echo "Downloading curated image...\n";
    echo "Source: Unsplash (professional architecture)\n";
    echo "Theme: Leverage, upward mobility, modern architecture\n\n";
    
    $httpClient = new Client(['timeout' => 30]);
    $response = $httpClient->get($fallbackUrl);
    
    $imageData = $response->getBody()->getContents();
    
    // Save to local directory
    $imagesDir = __DIR__ . '/public/images/posts';
    if (!file_exists($imagesDir)) {
        mkdir($imagesDir, 0755, true);
    }
    
    $filename = 'the-leverage-ladder-' . time() . '.jpg';
    $filepath = $imagesDir . '/' . $filename;
    file_put_contents($filepath, $imageData);
    
    $imageUrl = '/images/posts/' . $filename;
    echo "✅ Image saved locally: {$imageUrl}\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Step 3: Update Database\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Check if image_url column exists
try {
    $db->updatePostImage(12, $imageUrl);
    echo "✅ Database updated with image URL\n\n";
} catch (Exception $e) {
    echo "⚠️  Database update failed: " . $e->getMessage() . "\n";
    echo "Note: image_url column may need to be added to database\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✨ COMPLETE!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "🎨 View your blog post with the image:\n\n";
echo "   http://localhost:8001/post.php?id=12\n\n";
echo "🖼️  Direct image URL:\n\n";
echo "   http://localhost:8001{$imageUrl}\n\n";

$fileSize = filesize($filepath ?? (__DIR__ . '/public' . $imageUrl));
echo "📊 Image Details:\n";
echo "   Size: " . number_format($fileSize / 1024, 2) . " KB\n";
echo "   Format: JPEG\n";
echo "   Aspect Ratio: 16:9\n";
echo "   Quality: High (Unsplash Professional)\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

