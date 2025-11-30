<?php

require_once __DIR__ . '/../src/config.php';

use App\Database;

$db = new Database();
$posts = $db->getAllPosts(50, true);

// Organize posts by phase
$stillPosts = [];
$disciplinePosts = [];
$reflectionPosts = [];
$ascendPosts = [];

function getPostPhase($title) {
    $stillKeywords = ['brain', 'anxiety', 'overthinking', 'rejection', 'worth', 'time', 'panic', 'numbness', 'anhedonia', 'codependency', 'identity', 'comparison', 'peers', 'autopilot', 'shell', 'cutoff', 'nervous'];
    $disciplineKeywords = ['motivated', 'discipline', 'consistency', 'habits', 'addiction', 'porn', 'weed', 'fumbling', 'interview', 'habit', 'loop', 'burnout', 'hard mode', 'behavioral', 'activation', 'envelope', 'exposure', 'dopamine', 'detox'];
    $reflectionKeywords = ['behind', 'purpose', 'bloomer', 'adult', 'learning', 'social', 'skills', 'awkward', 'authoring', 'story', 'foreclosure', 'mask', 'lost years', 'narrative', 'growth mindset', 'values', 'numb'];
    $ascendKeywords = ['career', 'skills', 'compound', 'leverage', 'pivot', 'bet', 'yourself', 'responsibilities', 'freeze', 'flow', 'regulation', 'second act', 'strategic', 'building'];
    
    $lowerTitle = strtolower($title);
    
    foreach ($stillKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'STILL';
    }
    foreach ($disciplineKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'DISCIPLINE';
    }
    foreach ($reflectionKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'REFLECTION';
    }
    foreach ($ascendKeywords as $keyword) {
        if (str_contains($lowerTitle, $keyword)) return 'ASCEND';
    }
    
    return 'STILL';
}

