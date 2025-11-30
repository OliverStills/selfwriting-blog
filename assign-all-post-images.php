<?php

/**
 * Assign unique fallback images to all blog posts
 * Organized by phase with thematic consistency
 */

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use App\Database;
use GuzzleHttp\Client;

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      ASSIGN IMAGES TO ALL BLOG POSTS                ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

$db = new Database();
$posts = $db->getAllPosts(100, false);

echo "Found " . count($posts) . " posts\n\n";

// Phase-themed image collections from Unsplash
$phaseImages = [
    'STILL' => [
        'https://images.unsplash.com/photo-1557683316-973673baf926?w=1920&q=80', // Dark abstract gradient
        'https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=1920&q=80', // Moody ocean
        'https://images.unsplash.com/photo-1579546929518-9e396f3cc809?w=1920&q=80', // Dark abstract
        'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&q=80', // Mountain silhouette
        'https://images.unsplash.com/photo-1508615039623-a25605d2b022?w=1920&q=80', // Dark forest
        'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1920&q=80', // Mountain peak
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=1920&q=80', // Face in shadow
    ],
    'DISCIPLINE' => [
        'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1920&q=80', // Gym weights
        'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=1920&q=80', // Road ahead
        'https://images.unsplash.com/photo-1552674605-db6ffd4facb5?w=1920&q=80', // Runner
        'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1920&q=80', // Workout
        'https://images.unsplash.com/photo-1487956382158-bb926046304a?w=1920&q=80', // Boxing gloves
        'https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?w=1920&q=80', // Mountain climb
        'https://images.unsplash.com/photo-1519834785169-98be25ec3f84?w=1920&q=80', // Dark stairs
    ],
    'REFLECTION' => [
        'https://images.unsplash.com/photo-1502134249126-9f3755a50d78?w=1920&q=80', // Notebook writing
        'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=1920&q=80', // Books stack
        'https://images.unsplash.com/photo-1495364141860-b0d03eccd065?w=1920&q=80', // Mirror reflection
        'https://images.unsplash.com/photo-1509564324749-471bd272e1ff?w=1920&q=80', // Window view
        'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=1920&q=80', // Books and coffee
        'https://images.unsplash.com/photo-1503149779833-1de50ebe5f8a?w=1920&q=80', // Contemplative
        'https://images.unsplash.com/photo-1472289065668-ce650ac443d2?w=1920&q=80', // Reading
    ],
    'ASCEND' => [
        'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=1920&q=80', // Modern architecture
        'https://images.unsplash.com/photo-1497366216548-37526070297c?w=1920&q=80', // Office building
        'https://images.unsplash.com/photo-1551836022-d5d88e9218df?w=1920&q=80', // Business skyline
        'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=1920&q=80', // Laptop workspace
        'https://images.unsplash.com/photo-1557426272-fc759fdf7a8d?w=1920&q=80', // Stairs upward
        'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1920&q=80', // Analytics
        'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=1920&q=80', // Working
    ]
];

function getPostPhase($title) {
    $stillKeywords = ['brain', 'anxiety', 'overthinking', 'rejection', 'worth', 'time', 'panic', 'numbness', 'anhedonia', 'codependency', 'identity', 'comparison', 'peers', 'autopilot', 'shell', 'cutoff', 'nervous'];
    $disciplineKeywords = ['motivated', 'discipline', 'consistency', 'habits', 'addiction', 'porn', 'weed', 'fumbling', 'interview', 'habit', 'loop', 'burnout', 'hard mode', 'behavioral', 'activation', 'envelope', 'exposure', 'dopamine', 'detox'];
    $reflectionKeywords = ['behind', 'purpose', 'bloomer', 'adult', 'learning', 'social', 'skills', 'awkward', 'authoring', 'story', 'foreclosure', 'mask', 'lost years', 'narrative', 'growth mindset', 'values', 'numb'];
    $ascendKeywords = ['career', 'skills', 'compound', 'leverage', 'pivot', 'bet', 'yourself', 'responsibilities', 'freeze', 'flow', 'regulation', 'second act', 'strategic', 'building'];
    
    $lowerTitle = strtolower($title);
    
    foreach ($stillKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'STILL';
    }
    foreach ($disciplineKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'DISCIPLINE';
    }
    foreach ($reflectionKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'REFLECTION';
    }
    foreach ($ascendKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'ASCEND';
    }
    
    return 'STILL'; // Default
}

$httpClient = new Client(['timeout' => 30, 'verify' => false]);
$imagesDir = __DIR__ . '/public/images/posts';

if (!file_exists($imagesDir)) {
    mkdir($imagesDir, 0755, true);
}

$phaseCounters = [
    'STILL' => 0,
    'DISCIPLINE' => 0,
    'REFLECTION' => 0,
    'ASCEND' => 0
];

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Downloading and assigning images...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

foreach ($posts as $post) {
    $phase = getPostPhase($post['title']);
    $imageIndex = $phaseCounters[$phase] % count($phaseImages[$phase]);
    $imageUrl = $phaseImages[$phase][$imageIndex];
    
    echo "Post #{$post['id']}: {$post['title']}\n";
    echo "  Phase: {$phase}\n";
    
    // Create filename
    $titleSlug = strtolower($post['title']);
    $titleSlug = preg_replace('/[^a-z0-9]+/', '-', $titleSlug);
    $titleSlug = trim($titleSlug, '-');
    $titleSlug = substr($titleSlug, 0, 50);
    $filename = $titleSlug . '-' . $post['id'] . '.jpg';
    $filepath = $imagesDir . '/' . $filename;
    
    // Check if already exists
    if (file_exists($filepath)) {
        echo "  ✓ Image already exists\n";
        $relativeUrl = '/images/posts/' . $filename;
    } else {
        echo "  ⏳ Downloading...\n";
        try {
            $response = $httpClient->get($imageUrl);
            $imageData = $response->getBody()->getContents();
            file_put_contents($filepath, $imageData);
            $relativeUrl = '/images/posts/' . $filename;
            $fileSize = filesize($filepath);
            echo "  ✓ Saved (" . number_format($fileSize / 1024, 0) . " KB)\n";
        } catch (Exception $e) {
            echo "  ✗ Download failed: " . $e->getMessage() . "\n";
            $relativeUrl = null;
        }
    }
    
    // Update database (note: may fail if column doesn't exist yet)
    if ($relativeUrl) {
        try {
            $db->updatePostImage($post['id'], $relativeUrl);
            echo "  ✓ Database updated\n";
        } catch (Exception $e) {
            echo "  ⚠ DB update skipped (column may not exist)\n";
        }
    }
    
    echo "\n";
    $phaseCounters[$phase]++;
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ COMPLETE!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "Images assigned by phase:\n";
echo "  STILL: {$phaseCounters['STILL']} posts\n";
echo "  DISCIPLINE: {$phaseCounters['DISCIPLINE']} posts\n";
echo "  REFLECTION: {$phaseCounters['REFLECTION']} posts\n";
echo "  ASCEND: {$phaseCounters['ASCEND']} posts\n\n";

echo "View your posts at: http://localhost:8001/\n\n";


