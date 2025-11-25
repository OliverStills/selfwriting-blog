<?php

/**
 * Web-Accessible Cron Trigger for External Services (cron-job.org)
 * 
 * This endpoint can be called by external cron services like cron-job.org
 * to trigger automated post generation.
 * 
 * Security: Requires CRON_SECRET token in URL parameter
 * 
 * Usage: https://your-domain.com/cron-trigger.php?secret=your_secret_here
 */

// Set content type for proper HTTP response
header('Content-Type: application/json');

// Load configuration
require_once __DIR__ . '/../src/config.php';

// Security check: Verify secret token
$providedSecret = $_GET['secret'] ?? '';
$requiredSecret = $_ENV['CRON_SECRET'] ?? '';

if (empty($requiredSecret)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'CRON_SECRET not configured in environment variables',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

if ($providedSecret !== $requiredSecret) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid or missing secret token',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

// If we get here, authentication passed - now run the generation logic
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\PostGenerator;
use App\BookSelector;

// Capture output for logging
ob_start();

try {
    $db = new Database();
    
    // Check if we should generate a post
    $lastPostTime = $db->getLastPostTime();
    
    if ($lastPostTime !== null) {
        $lastPostTimestamp = strtotime($lastPostTime);
        $currentTimestamp = time();
        $minutesSinceLastPost = ($currentTimestamp - $lastPostTimestamp) / 60;
        $intervalMinutes = CRON_INTERVAL;
        
        if ($minutesSinceLastPost < $intervalMinutes) {
            $output = ob_get_clean();
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'action' => 'skipped',
                'message' => 'Not time to generate yet',
                'last_post' => $lastPostTime,
                'minutes_since_last' => round($minutesSinceLastPost, 1),
                'interval_required' => $intervalMinutes,
                'next_post_in_minutes' => round($intervalMinutes - $minutesSinceLastPost, 1),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            exit;
        }
    }
    
    // Generate post
    $generator = new PostGenerator();
    $postData = $generator->generatePost();
    
    if (!$postData['success']) {
        throw new Exception('Post generation failed: ' . ($postData['error'] ?? 'Unknown error'));
    }
    
    // Save post to database
    $postId = $db->createPost(
        $postData['title'],
        $postData['content'],
        $postData['excerpt']
    );
    
    // Generate book recommendations
    $bookSelector = new BookSelector();
    $booksData = $bookSelector->selectBooks($postData['title'], $postData['content']);
    
    $booksCount = 0;
    if ($booksData['success'] && !empty($booksData['books'])) {
        foreach ($booksData['books'] as $book) {
            $db->createBook(
                $postId,
                $book['title'],
                $book['author'],
                $book['amazon_link'],
                $book['relevance_note']
            );
            $booksCount++;
        }
    }
    
    // Log successful generation
    $db->logGeneration(true);
    
    $output = ob_get_clean();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'action' => 'generated',
        'message' => 'Post generated successfully',
        'post_id' => $postId,
        'post_title' => $postData['title'],
        'books_count' => $booksCount,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    $output = ob_get_clean();
    
    // Log failed generation
    try {
        $db = new Database();
        $db->logGeneration(false, $e->getMessage());
    } catch (Exception $logError) {
        // Silent fail on log error
    }
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'action' => 'error',
        'error' => $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}


