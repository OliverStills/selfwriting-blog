<?php

/**
 * Generate one blog post for each phase: STILL, DISCIPLINE, REFLECTION, ASCEND
 * For today's date (11/29/2025)
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\PostGenerator;
use App\BookSelector;
use App\ImageGenerator;
use App\Database;

echo "\n╔══════════════════════════════════════════════════════════╗\n";
echo "║  GENERATING POSTS FOR ALL 4 PHASES - " . date('m/d/Y') . "  ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n\n";

$postGenerator = new PostGenerator();
$bookSelector = new BookSelector();
$imageGenerator = new ImageGenerator();
$db = new Database();

// Define phases with their specific topics
$phases = [
    'STILL' => [
        'keywords' => ['anxiety', 'overthinking', 'brain', 'panic', 'nervous', 'numbness'],
        'name' => 'STILL - Mental Clarity'
    ],
    'DISCIPLINE' => [
        'keywords' => ['habits', 'discipline', 'consistency', 'addiction', 'motivation', 'burnout'],
        'name' => 'DISCIPLINE - Daily Action'
    ],
    'REFLECTION' => [
        'keywords' => ['purpose', 'behind', 'bloomer', 'identity', 'story', 'values'],
        'name' => 'REFLECTION - Purpose & Meaning'
    ],
    'ASCEND' => [
        'keywords' => ['career', 'skills', 'leverage', 'pivot', 'growth', 'strategic'],
        'name' => 'ASCEND - Strategic Growth'
    ]
];

$successCount = 0;
$errorCount = 0;

foreach ($phases as $phase => $data) {
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Phase: {$data['name']}\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    try {
        // Generate post
        echo "→ Generating blog content...\n";
        $post = $postGenerator->generatePost();
        
        if (!$post) {
            throw new Exception("Failed to generate post content");
        }
        
        echo "  ✓ Title: {$post['title']}\n";
        echo "  ✓ Word count: " . str_word_count($post['content']) . " words\n\n";
        
        // Save post to database (without image for now)
        echo "→ Saving to database...\n";
        $postId = $db->createPost(
            $post['title'],
            $post['excerpt'],
            $post['content']
        );
        
        if (!$postId) {
            throw new Exception("Failed to save post to database");
        }
        
        echo "  ✓ Post saved (ID: {$postId})\n\n";
        
        // Generate book recommendations
        echo "→ Selecting book recommendations...\n";
        $books = $bookSelector->selectBooks($post['title'], $post['content']);
        
        if ($books && count($books) > 0) {
            echo "  ✓ Found " . count($books) . " book recommendations\n";
            foreach ($books as $book) {
                $db->saveBook($postId, $book);
                echo "    - {$book['title']}\n";
            }
        } else {
            echo "  ⚠ No books recommended\n";
        }
        
        echo "\n✅ SUCCESS - {$data['name']} post created!\n\n";
        $successCount++;
        
        // Small delay between posts to avoid API rate limits
        if ($phase !== 'ASCEND') {
            echo "⏳ Waiting 3 seconds before next phase...\n\n";
            sleep(3);
        }
        
    } catch (Exception $e) {
        echo "\n❌ ERROR - Failed to create {$data['name']} post\n";
        echo "Error: " . $e->getMessage() . "\n\n";
        $errorCount++;
    }
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "SUMMARY\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
echo "✅ Successfully generated: $successCount posts\n";
echo "❌ Failed: $errorCount posts\n";
echo "📅 Date: " . date('F j, Y') . "\n";
echo "\n🎉 All done! Refresh your website to see the new posts.\n\n";

