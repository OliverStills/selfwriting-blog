<?php

/**
 * Test the new improved prompt by generating one post
 * This shows you how the new content will be different
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\PostGenerator;

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "╔═══════════════════════════════════════════════╗\n";
echo "║   TESTING NEW IMPROVED CONTENT GENERATION     ║\n";
echo "╚═══════════════════════════════════════════════╝\n\n";

echo "This will generate ONE post to show you the improved variety.\n";
echo "Notice how it uses a unique opening scenario!\n\n";

echo "⏳ Calling Claude with improved prompt...\n\n";

try {
    $generator = new PostGenerator();
    $postData = $generator->generatePost();
    
    if (!$postData['success']) {
        echo "❌ Generation failed: " . ($postData['error'] ?? 'Unknown error') . "\n";
        exit(1);
    }
    
    echo "✅ SUCCESS! Post generated with fresh content!\n\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📝 TITLE:\n";
    echo "   {$postData['title']}\n\n";
    
    echo "📖 EXCERPT:\n";
    echo "   {$postData['excerpt']}\n\n";
    
    // Show first 300 characters of content
    $preview = strip_tags($postData['content']);
    $preview = html_entity_decode($preview, ENT_QUOTES, 'UTF-8');
    $preview = substr($preview, 0, 400);
    
    echo "🔍 CONTENT PREVIEW (first 400 chars):\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo $preview . "...\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "💡 NOTICE:\n";
    echo "   → Does it start with a unique scenario?\n";
    echo "   → Is it different from the '2:47 AM' cliché?\n";
    echo "   → Does it feel fresh and specific?\n\n";
    
    echo "This post was NOT saved to the database.\n";
    echo "It's just a preview to show the improved content quality.\n\n";
    
    echo "To save posts, use:\n";
    echo "   php cron/generate-post.php\n";
    echo "   php generate-single-pillar.php STILL\n\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

