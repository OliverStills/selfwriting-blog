<?php

/**
 * Generate one post for each pillar of The Fifth State framework
 * Still, Grit, Reflection, Ascend
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\PostGenerator;
use App\BookSelector;

// Enable error logging
ini_set('display_errors', 1);
error_reporting(E_ALL);

function log_message(string $message): void
{
    $timestamp = date('Y-m-d H:i:s');
    echo "[{$timestamp}] {$message}\n";
}

// Topics organized by pillar
$pillarTopics = [
    'STILL' => [
        'Why Your Brain Won\'t Shut Up at 2AM',
        'The Anxiety Tax of Living in Comparison Mode',
        'You\'re Not Overthinking, You\'re Under-Doing',
        'The Weight of Potential: When Being Smart Makes You Stuck',
    ],
    'GRIT' => [
        'Stop Waiting to Feel Motivated',
        'The 3-Hour Rule: Why Daily Consistency Beats Yearly Goals',
        'Why You Keep Breaking Promises to Yourself',
        'The Morning After: Building Systems That Survive Your Worst Days',
    ],
    'REFLECTION' => [
        'You\'re Not Behind, You\'re Recalibrating',
        'What Your 18-Year-Old Self Got Wrong About Success',
        'The Gap Between Who You Are and Who You Could Be',
        'Why Clarity Comes From Action, Not Meditation',
    ],
    'ASCEND' => [
        'Career Pivots in Your 30s: From Stuck to Strategic',
        'Building Skills That Actually Compound',
        'The Leverage Ladder: Trading Time for Impact',
        'How to Bet on Yourself When You Have Responsibilities',
    ]
];

try {
    $db = new Database();
    $generator = new PostGenerator();
    $bookSelector = new BookSelector();
    
    $totalPosts = 0;
    
    foreach ($pillarTopics as $pillar => $topics) {
        log_message("=== Generating {$pillar} post ===");
        
        // Pick first topic from this pillar
        $topic = $topics[0];
        log_message("Topic: {$topic}");
        
        // Generate post using reflection to access private method
        $reflection = new ReflectionClass($generator);
        
        // Build prompt
        $buildPromptMethod = $reflection->getMethod('buildPrompt');
        $buildPromptMethod->setAccessible(true);
        $prompt = $buildPromptMethod->invoke($generator, $topic);
        
        // Call Claude API
        $callApiMethod = $reflection->getMethod('callClaudeAPI');
        $callApiMethod->setAccessible(true);
        $response = $callApiMethod->invoke($generator, $prompt);
        
        // Parse response
        $parseMethod = $reflection->getMethod('parseResponse');
        $parseMethod->setAccessible(true);
        $content = $parseMethod->invoke($generator, $response);
        
        // Generate excerpt
        $excerptMethod = $reflection->getMethod('generateExcerpt');
        $excerptMethod->setAccessible(true);
        $excerpt = $excerptMethod->invoke($generator, $content);
        
        log_message("Post content generated successfully");
        
        // Save post to database
        $postId = $db->createPost($topic, $content, $excerpt);
        log_message("Post saved with ID: {$postId}");
        
        // Generate book recommendations
        log_message("Generating book recommendations...");
        $booksData = $bookSelector->selectBooks($topic, $content);
        
        if ($booksData['success'] && !empty($booksData['books'])) {
            foreach ($booksData['books'] as $book) {
                $db->createBook(
                    $postId,
                    $book['title'],
                    $book['author'],
                    $book['amazon_link'],
                    $book['relevance_note']
                );
            }
            log_message('Saved ' . count($booksData['books']) . ' book recommendations');
        }
        
        $totalPosts++;
        log_message("=== {$pillar} post complete ===\n");
        
        // Small delay between API calls
        if ($pillar !== 'ASCEND') {
            log_message("Waiting 2 seconds before next generation...\n");
            sleep(2);
        }
    }
    
    log_message("=== ALL PILLARS COMPLETE ===");
    log_message("Total posts generated: {$totalPosts}");
    log_message("Visit http://localhost:8000 to see your posts!");
    
} catch (Exception $e) {
    log_message('ERROR: ' . $e->getMessage());
    log_message('Stack trace: ' . $e->getTraceAsString());
    exit(1);
}

