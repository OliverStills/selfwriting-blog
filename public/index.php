<?php

require_once __DIR__ . '/../src/config.php';

use App\Database;

$db = new Database();
$posts = $db->getAllPosts(50);

function formatDate($datetime) {
    return strtoupper(date('M j, Y', strtotime($datetime)));
}

function getPostCategory($title) {
    // Categorize posts based on The Fifth State framework
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
    <meta name="description" content="For ambitious men in their 20s and 30s who feel stuck, behind in life, or directionless but want to evolve.">
    <meta property="og:title" content="The Fifth State - Transform Your Life">
    <meta property="og:description" content="Stoic wisdom meets tactical action. For men who refuse to stay stuck.">
    <meta property="og:image" content="/fifth-state-skull-banner.png">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="The Fifth State">
    <meta name="twitter:description" content="For ambitious men who feel stuck but want to evolve.">
    <meta name="twitter:image" content="/fifth-state-skull-banner.png">
    <title>The Fifth State - Transform Your Life</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container-custom">
            <div class="header-content">
                <a href="/" class="site-logo">The Fifth State</a>
                <nav class="site-nav">
                    <a href="/" class="nav-link">Home</a>
                    <a href="#about" class="nav-link">About</a>
                    <a href="#framework" class="nav-link">Framework</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container-custom">
            <!-- Hero Banner Video -->
            <div class="hero-image-wrapper">
                <video src="fifth-state-video.mp4" class="hero-banner-image" autoplay loop muted playsinline>
                    Your browser does not support the video tag.
                </video>
            </div>
            
            <div class="hero-content">
                <h1 class="hero-title">
                    For men who refuse to stay stuck.
                </h1>
                <p class="hero-subtitle">
                    Stoic wisdom meets tactical action
                </p>
                <p class="hero-description">
                    You're ambitious. You know you have potential. But you feel stuck, behind, or directionless. 
                    This is your space to evolve—through mental clarity, discipline, purpose, and strategic growth.
                </p>
            </div>
        </div>
    </section>

    <!-- Posts Section -->
    <main class="section-padding" style="padding-top: 0;">
        <div class="container-custom">
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <h2>First Post Coming Soon</h2>
                    <p>Check back soon for insights on breaking through being stuck.</p>
                </div>
            <?php else: ?>
                <div class="post-grid">
                    <?php foreach ($posts as $post): ?>
                        <article class="post-card">
                            <div class="post-card-content">
                                <div class="post-meta">
                                    <span class="post-date"><?php echo formatDate($post['created_at']); ?></span>
                                    <span class="post-category"><?php echo getPostCategory($post['title']); ?></span>
                                </div>
                                <h2 class="post-title">
                                    <a href="post.php?id=<?php echo $post['id']; ?>">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </h2>
                                <p class="post-excerpt">
                                    <?php echo $post['excerpt']; ?>
                                </p>
                                <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">
                                    Read more
                                    <svg class="arrow-right" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- About Section -->
    <section id="about" class="section-padding" style="background: rgba(255, 255, 255, 0.02);">
        <div class="container-custom">
            <div style="max-width: 48rem; margin: 0 auto; text-align: center;">
                <h2 style="font-size: 2.5rem; font-weight: 600; margin-bottom: 1.5rem;">The Fifth State Framework</h2>
                <p style="font-size: 1.125rem; color: var(--muted-foreground); margin-bottom: 3rem;">
                    A system for evolving from stuck to unstoppable
                </p>
                <div style="display: grid; gap: 2rem; text-align: left;">
                    <div style="padding: 1.5rem; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius);">
                        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">STILL → Mental Clarity</h3>
                        <p style="color: var(--muted-foreground);">Calm the noise, get your head clear, understand what's really happening</p>
                    </div>
                    <div style="padding: 1.5rem; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius);">
                        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">GRIT → Daily Discipline</h3>
                        <p style="color: var(--muted-foreground);">Show up consistently, build habits that survive your worst days</p>
                    </div>
                    <div style="padding: 1.5rem; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius);">
                        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">REFLECTION → Purpose & Meaning</h3>
                        <p style="color: var(--muted-foreground);">Make sense of your story, figure out what you're really chasing</p>
                    </div>
                    <div style="padding: 1.5rem; background: var(--card); border: 1px solid var(--border); border-radius: var(--radius);">
                        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem;">ASCEND → Strategic Growth</h3>
                        <p style="color: var(--muted-foreground);">Turn inner work into external moves, career growth, real results</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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

