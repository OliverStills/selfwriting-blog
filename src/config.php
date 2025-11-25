<?php

/**
 * Configuration for Railway Deployment
 * Uses config.local.php for local development (if exists)
 * Uses environment variables for Railway deployment
 */

// Check if local config exists (for local development)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
    return; // Skip the rest - local config has everything
}

// Production/Railway Configuration
// Load environment variables from Railway or .env
require_once __DIR__ . '/../vendor/autoload.php';

// Try to load .env if it exists (optional on Railway)
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
}

// Configuration constants from environment variables
define('CLAUDE_API_KEY', getenv('CLAUDE_API_KEY') ?: $_ENV['CLAUDE_API_KEY'] ?? '');
define('AMAZON_AFFILIATE_ID', getenv('AMAZON_AFFILIATE_ID') ?: $_ENV['AMAZON_AFFILIATE_ID'] ?? '');
define('CRON_SECRET', getenv('CRON_SECRET') ?: $_ENV['CRON_SECRET'] ?? '');
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: $_ENV['ENVIRONMENT'] ?? 'production');
define('CRON_INTERVAL', (int)(getenv('CRON_INTERVAL') ?: $_ENV['CRON_INTERVAL'] ?? 1440));
define('NANO_BANANA_API_KEY', getenv('NANO_BANANA_API_KEY') ?: $_ENV['NANO_BANANA_API_KEY'] ?? '');

// Base paths
define('BASE_PATH', __DIR__ . '/..');
define('DB_PATH', BASE_PATH . '/database/blog.db');

// Claude API settings
define('CLAUDE_API_URL', 'https://api.anthropic.com/v1/messages');
define('CLAUDE_MODEL', 'claude-sonnet-4-20250514');
define('CLAUDE_API_VERSION', '2023-06-01');

