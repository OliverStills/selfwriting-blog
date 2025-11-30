<?php

/**
 * Regenerate specific posts with fresh, unique content
 * This will delete and recreate posts to get new AI-generated content
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\PostGenerator;
use App\BookSelector;

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== Regenerating Posts with Fresh Content ===\n\n";

// Posts to regenerate (by title pattern)
$postsToRegenerate = [
    "You're Not Overthinking, You're Under-Doing",
    "Why Your Brain Won't Shut Up at 2AM"
];

try {
    $db = new Database();
    $generator = new PostGenerator();
    $bookSelector = new BookSelector();
    
    // Get all posts
    $allPosts = $db->getAllPosts(100, true);
    
    $regeneratedCount = 0;
    
    foreach ($allPosts as $post) {
        $shouldRegenerate = false;
        
        // Check if this post should be regenerated
        foreach ($postsToRegenerate as $pattern) {
            if (stripos($post['title'], $pattern) !== false || $post['title'] === $pattern) {
                $shouldRegenerate = true;
                break;
            }
        }
        
        if (!$shouldRegenerate) {
            continue;
        }
        
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        echo "🔄 Regenerating: {$post['title']}\n";
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
        
        $oldPostId = $post['id'];
        $oldTitle = $post['title'];
        
        // Delete old books
        $db->deleteBooksByPostId($oldPostId);
        echo "  ✓ Deleted old book recommendations\n";
        
        // Generate new content
        echo "  → Generating fresh content with new prompt...\n";
        $postData = $generator->generatePost();
        
        if (!$postData['success']) {
            echo "  ✗ Failed to generate new content: " . ($postData['error'] ?? 'Unknown error') . "\n\n";
            continue;
        }
        
        // Use the same title but with new content
        $postData['title'] = $oldTitle;
        
        echo "  ✓ New content generated!\n";
        echo "  → Content preview: " . substr(strip_tags($postData['content']), 0, 100) . "...\n\n";
        
        // Update the post content in database
        $pdo = $db::getInstance();
        $stmt = $pdo->prepare('
            UPDATE posts 
            SET content = ?, excerpt = ?, created_at = datetime("now")
            WHERE id = ?
        ');
        $stmt->execute([$postData['content'], $postData['excerpt'], $oldPostId]);
        
        echo "  ✓ Updated post content in database\n";
        
        // Generate new book recommendations
        echo "  → Generating new book recommendations...\n";
        $booksData = $bookSelector->selectBooks($oldTitle, $postData['content']);
        
        if ($booksData['success'] && !empty($booksData['books'])) {
            foreach ($booksData['books'] as $book) {
                $db->createBook(
                    $oldPostId,
                    $book['title'],
                    $book['author'],
                    $book['amazon_link'],
                    $book['relevance_note']
                );
            }
            echo "  ✓ Added " . count($booksData['books']) . " new book recommendations\n";
        }
        
        echo "\n✅ Successfully regenerated: {$oldTitle}\n\n";
        $regeneratedCount++;
        
        // Delay between API calls
        if ($regeneratedCount < count($postsToRegenerate)) {
            echo "⏳ Waiting 3 seconds before next regeneration...\n\n";
            sleep(3);
        }
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "🎉 COMPLETE! Regenerated {$regeneratedCount} posts\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Visit http://localhost:8000 to see the fresh content!\n\n";
    
    if ($regeneratedCount === 0) {
        echo "Note: No matching posts found. Check that post titles match exactly.\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}




