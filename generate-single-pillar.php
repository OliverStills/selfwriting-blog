<?php

/**
 * Generate a single post for a specific pillar
 * Usage: php generate-single-pillar.php STILL|GRIT|REFLECTION|ASCEND
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\PostGenerator;
use App\BookSelector;

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get pillar from command line argument
$pillar = $argv[1] ?? 'STILL';
$pillar = strtoupper($pillar);

$topics = [
    'STILL' => 'Why Your Brain Won\'t Shut Up at 2AM',
    'GRIT' => 'Stop Waiting to Feel Motivated',
    'REFLECTION' => 'You\'re Not Behind, You\'re Recalibrating',
    'ASCEND' => 'Building Skills That Actually Compound'
];

if (!isset($topics[$pillar])) {
    echo "Invalid pillar. Use: STILL, GRIT, REFLECTION, or ASCEND\n";
    exit(1);
}

$topic = $topics[$pillar];

echo "[" . date('Y-m-d H:i:s') . "] Generating {$pillar} post...\n";
echo "[" . date('Y-m-d H:i:s') . "] Topic: {$topic}\n";
echo "[" . date('Y-m-d H:i:s') . "] Calling Claude API (this may take 10-30 seconds)...\n";

try {
    $db = new Database();
    $generator = new PostGenerator();
    
    // Use reflection to access private methods
    $reflection = new ReflectionClass($generator);
    
    $buildPromptMethod = $reflection->getMethod('buildPrompt');
    $buildPromptMethod->setAccessible(true);
    $prompt = $buildPromptMethod->invoke($generator, $topic);
    
    $callApiMethod = $reflection->getMethod('callClaudeAPI');
    $callApiMethod->setAccessible(true);
    $response = $callApiMethod->invoke($generator, $prompt);
    
    echo "[" . date('Y-m-d H:i:s') . "] Content received from Claude!\n";
    
    $parseMethod = $reflection->getMethod('parseResponse');
    $parseMethod->setAccessible(true);
    $content = $parseMethod->invoke($generator, $response);
    
    $excerptMethod = $reflection->getMethod('generateExcerpt');
    $excerptMethod->setAccessible(true);
    $excerpt = $excerptMethod->invoke($generator, $content);
    
    $postId = $db->createPost($topic, $content, $excerpt);
    echo "[" . date('Y-m-d H:i:s') . "] ✓ Post saved with ID: {$postId}\n";
    
    echo "[" . date('Y-m-d H:i:s') . "] Generating book recommendations...\n";
    $bookSelector = new BookSelector();
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
        echo "[" . date('Y-m-d H:i:s') . "] ✓ Saved " . count($booksData['books']) . " book recommendations\n";
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] === {$pillar} POST COMPLETE ===\n\n";
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] ERROR: " . $e->getMessage() . "\n";
    exit(1);
}




