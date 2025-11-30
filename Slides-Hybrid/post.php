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
        <a href="index.php" class="logo" style="text-decoration: none; color: inherit; cursor: pointer;">The Fifth State</a>
        <div class="menu-btn"><a href="index.php" style="color: inherit; text-decoration: none;">Back</a></div>
    </header>

    <main class="post-page">
        <!-- Hero Banner with Title Overlay -->
        <?php 
        // Use image from database, or fallback to default
        $heroImage = !empty($post['image_url']) ? $post['image_url'] : null;
        ?>
        
        <?php if ($heroImage): ?>
        <section class="post-hero" style="position: relative; height: 60vh; min-height: 500px; overflow: hidden;">
            <div class="post-hero-image" style="position: absolute; inset: 0;">
                <img src="<?php echo htmlspecialchars($heroImage); ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                     style="width: 100%; height: 100%; object-fit: cover;">
                <div class="post-hero-overlay" style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.7));"></div>
            </div>
            
            <!-- Title Overlay on Hero -->
            <div style="position: absolute; inset: 0; z-index: 10; display: flex; flex-direction: column; justify-content: flex-end; padding: 3rem; color: white;">
                <div style="max-width: 1200px; margin: 0 auto; width: 100%;">
                    <div style="margin-bottom: 1rem; display: flex; gap: 1rem; align-items: center;">
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.7);">
                            <?php echo formatDate($post['created_at']); ?>
                        </span>
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; text-transform: uppercase; color: #f97316;">
                            <?php echo getPostCategory($post['title']); ?>
                        </span>
                    </div>
                    <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: clamp(2rem, 5vw, 4rem); font-weight: 700; line-height: 1.1; margin: 0; color: white;">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h1>
                </div>
            </div>
        </section>
        <?php else: ?>
        <!-- Fallback: Regular title if no hero image -->
        <div style="padding-top: 6rem;">
            <div class="container-post">
                <header class="post-header-content">
                    <div class="post-meta-header">
                        <span class="post-date-header"><?php echo formatDate($post['created_at']); ?></span>
                        <span class="post-category-header"><?php echo getPostCategory($post['title']); ?></span>
                    </div>
                    <h1 class="post-title-main">
                        <?php echo htmlspecialchars($post['title']); ?>
                    </h1>
                </header>
            </div>
        </div>
        <?php endif; ?>

        <!-- Post Content -->
        <article class="post-article">
            <div class="container-post" style="<?php echo $heroImage ? 'padding-top: 3rem;' : ''; ?>">

                <div class="post-content-body">
                    <?php echo $post['content']; ?>
                </div>

                <?php if (!empty($books)): 
                    // Filter to only show books
                    $booksOnly = array_filter($books, function($item) {
                        return ($item['type'] ?? 'BOOK') === 'BOOK';
                    });
                ?>
                    <?php if (!empty($booksOnly)): ?>
                    <section class="book-recommendations-section">
                        <h2>Recommended Reading</h2>
                        <p class="book-recommendations-intro">
                            Deepen your understanding with these books that align with this post's insights:
                        </p>
                        
                        <div class="books-grid-layrid">
                            <?php foreach ($booksOnly as $book): ?>
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



