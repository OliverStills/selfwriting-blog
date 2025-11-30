<?php

require_once __DIR__ . '/src/config.php';
use App\Database;

$pdo = Database::getInstance();

echo "\n╔═══════════════════════════════════════════════════════╗\n";
echo "║      DATABASE DIRECT CHECK                          ║\n";
echo "╚═══════════════════════════════════════════════════════╝\n\n";

// Check table structure
echo "Posts table columns:\n";
$stmt = $pdo->query("PRAGMA table_info(posts)");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo "  - {$col['name']} ({$col['type']})\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "First 5 posts with image URLs:\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

$stmt = $pdo->query("SELECT id, title, image_url FROM posts ORDER BY id DESC LIMIT 5");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($posts as $post) {
    echo "Post #{$post['id']}:\n";
    echo "  Title: {$post['title']}\n";
    echo "  Image URL: " . ($post['image_url'] ?? 'NULL') . "\n\n";
}

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

