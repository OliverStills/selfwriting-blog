<?php

/**
 * Regenerate product recommendations for all existing posts
 * Includes books + other relevant Amazon products
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\ProductRecommender;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║  REGENERATE PRODUCT RECOMMENDATIONS (EXPANDED)      ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$db = new Database();
$recommender = new ProductRecommender();

// First, add product_type column if it doesn't exist
echo "Checking database schema...\n";
try {
    $pdo = Database::getInstance();
    $stmt = $pdo->query("PRAGMA table_info(books)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasTypeColumn = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'type') {
            $hasTypeColumn = true;
            break;
        }
    }
    
    if (!$hasTypeColumn) {
        echo "Adding 'type' column to books table...\n";
        $pdo->exec("ALTER TABLE books ADD COLUMN type TEXT DEFAULT 'BOOK'");
        echo "✓ Column added\n\n";
    } else {
        echo "✓ Column exists\n\n";
    }
} catch (Exception $e) {
    echo "⚠ Database schema check failed: " . $e->getMessage() . "\n\n";
}

// Get all posts
$db = new Database();
$posts = $db->getAllPosts(100, true);

echo "Found " . count($posts) . " posts\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$successCount = 0;
$failureCount = 0;

foreach ($posts as $index => $post) {
    $postNum = $index + 1;
    echo "[$postNum/" . count($posts) . "] Post #{$post['id']}: {$post['title']}\n";
    
    // Delete existing recommendations
    echo "  🗑️  Clearing old recommendations...\n";
    $db->deleteBooksByPostId($post['id']);
    
    // Generate new expanded recommendations
    echo "  🤖 Generating 6-8 product recommendations...\n";
    $result = $recommender->recommendProducts($post['title'], $post['content']);
    
    if ($result['success'] && !empty($result['products'])) {
        echo "  ✓ Generated " . count($result['products']) . " products:\n";
        
        foreach ($result['products'] as $product) {
            echo "    • {$product['type']}: {$product['title']}\n";
            
            try {
                $db->createBook(
                    $post['id'],
                    $product['title'],
                    $product['author'],
                    $product['relevance_note'],
                    $product['amazon_link'],
                    $product['type'] ?? 'BOOK'
                );
            } catch (Exception $e) {
                echo "    ⚠ Failed to save: " . $e->getMessage() . "\n";
            }
        }
        
        echo "  ✅ Saved to database\n";
        $successCount++;
    } else {
        echo "  ❌ Failed: " . ($result['error'] ?? 'No products returned') . "\n";
        $failureCount++;
    }
    
    echo "\n";
    
    // Add delay to avoid rate limiting
    if ($postNum < count($posts)) {
        echo "  ⏸️  Waiting 3 seconds...\n\n";
        sleep(3);
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ COMPLETE!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Results:\n";
echo "  ✓ Success: {$successCount} posts\n";
echo "  ✗ Failed: {$failureCount} posts\n\n";

echo "Each post now has:\n";
echo "  📚 Books (2-3)\n";
echo "  📓 Journals/Planners\n";
echo "  💊 Supplements (if relevant)\n";
echo "  🏋️ Fitness gear (if relevant)\n";
echo "  🔧 Tools & tech\n\n";

echo "View updated posts: http://localhost:8001/\n\n";

