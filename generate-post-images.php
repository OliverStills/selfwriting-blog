<?php

/**
 * Generate AI images for existing blog posts that don't have them
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use App\ImageGenerator;

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🎨 GENERATING AI IMAGES FOR BLOG POSTS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

try {
    $db = new Database();
    $imageGenerator = new ImageGenerator();
    
    // Get all posts
    $posts = $db->getAllPosts(100, true);
    
    if (empty($posts)) {
        echo "No posts found.\n";
        exit(0);
    }
    
    echo "Found " . count($posts) . " posts.\n";
    echo "Checking which posts need images...\n\n";
    
    $postsNeedingImages = [];
    foreach ($posts as $post) {
        if (empty($post['image_url']) || $post['image_url'] === null) {
            $postsNeedingImages[] = $post;
        }
    }
    
    if (empty($postsNeedingImages)) {
        echo "✓ All posts already have images!\n";
        exit(0);
    }
    
    echo "Found " . count($postsNeedingImages) . " posts needing images.\n";
    echo "Starting image generation...\n\n";
    
    $generated = 0;
    $failed = 0;
    
    foreach ($postsNeedingImages as $index => $post) {
        $num = $index + 1;
        echo "[$num/" . count($postsNeedingImages) . "] Processing: " . substr($post['title'], 0, 60) . "...\n";
        
        // Generate image
        $imageUrl = $imageGenerator->generateImageForPost($post['title'], $post['excerpt']);
        
        if ($imageUrl) {
            // Update post with image URL
            $db->updatePostImage($post['id'], $imageUrl);
            echo "  ✓ Image generated and saved: $imageUrl\n";
            $generated++;
        } else {
            // Use fallback image
            $fallback = $imageGenerator->getFallbackImage();
            $db->updatePostImage($post['id'], $fallback);
            echo "  ⚠ Image generation failed, using fallback\n";
            $failed++;
        }
        
        echo "\n";
        
        // Delay between API calls to avoid rate limiting
        if ($num < count($postsNeedingImages)) {
            echo "⏳ Waiting 3 seconds before next generation...\n\n";
            sleep(3);
        }
    }
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ IMAGE GENERATION COMPLETE\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    echo "Successfully generated: $generated\n";
    echo "Failed (using fallback): $failed\n";
    echo "Total processed: " . count($postsNeedingImages) . "\n\n";
    echo "Visit your site to see the images!\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

