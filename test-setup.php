<?php

/**
 * Setup Testing Script
 * 
 * Run this script to verify your installation is configured correctly.
 * Usage: php test-setup.php
 */

echo "=== Stuck in Adulthood - Setup Test ===\n\n";

// Check PHP version
echo "1. Checking PHP version...\n";
$phpVersion = PHP_VERSION;
$requiredVersion = '8.2.0';
if (version_compare($phpVersion, $requiredVersion, '>=')) {
    echo "   ✓ PHP version: {$phpVersion} (required: {$requiredVersion}+)\n";
} else {
    echo "   ✗ PHP version: {$phpVersion} (required: {$requiredVersion}+)\n";
    exit(1);
}

// Check if vendor directory exists
echo "\n2. Checking Composer dependencies...\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "   ✓ Composer dependencies installed\n";
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    echo "   ✗ Composer dependencies not found. Run: composer install\n";
    exit(1);
}

// Check for .env file
echo "\n3. Checking environment configuration...\n";
if (file_exists(__DIR__ . '/.env')) {
    echo "   ✓ .env file exists\n";
    
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Check required variables
    $hasClaudeKey = !empty($_ENV['CLAUDE_API_KEY'] ?? '');
    $hasAffiliateId = !empty($_ENV['AMAZON_AFFILIATE_ID'] ?? '');
    
    if ($hasClaudeKey) {
        $keyPreview = substr($_ENV['CLAUDE_API_KEY'], 0, 10) . '...';
        echo "   ✓ CLAUDE_API_KEY is set ({$keyPreview})\n";
    } else {
        echo "   ✗ CLAUDE_API_KEY is not set or empty\n";
    }
    
    if ($hasAffiliateId) {
        echo "   ✓ AMAZON_AFFILIATE_ID is set\n";
    } else {
        echo "   ⚠ AMAZON_AFFILIATE_ID is not set (optional for testing)\n";
    }
    
    $environment = $_ENV['ENVIRONMENT'] ?? 'production';
    $cronInterval = $_ENV['CRON_INTERVAL'] ?? 1440;
    echo "   ✓ ENVIRONMENT: {$environment}\n";
    echo "   ✓ CRON_INTERVAL: {$cronInterval} minutes\n";
    
} else {
    echo "   ✗ .env file not found. Copy env.example to .env and configure it.\n";
    exit(1);
}

// Check database directory
echo "\n4. Checking database directory...\n";
$dbDir = __DIR__ . '/database';
if (!is_dir($dbDir)) {
    echo "   ⚠ Database directory doesn't exist. Creating...\n";
    mkdir($dbDir, 0755, true);
}
if (is_writable($dbDir)) {
    echo "   ✓ Database directory is writable\n";
} else {
    echo "   ✗ Database directory is not writable\n";
    exit(1);
}

// Test database connection
echo "\n5. Testing database connection...\n";
try {
    require_once __DIR__ . '/src/config.php';
    $db = new App\Database();
    echo "   ✓ Database connection successful\n";
    echo "   ✓ Database tables created\n";
    
    // Check for existing posts
    $lastPost = $db->getLastPostTime();
    if ($lastPost) {
        echo "   ✓ Database has posts (last: {$lastPost})\n";
    } else {
        echo "   ℹ Database is empty (no posts yet)\n";
    }
} catch (Exception $e) {
    echo "   ✗ Database error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test Claude API connection (optional)
echo "\n6. Testing Claude API connection...\n";
if ($hasClaudeKey) {
    try {
        $client = new GuzzleHttp\Client([
            'timeout' => 10,
            'headers' => [
                'x-api-key' => $_ENV['CLAUDE_API_KEY'],
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ]
        ]);
        
        // Make a minimal test request
        $response = $client->post('https://api.anthropic.com/v1/messages', [
            'json' => [
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 10,
                'messages' => [
                    ['role' => 'user', 'content' => 'Say "Hello"']
                ]
            ]
        ]);
        
        if ($response->getStatusCode() === 200) {
            echo "   ✓ Claude API connection successful\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Claude API error: " . $e->getMessage() . "\n";
        echo "   (Check your API key and credits)\n";
    }
} else {
    echo "   ⊘ Skipped (no API key configured)\n";
}

echo "\n=== Setup Test Complete ===\n\n";

echo "Next steps:\n";
echo "1. Start local server: php -S localhost:8000 -t public\n";
echo "2. Generate first post: php cron/generate-post.php\n";
echo "3. Open browser: http://localhost:8000\n\n";

echo "For deployment to Railway, see README.md\n";

