<?php

require_once __DIR__ . '/../src/config.php';

use App\Database;

$db = new Database();

// Get post ID from URL
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($postId === 0) {
    header('Location: index.php');
    exit;
}

$post = $db->getPostById($postId);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    echo '<!DOCTYPE html><html><head><title>Post Not Found</title><link rel="stylesheet" href="styles.css"></head><body><div class="container"><h1>Post Not Found</h1><p><a href="index.php">← Back to home</a></p></div></body></html>';
    exit;
}

$books = $db->getBooksByPostId($postId);

function formatDate($datetime) {
    return date('F j, Y', strtotime($datetime));
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <title><?php echo htmlspecialchars($post['title']); ?> - Stuck in Adulthood</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="site-header">
            <h1 class="site-title">
                <a href="/">Stuck in Adulthood</a>
            </h1>
            <p class="site-description">
                For ambitious men in their 20s and 30s who feel stuck but want to evolve.
            </p>
        </header>

        <main>
            <a href="index.php" class="back-link">← All posts</a>

            <article class="post-single">
                <header class="post-header">
                    <time class="post-date" datetime="<?php echo htmlspecialchars($post['created_at']); ?>">
                        <?php echo formatDate($post['created_at']); ?>
                    </time>
                    <h1 class="post-title">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h1>
                </header>

                <div class="post-content">
                    <?php echo $post['content']; ?>
                </div>

                <?php if (!empty($books)): ?>
                    <section class="book-recommendations">
                        <h2>Recommended Reading</h2>
                        <p class="book-recommendations-intro">
                            Deepen your understanding with these hand-picked books related to this topic:
                        </p>
                        
                        <div class="books-list">
                            <?php foreach ($books as $book): ?>
                                <div class="book-item">
                                    <h3 class="book-title">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </h3>
                                    <p class="book-author">
                                        by <?php echo htmlspecialchars($book['author']); ?>
                                    </p>
                                    <p class="book-relevance">
                                        <?php echo htmlspecialchars($book['relevance_note']); ?>
                                    </p>
                                    <a href="<?php echo htmlspecialchars($book['amazon_link']); ?>" 
                                       class="book-link" 
                                       target="_blank" 
                                       rel="noopener">
                                        View on Amazon →
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </article>
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

