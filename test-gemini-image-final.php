<?php

/**
 * Test Gemini Image Generation with Official API Format
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\ImageGenerator;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      GEMINI IMAGE GENERATION - FINAL TEST           ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

echo "Testing with official Gemini API format...\n";
echo "Model: gemini-2.5-flash-image (Nano Banana)\n";
echo "Endpoint: generateContent\n\n";

$generator = new ImageGenerator();

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Generating image for blog post...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$title = "The Leverage Ladder";
$excerpt = "Stop trading time for money. Learn how to build systems that scale your impact without scaling your hours.";

echo "Title: {$title}\n";
echo "Excerpt: " . substr($excerpt, 0, 60) . "...\n\n";

try {
    echo "⏳ Generating image (this may take 30-60 seconds)...\n\n";
    
    $imageUrl = $generator->generateImageForPost($title, $excerpt);
    
    if ($imageUrl) {
        echo "✅ SUCCESS!\n\n";
        echo "Image saved to: {$imageUrl}\n";
        echo "Full path: " . __DIR__ . "/public{$imageUrl}\n\n";
        
        if (file_exists(__DIR__ . "/public{$imageUrl}")) {
            $fileSize = filesize(__DIR__ . "/public{$imageUrl}");
            echo "File size: " . number_format($fileSize / 1024, 2) . " KB\n";
            echo "Format: PNG with SynthID watermark\n\n";
            
            echo "🎉🎉🎉 IMAGE GENERATION WORKING! 🎉🎉🎉\n\n";
            echo "You can now use this image in your blog posts!\n";
            echo "View it in browser: http://localhost:8001{$imageUrl}\n\n";
        }
    } else {
        echo "❌ Image generation returned null\n";
        echo "Check error_log for details\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ FAILED\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "This could mean:\n";
    echo "  1. API quota exceeded\n";
    echo "  2. Invalid API key\n";
    echo "  3. Network connectivity issue\n";
    echo "  4. API format mismatch\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "TEST COMPLETE\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";


