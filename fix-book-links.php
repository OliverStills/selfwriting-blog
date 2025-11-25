<?php

/**
 * Fix existing book recommendations to use proper Amazon product links
 * This will regenerate book recommendations for all existing posts
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\BookSelector;

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== Fixing Amazon Book Links ===\n\n";

try {
    $db = new Database();
    $bookSelector = new BookSelector();
    
    // Get all posts with full content
    $posts = $db->getAllPosts(100, true);
    
    if (empty($posts)) {
        echo "No posts found. Nothing to fix.\n";
        exit(0);
    }
    
    echo "Found " . count($posts) . " posts. Regenerating book recommendations...\n\n";
    
    foreach ($posts as $post) {
        echo "[" . date('H:i:s') . "] Processing: {$post['title']}\n";
        
        // Delete old books for this post
        $db->deleteBooksByPostId($post['id']);
        
        // Generate new book recommendations with ASINs
        echo "  → Calling Claude for book recommendations with ASINs...\n";
        $booksData = $bookSelector->selectBooks($post['title'], $post['content']);
        
        if ($booksData['success'] && !empty($booksData['books'])) {
            foreach ($booksData['books'] as $book) {
                $db->createBook(
                    $post['id'],
                    $book['title'],
                    $book['author'],
                    $book['amazon_link'],
                    $book['relevance_note']
                );
                
                // Show what link was generated
                $asin = $book['asin'] ?? 'N/A';
                echo "  ✓ {$book['title']} by {$book['author']} (ASIN: {$asin})\n";
                echo "    Link: {$book['amazon_link']}\n";
            }
            echo "  ✓ Updated " . count($booksData['books']) . " books\n";
        } else {
            echo "  ✗ Failed to generate books: " . ($booksData['error'] ?? 'Unknown error') . "\n";
        }
        
        echo "\n";
        
        // Small delay to avoid rate limiting
        sleep(2);
    }
    
    echo "=== All book links updated! ===\n";
    echo "Visit http://localhost:8000 to see the updated links.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

