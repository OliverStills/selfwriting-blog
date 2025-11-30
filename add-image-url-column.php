<?php

/**
 * Add image_url column to posts table
 */

require_once __DIR__ . '/src/config.php';
use App\Database;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      ADD IMAGE_URL COLUMN TO DATABASE               ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

try {
    $pdo = Database::getInstance();
    
    // Check if column already exists
    $stmt = $pdo->query("PRAGMA table_info(posts)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasImageUrl = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'image_url') {
            $hasImageUrl = true;
            break;
        }
    }
    
    if ($hasImageUrl) {
        echo "✓ Column 'image_url' already exists\n\n";
    } else {
        echo "Adding 'image_url' column to posts table...\n";
        $pdo->exec("ALTER TABLE posts ADD COLUMN image_url TEXT");
        echo "✅ Column added successfully!\n\n";
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Now updating post images in database...\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    // Read all image files and update database
    $imagesDir = __DIR__ . '/public/images/posts';
    $files = glob($imagesDir . '/*.jpg');
    
    $db = new Database();
    $updated = 0;
    
    foreach ($files as $filepath) {
        $filename = basename($filepath);
        
        // Extract post ID from filename (format: title-slug-{id}.jpg)
        if (preg_match('/-(\d+)\.jpg$/', $filename, $matches)) {
            $postId = (int)$matches[1];
            $relativeUrl = '/images/posts/' . $filename;
            
            try {
                $db->updatePostImage($postId, $relativeUrl);
                echo "✓ Updated post #{$postId}\n";
                $updated++;
            } catch (Exception $e) {
                echo "✗ Failed to update post #{$postId}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ COMPLETE!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Updated {$updated} posts with image URLs\n";
    echo "View your blog: http://localhost:8001/\n\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    exit(1);
}

