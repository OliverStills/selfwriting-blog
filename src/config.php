<?php

// Load environment variables
require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Configuration constants
define('CLAUDE_API_KEY', $_ENV['CLAUDE_API_KEY'] ?? '');
define('AMAZON_AFFILIATE_ID', $_ENV['AMAZON_AFFILIATE_ID'] ?? '');
define('CRON_SECRET', $_ENV['CRON_SECRET'] ?? '');
define('ENVIRONMENT', $_ENV['ENVIRONMENT'] ?? 'production');
define('CRON_INTERVAL', (int)($_ENV['CRON_INTERVAL'] ?? 1440));
define('BASE_PATH', __DIR__ . '/..');
define('DB_PATH', BASE_PATH . '/database/blog.db');

// Claude API settings
define('CLAUDE_API_URL', 'https://api.anthropic.com/v1/messages');
define('CLAUDE_MODEL', 'claude-sonnet-4-20250514');
define('CLAUDE_API_VERSION', '2023-06-01');

