<?php

require_once __DIR__ . '/src/config.php';
use App\Database;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      IMAGE PATH VERIFICATION                        ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$db = new Database();
$posts = $db->getAllPosts(5, false);

echo "Checking first 5 posts:\n\n";

foreach ($posts as $post) {
    echo "Post #{$post['id']}: {$post['title']}\n";
    echo "  DB Path: " . ($post['image_url'] ?? 'NULL') . "\n";
    
    if (!empty($post['image_url'])) {
        // Check if file exists in Slides-Hybrid
        $slidesPath = __DIR__ . '/Slides-Hybrid' . $post['image_url'];
        $publicPath = __DIR__ . '/public' . $post['image_url'];
        
        if (file_exists($slidesPath)) {
            $size = filesize($slidesPath);
            echo "  ✓ Found in Slides-Hybrid (" . number_format($size / 1024, 0) . " KB)\n";
        } else {
            echo "  ✗ NOT found in Slides-Hybrid: {$slidesPath}\n";
        }
        
        if (file_exists($publicPath)) {
            echo "  ✓ Found in public/\n";
        }
    } else {
        echo "  ⚠ No image URL in database\n";
    }
    echo "\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Images should now be visible at:\n";
echo "   http://localhost:8001/\n\n";
echo "Try viewing a specific post:\n";
echo "   http://localhost:8001/post.php?id=12\n\n";


