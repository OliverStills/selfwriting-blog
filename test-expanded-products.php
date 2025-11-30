<?php

/**
 * Test expanded product recommendations for one post
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\ProductRecommender;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      TEST EXPANDED PRODUCT RECOMMENDATIONS          ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$db = new Database();
$recommender = new ProductRecommender();

// Get post #12 (The Leverage Ladder)
$post = $db->getPostById(12);

if (!$post) {
    echo "❌ Post not found\n";
    exit(1);
}

echo "Testing with:\n";
echo "  Post: {$post['title']}\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Generating 6-8 product recommendations...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$result = $recommender->recommendProducts($post['title'], $post['content']);

if ($result['success'] && !empty($result['products'])) {
    echo "✅ Generated " . count($result['products']) . " products!\n\n";
    
    // Group by type
    $byType = [];
    foreach ($result['products'] as $product) {
        $type = $product['type'] ?? 'OTHER';
        if (!isset($byType[$type])) {
            $byType[$type] = [];
        }
        $byType[$type][] = $product;
    }
    
    // Display organized by type
    $typeIcons = [
        'BOOK' => '📚',
        'JOURNAL' => '📓',
        'SUPPLEMENT' => '💊',
        'FITNESS' => '🏋️',
        'TECH' => '📱',
        'TOOL' => '🔧'
    ];
    
    foreach ($byType as $type => $products) {
        $icon = $typeIcons[$type] ?? '📦';
        echo "{$icon} {$type}S (" . count($products) . "):\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        
        foreach ($products as $i => $product) {
            echo ($i + 1) . ". {$product['title']}\n";
            echo "   Brand: {$product['author']}\n";
            echo "   ASIN: {$product['asin']}\n";
            echo "   Why: {$product['relevance_note']}\n";
            echo "   Link: {$product['amazon_link']}\n\n";
        }
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ TEST SUCCESSFUL!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "This post now has a mix of:\n";
    foreach ($byType as $type => $products) {
        echo "  • " . count($products) . " {$type}(s)\n";
    }
    
    echo "\nReady to run full regeneration? Run:\n";
    echo "  php regenerate-product-recommendations.php\n\n";
    
} else {
    echo "❌ Failed: " . ($result['error'] ?? 'Unknown error') . "\n\n";
}


