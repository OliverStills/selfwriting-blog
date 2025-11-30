<?php

/**
 * Generate 4 posts immediately (bypassing time checks)
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\PostGenerator;
use App\BookSelector;
use App\Database;

echo "\n╔════════════════════════════════════════╗\n";
echo "║  GENERATING 4 POSTS FOR 11/29/2025  ║\n";
echo "╚════════════════════════════════════════╝\n\n";

$postGenerator = new PostGenerator();
$bookSelector = new BookSelector();
$db = new Database();

for ($i = 1; $i <= 4; $i++) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Post $i of 4\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    try {
        // Generate post
        echo "→ Generating content...\n";
        $post = $postGenerator->generatePost();
        
        if (!$post) {
            throw new Exception("Failed to generate post");
        }
        
        echo "  ✓ Title: {$post['title']}\n";
        echo "  ✓ Words: " . str_word_count($post['content']) . "\n\n";
        
        // Save to database
        echo "→ Saving to database...\n";
        $postId = $db->createPost(
            $post['title'],
            $post['content'],
            $post['excerpt']
        );
        
        echo "  ✓ Post ID: $postId\n\n";
        
        // Generate books
        echo "→ Getting book recommendations...\n";
        try {
            $books = $bookSelector->selectBooks($post['title'], $post['content']);
            
            if ($books && is_array($books) && count($books) > 0) {
                foreach ($books as $book) {
                    if (isset($book['title']) && isset($book['author'])) {
                        $db->createBook(
                            $postId,
                            $book['title'],
                            $book['author'],
                            $book['amazon_link'],
                            $book['relevance_note']
                        );
                    }
                }
                echo "  ✓ Added " . count($books) . " books\n";
            } else {
                echo "  ⚠ No books available\n";
            }
        } catch (Exception $e) {
            echo "  ⚠ Book generation skipped: " . $e->getMessage() . "\n";
        }
        
        echo "\n✅ Post $i complete!\n\n";
        
        // Delay between posts
        if ($i < 4) {
            echo "⏳ Waiting 3 seconds...\n\n";
            sleep(3);
        }
        
    } catch (Exception $e) {
        echo "\n❌ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "COMPLETE!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "🎉 4 new posts generated for " . date('F j, Y') . "!\n";
echo "📍 Refresh http://localhost:8001 to see them.\n\n";

