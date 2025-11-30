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
    echo '<!DOCTYPE html><html><head><title>Post Not Found</title><link rel="stylesheet" href="style.css"></head><body><div class="container-post" style="padding: 6rem 0;"><h1 style="font-size: 3rem; margin-bottom: 1rem;">Post Not Found</h1><p style="font-size: 1.125rem; opacity: 0.7;"><a href="index.php" style="color: var(--text-color);">← Back to home</a></p></div></body></html>';
    exit;
}

$books = $db->getBooksByPostId($postId);

function formatDate($datetime) {
    return strtoupper(date('F j, Y', strtotime($datetime)));
}

function getPostCategory($title) {
    $stillKeywords = ['brain', 'anxiety', 'overthinking', 'rejection', 'worth', 'time', 'panic', 'numbness', 'anhedonia', 'codependency', 'identity', 'comparison', 'peers', 'autopilot', 'shell', 'cutoff', 'nervous'];
    $gritKeywords = ['motivated', 'discipline', 'consistency', 'habits', 'addiction', 'porn', 'weed', 'fumbling', 'interview', 'habit', 'loop', 'burnout', 'hard mode', 'behavioral', 'activation', 'envelope', 'exposure', 'dopamine', 'detox'];
    $reflectionKeywords = ['behind', 'purpose', 'bloomer', 'adult', 'learning', 'social', 'skills', 'awkward', 'authoring', 'story', 'foreclosure', 'mask', 'lost years', 'narrative', 'growth mindset', 'values', 'numb'];
    $ascendKeywords = ['career', 'skills', 'compound', 'leverage', 'pivot', 'bet', 'yourself', 'responsibilities', 'freeze', 'flow', 'regulation', 'second act', 'strategic', 'building'];
    
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
    <meta property="og:title" content="<?php echo htmlspecialchars($post['title']); ?> - The Fifth State">
    <meta property="og:description" content="<?php echo htmlspecialchars($post['excerpt']); ?>">
    <meta property="og:image" content="<?php echo !empty($post['image_url']) ? htmlspecialchars($post['image_url']) : '/fifth-state-skull-banner.png'; ?>">
    <title><?php echo htmlspecialchars($post['title']); ?> - The Fifth State</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="logo">The Fifth State</div>
        <div class="menu-btn"><a href="index.php" style="color: inherit; text-decoration: none;">Back</a></div>
    </header>

    <main class="post-page">
        <!-- Hero Image -->
        <?php if (!empty($post['image_url'])): ?>
        <section class="post-hero">
            <div class="post-hero-image">
                <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                <div class="post-hero-overlay"></div>
            </div>
        </section>
        <?php endif; ?>

        <!-- Post Content -->
        <article class="post-article">
            <div class="container-post">
                <a href="index.php" class="back-link">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to all posts
                </a>

                <header class="post-header-content">
                    <div class="post-meta-header">
                        <span class="post-date-header"><?php echo formatDate($post['created_at']); ?></span>
                        <span class="post-category-header"><?php echo getPostCategory($post['title']); ?></span>
                    </div>
                    <h1 class="post-title-main">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h1>
                </header>

                <div class="post-content-body">
                    <?php echo $post['content']; ?>
                </div>

                <?php if (!empty($books)): ?>
                    <section class="book-recommendations-section">
                        <h2>Recommended Reading</h2>
                        <p class="book-recommendations-intro">
                            Deepen your understanding with these books that align with this post's insights:
                        </p>
                        
                        <div class="books-grid-layrid">
                            <?php foreach ($books as $book): ?>
                                <div class="book-card-layrid">
                                    <h3 class="book-title-layrid">
                                        <?php echo htmlspecialchars($book['title']); ?>
                                    </h3>
                                    <p class="book-author-layrid">
                                        by <?php echo htmlspecialchars($book['author']); ?>
                                    </p>
                                    <p class="book-relevance-layrid">
                                        <?php echo htmlspecialchars($book['relevance_note']); ?>
                                    </p>
                                    <a href="<?php echo htmlspecialchars($book['amazon_link']); ?>" 
                                       class="book-link-layrid" 
                                       target="_blank" 
                                       rel="noopener">
                                        View on Amazon →
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </article>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <h3>The Fifth State</h3>
                <p>For men who refuse to stay stuck.</p>
            </div>
            <div class="footer-info">
                <p>© <?php echo date('Y'); ?> The Fifth State</p>
                <p>Content powered by Claude AI</p>
                <p>Images generated by Nano Banana</p>
                <p>Amazon links are affiliate links</p>
            </div>
        </div>
    </footer>

</body>
</html>




