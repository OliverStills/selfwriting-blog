<?php

require_once __DIR__ . '/../src/config.php';

use App\Database;

$db = new Database();

// Get post ID from URL
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($postId === 0) {
    header('Location: index-new.php');
    exit;
}

$post = $db->getPostById($postId);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    echo '<!DOCTYPE html><html><head><title>Post Not Found</title><link rel="stylesheet" href="styles-new.css"></head><body><div class="container-custom" style="padding: 6rem 0;"><h1 style="font-size: 3rem; margin-bottom: 1rem;">Post Not Found</h1><p style="font-size: 1.125rem; color: var(--muted-foreground);"><a href="index-new.php" style="color: var(--foreground);">← Back to home</a></p></div></body></html>';
    exit;
}

$books = $db->getBooksByPostId($postId);

function formatDate($datetime) {
    return strtoupper(date('F j, Y', strtotime($datetime)));
}

function getPostCategory($title) {
    $stillKeywords = ['brain', 'anxiety', 'overthinking', 'weight', 'spiral', 'noise', 'mental'];
    $gritKeywords = ['motivated', 'discipline', 'consistency', 'habits', 'promises', 'dopamine', 'morning'];
    $reflectionKeywords = ['behind', 'purpose', 'gap', 'clarity', 'stories', 'meaning', 'success'];
    $ascendKeywords = ['career', 'skills', 'leverage', 'bet', 'project', 'strategic', 'growth'];
    
    $lowerTitle = strtolower($title);
    
    foreach ($stillKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'STILL';
    }
    foreach ($gritKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'GRIT';
    }
    foreach ($reflectionKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'REFLECTION';
    }
    foreach ($ascendKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'ASCEND';
    }
    
    return 'INSIGHT';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <title><?php echo htmlspecialchars($post['title']); ?> - The Fifth State</title>
    <link rel="stylesheet" href="styles-new.css">
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container-custom">
            <div class="header-content">
                <a href="index-new.php" class="site-logo">The Fifth State</a>
                <nav class="site-nav">
                    <a href="index-new.php" class="nav-link">Home</a>
                    <a href="index-new.php#about" class="nav-link">About</a>
                    <a href="index-new.php#framework" class="nav-link">Framework</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="section-padding">
        <div class="container-custom">
            <a href="index-new.php" class="back-link">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to all posts
            </a>

            <article class="post-single">
                <header class="post-header">
                    <div class="post-meta">
                        <span class="post-date"><?php echo formatDate($post['created_at']); ?></span>
                        <span class="post-category"><?php echo getPostCategory($post['title']); ?></span>
                    </div>
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
                            Deepen your understanding with these books that align with this post's insights:
                        </p>
                        
                        <div class="books-grid">
                            <?php foreach ($books as $book): ?>
                                <div class="book-card">
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
        </div>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container-custom">
            <div class="footer-content">
                <p class="footer-text">
                    © <?php echo date('Y'); ?> The Fifth State. 
                    Content powered by AI. 
                    <a href="https://github.com/yourusername/the-fifth-state" target="_blank">Open Source</a>
                </p>
                <p class="footer-text">
                    Amazon links are affiliate links.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>

