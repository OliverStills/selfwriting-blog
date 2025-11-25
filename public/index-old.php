<?php

require_once __DIR__ . '/../src/config.php';

use App\Database;

$db = new Database();
$posts = $db->getAllPosts(50);

function formatDate($datetime) {
    return date('M j, Y', strtotime($datetime));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="For ambitious men in their 20s and 30s who feel stuck or behind. Stoic wisdom meets tactical action.">
    <title>Stuck in Adulthood - For Men Who Feel Stuck But Want to Evolve</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="site-header">
            <h1 class="site-title">
                <a href="/">Stuck in Adulthood</a>
            </h1>
            <p class="site-description">
                For ambitious men in their 20s and 30s who feel stuck, behind, or directionless. 
                Stoic wisdom meets tactical action.
            </p>
        </header>

        <main>
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <h2>No Posts Yet</h2>
                    <p>Check back soon! The first post is being generated.</p>
                </div>
            <?php else: ?>
                <div class="post-feed">
                    <?php foreach ($posts as $post): ?>
                        <article class="post-card">
                            <time class="post-date" datetime="<?php echo htmlspecialchars($post['created_at']); ?>">
                                <?php echo formatDate($post['created_at']); ?>
                            </time>
                            <h2 class="post-title">
                                <a href="post.php?id=<?php echo $post['id']; ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h2>
                            <p class="post-excerpt">
                                <?php echo $post['excerpt']; ?>
                            </p>
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">
                                Keep reading →
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

        <footer class="site-footer">
            <p>
                © <?php echo date('Y'); ?> Stuck in Adulthood. 
                Content generated with Claude AI. 
                Amazon links are affiliate links.
            </p>
        </footer>
    </div>
</body>
</html>

