<?php

/**
 * Automated Post Generation Script - Stuck in Adulthood
 * 
 * Generates content for ambitious men in their 20s and 30s who feel stuck or directionless.
 * Uses The Fifth State framework: Still, Grit, Reflection, Ascend.
 * 
 * This script is designed to be run via cron job.
 * It checks if enough time has passed since the last post and generates a new one if needed.
 * 
 * For testing: runs every 5 minutes
 * For production: runs every 24 hours
 */

require_once __DIR__ . '/../src/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;
use App\PostGenerator;
use App\BookSelector;
use App\ImageGenerator;

// Enable error logging
ini_set('display_errors', ENVIRONMENT === 'testing' ? 1 : 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

function log_message(string $message): void
{
    $timestamp = date('Y-m-d H:i:s');
    $logFile = __DIR__ . '/../database/cron.log';
    file_put_contents($logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
    
    if (php_sapi_name() === 'cli') {
        echo "[{$timestamp}] {$message}\n";
    }
}

function shouldGeneratePost(Database $db): bool
{
    $lastPostTime = $db->getLastPostTime();
    
    if ($lastPostTime === null) {
        log_message('No posts found. Generating first post.');
        return true;
    }
    
    $lastPostTimestamp = strtotime($lastPostTime);
    $currentTimestamp = time();
    $minutesSinceLastPost = ($currentTimestamp - $lastPostTimestamp) / 60;
    
    $intervalMinutes = CRON_INTERVAL;
    
    if ($minutesSinceLastPost >= $intervalMinutes) {
        log_message(sprintf(
            'Time to generate new post. Last post: %s (%.1f minutes ago, interval: %d minutes)',
            $lastPostTime,
            $minutesSinceLastPost,
            $intervalMinutes
        ));
        return true;
    }
    
    log_message(sprintf(
        'Not time yet. Last post: %s (%.1f minutes ago, waiting for %d minutes)',
        $lastPostTime,
        $minutesSinceLastPost,
        $intervalMinutes
    ));
    return false;
}

function generateAndSavePost(): void
{
    log_message('=== Starting post generation ===');
    
    try {
        $db = new Database();
        
        if (!shouldGeneratePost($db)) {
            log_message('=== Skipping generation - not time yet ===');
            return;
        }
        
        // Generate post
        log_message('Calling PostGenerator...');
        $generator = new PostGenerator();
        $postData = $generator->generatePost();
        
        if (!$postData['success']) {
            throw new Exception('Post generation failed: ' . ($postData['error'] ?? 'Unknown error'));
        }
        
        log_message('Post generated successfully: ' . $postData['title']);
        
        // Generate image for the post
        log_message('Generating AI image for post...');
        $imageGenerator = new ImageGenerator();
        $imageUrl = $imageGenerator->generateImageForPost($postData['title'], $postData['excerpt']);
        
        if ($imageUrl) {
            log_message('Image generated successfully: ' . $imageUrl);
        } else {
            log_message('Image generation failed, using fallback');
            $imageUrl = $imageGenerator->getFallbackImage();
        }
        
        // Save post to database
        $postId = $db->createPost(
            $postData['title'],
            $postData['content'],
            $postData['excerpt'],
            $imageUrl
        );
        
        log_message('Post saved with ID: ' . $postId);
        
        // Generate book recommendations
        log_message('Calling BookSelector...');
        $bookSelector = new BookSelector();
        $booksData = $bookSelector->selectBooks($postData['title'], $postData['content']);
        
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
        } else {
            log_message('Warning: No books were selected: ' . ($booksData['error'] ?? 'Unknown reason'));
        }
        
        // Log successful generation
        $db->logGeneration(true);
        
        log_message('=== Post generation complete ===');
        
    } catch (Exception $e) {
        log_message('ERROR: ' . $e->getMessage());
        log_message('Stack trace: ' . $e->getTraceAsString());
        
        // Log failed generation
        try {
            $db = new Database();
            $db->logGeneration(false, $e->getMessage());
        } catch (Exception $logError) {
            log_message('Failed to log error to database: ' . $logError->getMessage());
        }
        
        log_message('=== Post generation failed ===');
        exit(1);
    }
}

// Run the generation
generateAndSavePost();