// Organize posts
foreach ($posts as $post) {
    $phase = getPostPhase($post['title']);
    switch ($phase) {
        case 'STILL': $stillPosts[] = $post; break;
        case 'DISCIPLINE': $disciplinePosts[] = $post; break;
        case 'REFLECTION': $reflectionPosts[] = $post; break;
        case 'ASCEND': $ascendPosts[] = $post; break;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>The Fifth State - For Men Who Refuse to Stay Stuck</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Space Grotesk', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    animation: {
                        'fade-up': 'fadeUp 0.8s ease-out forwards',
                        'bounce-x': 'bounceX 1s infinite',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        bounceX: {
                            '0%, 100%': { transform: 'translateX(-25%)', animationTimingFunction: 'cubic-bezier(0.8,0,1,1)' },
                            '50%': { transform: 'none', animationTimingFunction: 'cubic-bezier(0,0,0.2,1)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Hide scrollbars completely for clean look */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        body { background-color: #050505; color: white; }

        .glass-panel {
            background: rgba(20, 20, 20, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Video Hero Section */
        .video-hero {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 1000;
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }

        .video-hero.hidden {
            opacity: 0;
            transform: translateY(-50px);
            pointer-events: none;
        }

        /* Slides container smooth entry */
        main {
            opacity: 0;
            transform: translateY(50px);
            transition: opacity 0.8s ease-in, transform 0.8s ease-in;
        }

        main.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .video-hero video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .video-content {
            position: relative;
            z-index: 2;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
        }

        .video-hero h1 {
            font-size: clamp(40px, 8vw, 100px);
            font-weight: 600;
            line-height: 0.9;
            margin-bottom: 20px;
            text-transform: uppercase;
            color: #ffffff;
        }

        .video-hero p {
            font-size: 24px;
            opacity: 0.9;
            color: #ffffff;
        }

        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            color: white;
            cursor: pointer;
            transition: opacity 0.3s;
        }

        .scroll-indicator:hover {
            opacity: 0.7;
        }

        .scroll-indicator span {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.7;
        }
    </style>
</head>
<body class="overflow-hidden">

    <!-- Video Hero Banner -->
    <div id="video-hero" class="video-hero">
        <video autoplay loop muted playsinline>
            <source src="fifth-in-city.mp4" type="video/mp4">
        </video>
        <div class="video-overlay"></div>
        <div class="video-content">
            <h1 class="font-display">The Fifth State</h1>
            <p>Beyond emotion. Beyond reaction. Beyond thought. Beyond action. There is Awareness.</p>
        </div>
        <div class="scroll-indicator" onclick="enterSlides()">
            <span class="font-mono">Scroll to Explore</span>
            <iconify-icon icon="solar:arrow-down-bold-duotone" class="text-2xl animate-bounce"></iconify-icon>
        </div>
    </div>

    <main class="flex flex-row w-screen h-screen overflow-x-auto snap-x snap-mandatory no-scrollbar scroll-smooth">

        <!-- STILL Phase Section -->
        <section class="w-screen h-screen shrink-0 overflow-y-auto snap-y snap-mandatory no-scrollbar relative">
            
            <!-- Phase 1 Header Card -->
            <div class="snap-start w-full h-screen flex items-center justify-center p-4">
                <div class="aspect-[3/4] glass-panel flex flex-col w-full max-w-xl relative shadow-2xl overflow-hidden group rounded-3xl">
                    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1518837695005-2083093ee35b?w=1920&q=80')] bg-cover bg-center opacity-40"></div>
                    <div class="absolute inset-0 bg-gradient-to-b from-black/20 to-black/80"></div>
                    
                    <div class="relative z-10 h-full flex flex-col justify-between p-12">
                        <div class="flex justify-between items-center opacity-70 animate-fade-up">
                            <span class="text-xs uppercase tracking-widest font-mono text-neutral-300">Phase 1</span>
                            <span class="text-xs font-mono text-neutral-400">Mental Clarity</span>
                        </div>
                        <div class="space-y-4">
                            <h1 class="text-7xl font-display font-semibold tracking-tighter text-white animate-fade-up">STILL</h1>
                            <p class="text-neutral-300 font-mono text-sm border-l-2 border-blue-500 pl-4 animate-fade-up">
                                "Calm the noise. Get through today without making it worse."
                            </p>
                        </div>
                        <div class="flex justify-between items-end animate-fade-up">
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollDownInSection(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">View Posts</span>
                                <iconify-icon icon="solar:arrow-down-bold-duotone" class="text-2xl text-white animate-bounce"></iconify-icon>
                            </div>
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollToNextPhase(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">Next Phase</span>
                                <iconify-icon icon="solar:arrow-right-bold-duotone" class="text-2xl text-white animate-bounce-x"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php foreach ($stillPosts as $index => $post): ?>
            <!-- Post Card -->
            <div class="snap-start w-full h-screen flex items-center justify-center p-4">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="aspect-[3/4] bg-[#0A0A0A] border border-white/10 flex flex-col w-full max-w-xl relative shadow-2xl overflow-hidden hover:border-white/30 transition-colors rounded-3xl">
                    <div class="h-[50%] bg-cover bg-center relative" style="background-image: url('<?php echo !empty($post['image_url']) ? htmlspecialchars($post['image_url']) : 'https://images.unsplash.com/photo-1557683316-973673baf926?w=1000&q=80'; ?>');"></div>
                    <div class="flex-grow p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-mono text-blue-400 uppercase">STILL</span>
                                <span class="text-xs font-mono text-neutral-600"><?php echo strtoupper(date('M j, Y', strtotime($post['created_at']))); ?></span>
                            </div>
                            <h2 class="text-4xl font-display font-bold text-white tracking-tighter mb-4"><?php echo htmlspecialchars($post['title']); ?></h2>
                            <p class="text-neutral-400 text-sm leading-relaxed"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        </div>
                        <div class="flex justify-between items-end border-t border-white/10 pt-6">
                            <span class="text-xs text-neutral-500">Read Article</span>
                            <iconify-icon icon="solar:arrow-right-up-bold-duotone" class="text-2xl text-white"></iconify-icon>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>

        </section>


        <!-- DISCIPLINE Phase Section -->
        <section class="w-screen h-screen shrink-0 overflow-y-auto snap-y snap-mandatory no-scrollbar relative">
            
            <!-- Phase 2 Header Card -->
            <div class="snap-start w-full h-screen flex items-center justify-center p-4">
                <div class="aspect-[3/4] glass-panel flex flex-col w-full max-w-xl relative shadow-2xl overflow-hidden group rounded-3xl">
                    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1920&q=80')] bg-cover bg-center opacity-30 grayscale"></div>
                    
                    <div class="relative z-10 h-full flex flex-col justify-between p-12">
                        <div class="flex justify-between items-center opacity-70">
                            <span class="text-xs uppercase tracking-widest font-mono text-neutral-300">Phase 2</span>
                            <span class="text-xs font-mono text-neutral-400">Daily Discipline</span>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 border border-white/20 rounded-full flex items-center justify-center mb-8 mx-auto backdrop-blur-md">
                                <iconify-icon icon="solar:graph-up-bold-duotone" class="text-3xl text-white"></iconify-icon>
                            </div>
                            <h2 class="text-6xl font-display font-bold tracking-tighter text-white mb-4">DISCIPLINE</h2>
                            <p class="text-neutral-300 font-mono text-sm">
                                "Do the thing X days in a row. Action before feeling."
                            </p>
                        </div>
                        <div class="flex justify-between items-end">
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollToPreviousPhase(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">Previous Phase</span>
                                <iconify-icon icon="solar:arrow-left-bold-duotone" class="text-2xl text-white animate-bounce-x"></iconify-icon>
                            </div>
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollDownInSection(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">View Posts</span>
                                <iconify-icon icon="solar:arrow-down-bold-duotone" class="text-2xl text-white animate-bounce"></iconify-icon>
                            </div>
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollToNextPhase(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">Next Phase</span>
                                <iconify-icon icon="solar:arrow-right-bold-duotone" class="text-2xl text-white animate-bounce-x"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php foreach ($disciplinePosts as $index => $post): ?>
            <!-- Post Card -->
            <div class="snap-start w-full h-screen flex items-center justify-center p-4">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="aspect-[3/4] bg-[#0A0A0A] border border-white/10 flex flex-col w-full max-w-xl relative shadow-2xl overflow-hidden hover:border-white/30 transition-colors rounded-3xl">
                    <div class="h-[50%] bg-cover bg-center relative" style="background-image: url('<?php echo !empty($post['image_url']) ? htmlspecialchars($post['image_url']) : 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1000&q=80'; ?>');"></div>
                    <div class="flex-grow p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-mono text-red-400 uppercase">DISCIPLINE</span>
                                <span class="text-xs font-mono text-neutral-600"><?php echo strtoupper(date('M j, Y', strtotime($post['created_at']))); ?></span>
                            </div>
                            <h2 class="text-4xl font-display font-bold text-white tracking-tighter mb-4"><?php echo htmlspecialchars($post['title']); ?></h2>
                            <p class="text-neutral-400 text-sm leading-relaxed"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        </div>
                        <div class="flex justify-between items-end border-t border-white/10 pt-6">
                            <span class="text-xs text-neutral-500">Read Article</span>
                            <iconify-icon icon="solar:arrow-right-up-bold-duotone" class="text-2xl text-white"></iconify-icon>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>

        </section>


        <!-- REFLECTION Phase Section -->
        <section class="w-screen h-screen shrink-0 overflow-y-auto snap-y snap-mandatory no-scrollbar relative">
            
            <!-- Phase 3 Header Card -->
            <div class="snap-start w-full h-screen flex items-center justify-center p-4">
                <div class="aspect-[3/4] glass-panel flex flex-col w-full max-w-xl relative shadow-2xl overflow-hidden group rounded-3xl">
                    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1507415492521-917f60c93bfe?w=1920&q=80')] bg-cover bg-center opacity-50 mix-blend-overlay"></div>
                    <div class="absolute inset-0 bg-black/60"></div>
                    <div class="relative z-10 h-full flex flex-col p-12 justify-between">
                        <div class="flex justify-between items-center opacity-70">
                            <span class="text-xs uppercase tracking-widest font-mono text-neutral-300">Phase 3</span>
                            <span class="text-xs font-mono text-neutral-400">Purpose & Meaning</span>
                        </div>
                        <div class="space-y-4">
                            <h2 class="text-7xl font-display font-bold tracking-tighter text-white">REFLECTION</h2>
                            <p class="text-neutral-300 font-mono text-sm border-l-2 border-purple-500 pl-4">
                                "Make sense of your story. Understand why you're here."
                            </p>
                        </div>
                        <div class="flex justify-between items-end">
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollToPreviousPhase(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">Previous Phase</span>
                                <iconify-icon icon="solar:arrow-left-bold-duotone" class="text-2xl text-white animate-bounce-x"></iconify-icon>
                            </div>
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollDownInSection(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">View Posts</span>
                                <iconify-icon icon="solar:arrow-down-bold-duotone" class="text-2xl text-white animate-bounce"></iconify-icon>
                            </div>
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollToNextPhase(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">Next Phase</span>
                                <iconify-icon icon="solar:arrow-right-bold-duotone" class="text-2xl text-white animate-bounce-x"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php foreach ($reflectionPosts as $index => $post): ?>
            <!-- Post Card -->
            <div class="snap-start w-full h-screen flex items-center justify-center p-4">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="aspect-[3/4] bg-[#0A0A0A] border border-white/10 flex flex-col w-full max-w-xl relative shadow-2xl overflow-hidden hover:border-white/30 transition-colors rounded-3xl">
                    <div class="h-[50%] bg-cover bg-center relative" style="background-image: url('<?php echo !empty($post['image_url']) ? htmlspecialchars($post['image_url']) : 'https://images.unsplash.com/photo-1499209974431-9dddcece7f88?w=1000&q=80'; ?>');"></div>
                    <div class="flex-grow p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-mono text-purple-400 uppercase">REFLECTION</span>
                                <span class="text-xs font-mono text-neutral-600"><?php echo strtoupper(date('M j, Y', strtotime($post['created_at']))); ?></span>
                            </div>
                            <h2 class="text-4xl font-display font-bold text-white tracking-tighter mb-4"><?php echo htmlspecialchars($post['title']); ?></h2>
                            <p class="text-neutral-400 text-sm leading-relaxed"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        </div>
                        <div class="flex justify-between items-end border-t border-white/10 pt-6">
                            <span class="text-xs text-neutral-500">Read Article</span>
                            <iconify-icon icon="solar:arrow-right-up-bold-duotone" class="text-2xl text-white"></iconify-icon>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>

        </section>


        <!-- ASCEND Phase Section -->
        <section class="w-screen h-screen shrink-0 overflow-y-auto snap-y snap-mandatory no-scrollbar relative">
            
            <!-- Phase 4 Header Card -->
            <div class="snap-start w-full h-screen flex items-center justify-center p-4">
                <div class="aspect-[3/4] glass-panel flex flex-col w-full max-w-xl relative shadow-2xl overflow-hidden group rounded-3xl">
                    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1920&q=80')] bg-cover bg-center opacity-40"></div>
                    <div class="relative z-10 h-full flex flex-col p-12 justify-between">
                        <div class="flex justify-between items-center opacity-70">
                            <span class="text-xs uppercase tracking-widest font-mono text-neutral-300">Phase 4</span>
                            <span class="text-xs font-mono text-neutral-400">Strategic Growth</span>
                        </div>
                        <div class="space-y-4">
                            <h2 class="text-7xl font-display font-bold tracking-tighter text-white">ASCEND</h2>
                            <p class="text-neutral-300 font-mono text-sm border-l-2 border-orange-500 pl-4">
                                "Execute on clarity. Turn inner work into external results."
                            </p>
                        </div>
                        <div class="flex justify-between items-end">
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollToPreviousPhase(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">Previous Phase</span>
                                <iconify-icon icon="solar:arrow-left-bold-duotone" class="text-2xl text-white animate-bounce-x"></iconify-icon>
                            </div>
                            <div class="flex flex-col items-center gap-1 cursor-pointer hover:opacity-70 transition-opacity" onclick="scrollDownInSection(this)">
                                <span class="text-[10px] uppercase font-mono text-neutral-500">View Posts</span>
                                <iconify-icon icon="solar:arrow-down-bold-duotone" class="text-2xl text-white animate-bounce"></iconify-icon>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php foreach ($ascendPosts as $index => $post): ?>
            <!-- Post Card -->
            <div class="snap-start w-full h-screen flex items-center justify-center p-4">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="aspect-[3/4] bg-[#0A0A0A] border border-white/10 flex flex-col w-full max-w-xl relative shadow-2xl overflow-hidden hover:border-white/30 transition-colors rounded-3xl">
                    <div class="h-[50%] bg-cover bg-center relative" style="background-image: url('<?php echo !empty($post['image_url']) ? htmlspecialchars($post['image_url']) : 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1000&q=80'; ?>');"></div>
                    <div class="flex-grow p-10 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-4">
                                <span class="text-xs font-mono text-orange-400 uppercase">ASCEND</span>
                                <span class="text-xs font-mono text-neutral-600"><?php echo strtoupper(date('M j, Y', strtotime($post['created_at']))); ?></span>
                            </div>
                            <h2 class="text-4xl font-display font-bold text-white tracking-tighter mb-4"><?php echo htmlspecialchars($post['title']); ?></h2>
                            <p class="text-neutral-400 text-sm leading-relaxed"><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        </div>
                        <div class="flex justify-between items-end border-t border-white/10 pt-6">
                            <span class="text-xs text-neutral-500">Read Article</span>
                            <iconify-icon icon="solar:arrow-right-up-bold-duotone" class="text-2xl text-white"></iconify-icon>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>

        </section>

    </main>

    <script>
        // Video hero transition
        let isTransitioning = false;
        let hasEnteredSlides = false;

        function enterSlides() {
            if (isTransitioning || hasEnteredSlides) return;
            isTransitioning = true;
            hasEnteredSlides = true;

            const videoHero = document.getElementById('video-hero');
            const slidesMain = document.querySelector('main');
            
            // Fade out video with transform
            videoHero.classList.add('hidden');
            
            // Wait a bit then fade in slides
            setTimeout(() => {
                slidesMain.classList.add('visible');
            }, 300);

            setTimeout(() => {
                videoHero.style.display = 'none';
                isTransitioning = false;
            }, 800);
        }

        // Listen for scroll/swipe to enter slides
        function handleScroll(e) {
            if (!hasEnteredSlides && e.deltaY > 0) {
                enterSlides();
            }
        }

        window.addEventListener('wheel', handleScroll, { passive: true });
        window.addEventListener('touchstart', function(e) {
            const touch = e.touches[0];
            window.touchStartY = touch.clientY;
        });

        window.addEventListener('touchmove', function(e) {
            if (!window.touchStartY || hasEnteredSlides) return;
            const touch = e.touches[0];
            const deltaY = window.touchStartY - touch.clientY;
            if (deltaY > 50) {
                enterSlides();
            }
        });

        // Track scroll positions for return navigation
        let scrollPositions = {
            mainScrollLeft: 0,
            sectionScrollTop: 0,
            fromPostId: null
        };

        // Save scroll position before navigating to post
        document.querySelectorAll('a[href^="post.php"]').forEach(link => {
            link.addEventListener('click', function(e) {
                const main = document.querySelector('main');
                const currentSection = this.closest('section');
                
                scrollPositions.mainScrollLeft = main.scrollLeft;
                scrollPositions.sectionScrollTop = currentSection ? currentSection.scrollTop : 0;
                scrollPositions.fromPostId = new URL(this.href).searchParams.get('id');
                
                // Save to sessionStorage for persistence
                sessionStorage.setItem('scrollPositions', JSON.stringify(scrollPositions));
            });
        });

        // Scroll down within current section (view posts)
        function scrollDownInSection(element) {
            const section = element.closest('section');
            section.scrollBy({ 
                top: window.innerHeight, 
                behavior: 'smooth' 
            });
        }

        // Scroll to next phase (right)
        function scrollToNextPhase(element) {
            const currentSection = element.closest('section');
            const nextSection = currentSection.nextElementSibling;
            
            if (nextSection && nextSection.tagName === 'SECTION') {
                nextSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start', 
                    inline: 'start' 
                });
            }
        }

        // Scroll to previous phase (left)
        function scrollToPreviousPhase(element) {
            const currentSection = element.closest('section');
            const prevSection = currentSection.previousElementSibling;
            
            if (prevSection && prevSection.tagName === 'SECTION') {
                prevSection.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start', 
                    inline: 'start' 
                });
            }
        }

        // Restore scroll position on page load (if returning from post)
        window.addEventListener('load', function() {
            const saved = sessionStorage.getItem('scrollPositions');
            if (saved) {
                const positions = JSON.parse(saved);
                const main = document.querySelector('main');
                
                // Small delay to ensure page is fully rendered
                setTimeout(() => {
                    // Restore horizontal scroll (which section)
                    main.scrollLeft = positions.mainScrollLeft;
                    
                    // Find the active section and restore vertical scroll
                    setTimeout(() => {
                        const sections = main.querySelectorAll('section');
                        const mainCenter = main.scrollLeft + (main.offsetWidth / 2);
                        
                        sections.forEach(section => {
                            const sectionLeft = section.offsetLeft;
                            const sectionRight = sectionLeft + section.offsetWidth;
                            
                            if (mainCenter >= sectionLeft && mainCenter <= sectionRight) {
                                section.scrollTop = positions.sectionScrollTop;
                            }
                        });
                        
                        // Clear after restoration
                        sessionStorage.removeItem('scrollPositions');
                    }, 100);
                }, 100);
            }
        });
    </script>

</body>
</html>
