<?php

// Handle non-www to www redirect
$host = $_SERVER['HTTP_HOST'] ?? '';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

// If accessing without www and not localhost, redirect to www
if (!str_starts_with($host, 'www.') && !str_contains($host, 'localhost') && !str_contains($host, '127.0.0.1') && !str_contains($host, 'railway.app')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $redirectUrl = $protocol . '://www.' . $host . $uri;
    header('Location: ' . $redirectUrl, true, 301);
    exit;
}

require_once __DIR__ . '/../src/config.php';

use App\Database;

$db = new Database();
$posts = $db->getAllPosts(50);

function formatDate($datetime) {
    return strtoupper(date('M j, Y', strtotime($datetime)));
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
    <title>The Fifth State - For Men Who Refuse to Stay Stuck</title>
    <meta name="description" content="Evidence-based frameworks for ambitious men feeling stuck, behind, or emotionally numb. Stoic wisdom meets tactical action.">
    <meta property="og:title" content="The Fifth State">
    <meta property="og:description" content="For men who refuse to stay stuck. Stoic wisdom meets tactical action.">
    <meta property="og:image" content="/fifth-state-skull-banner.png">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <header class="header">
        <div class="logo">The Fifth State</div>
        <div class="menu-btn">Menu</div>
    </header>

    <main>
        <!-- Hero Section with Video Banner -->
        <section class="hero">
            <div class="hero-bg">
                <video autoplay loop muted playsinline class="hero-video">
                    <source src="fifth-state-video.mp4" type="video/mp4">
                </video>
                <div class="overlay"></div>
            </div>
            <div class="hero-content">
                <h1>The Fifth State</h1>
                <p>For men who refuse to stay stuck</p>
            </div>
            
            <div class="hero-details">
                <span>Evidence-Based</span>
                <span>Framework-Driven</span>
                <span>Stoic Wisdom</span>
            </div>
        </section>

        <!-- Overview Section -->
        <section class="overview">
            <div class="grid-container">
                <div class="col-title">
                    <h2>The Framework</h2>
                </div>
                <div class="col-text">
                    <p class="en">A system for ambitious men in their 20s and 30s who feel stuck, behind in life, or emotionally numb but want to evolve. Grounded in Stoicism, psychology, and behavioral science.</p>
                </div>
            </div>
        </section>

        <!-- Phase Cards - Sticky Stacking Effect -->
        <div class="stack-wrapper">
            
            <!-- STILL Phase Card -->
            <section class="sticky-card phase-card" id="card-still">
                <div class="card-bg">
                    <img src="https://images.unsplash.com/photo-1557683316-973673baf926?w=1920&q=80" alt="STILL Phase">
                </div>
                <div class="card-content">
                    <span class="phase-label">Phase 01</span>
                    <h2>STILL</h2>
                    <p class="phase-desc">Mental Clarity & Stabilization</p>
                    <p class="phase-explanation">Get through today without making it worse. Calm the noise, name what's happening, stop the spiral.</p>
                </div>
            </section>

            <!-- GRIT Phase Card -->
            <section class="sticky-card phase-card" id="card-grit">
                <div class="card-bg">
                    <img src="https://images.unsplash.com/photo-1519681393784-d120267933ba?w=1920&q=80" alt="GRIT Phase">
                </div>
                <div class="card-content">
                    <span class="phase-label">Phase 02</span>
                    <h2>GRIT</h2>
                    <p class="phase-desc">Daily Discipline & Execution</p>
                    <p class="phase-explanation">Do the thing X days in a row. Show up to the practice even when it sucks. Action before feeling.</p>
                </div>
            </section>

            <!-- REFLECTION Phase Card -->
            <section class="sticky-card phase-card" id="card-reflection">
                <div class="card-bg">
                    <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&q=80" alt="REFLECTION Phase">
                </div>
                <div class="card-content">
                    <span class="phase-label">Phase 03</span>
                    <h2>REFLECTION</h2>
                    <p class="phase-desc">Purpose & Narrative Clarity</p>
                    <p class="phase-explanation">Make sense of your story. Understand why you're here and what matters to you. No big moves yet—just clarity.</p>
                </div>
            </section>

            <!-- ASCEND Phase Card -->
            <section class="sticky-card phase-card" id="card-ascend">
                <div class="card-bg">
                    <img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1920&q=80" alt="ASCEND Phase">
                </div>
                <div class="card-content">
                    <span class="phase-label">Phase 04</span>
                    <h2>ASCEND</h2>
                    <p class="phase-desc">Strategic Growth & Building</p>
                    <p class="phase-explanation">Execute on the clarity you've built. Turn inner work into external results. Build momentum.</p>
                </div>
            </section>

        </div>

        <!-- Blog Posts Section -->
        <section class="posts-section">
            <div class="section-header">
                <h2>Latest Insights</h2>
                <p>Framework-based advice for real problems</p>
            </div>

            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <h3>First Post Coming Soon</h3>
                    <p>Check back soon for evidence-based insights.</p>
                </div>
            <?php else: ?>
                <div class="posts-grid">
                    <?php foreach ($posts as $post): ?>
                        <article class="post-card-layrid">
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="post-link">
                                <div class="post-image">
                                    <?php if (!empty($post['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($post['image_url']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1557683316-973673baf926?w=800&q=80" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    <?php endif; ?>
                                    <div class="post-image-overlay"></div>
                                </div>
                                <div class="post-card-content">
                                    <div class="post-meta">
                                        <span class="post-date"><?php echo formatDate($post['created_at']); ?></span>
                                        <span class="post-category"><?php echo getPostCategory($post['title']); ?></span>
                                    </div>
                                    <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <p class="post-excerpt"><?php echo $post['excerpt']; ?></p>
                                    <span class="read-more-link">Read Article →</span>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Footer -->
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>The Fifth State</h3>
                    <p>For men who refuse to stay stuck.</p>
                </div>
                <div class="footer-info">
                    <p>© <?php echo date('Y'); ?> The Fifth State</p>
                    <p>Content powered by Claude AI</p>
                    <p>Images generated by Nano Banana (Google Imagen)</p>
                    <p>Amazon links are affiliate links</p>
                </div>
            </div>
        </footer>

    </main>

    <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.29/bundled/lenis.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="script.js"></script>
</body>
</html>

