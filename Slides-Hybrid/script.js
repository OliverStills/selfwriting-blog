// Initialize Lenis for Smooth Scrolling
const lenis = new Lenis({
    duration: 1.2,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
    direction: 'vertical',
    gestureDirection: 'vertical',
    smooth: true,
    mouseMultiplier: 1,
    smoothTouch: false,
    touchMultiplier: 2,
});

// Animation Loop for Lenis
function raf(time) {
    lenis.raf(time);
    requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

// Optional: GSAP ScrollTrigger Integration
// This connects the smooth scroll to any scroll-based animations you might add later
gsap.registerPlugin(ScrollTrigger);

// Example: Fade in text in the overview section
gsap.from(".col-text p", {
    y: 50,
    opacity: 0,
    duration: 1,
    scrollTrigger: {
        trigger: ".overview",
        start: "top 70%",
        end: "bottom top",
        toggleActions: "play none none reverse"
    }
});

// Card Background Parallax Effect
document.querySelectorAll('.sticky-card').forEach((card) => {
    const bg = card.querySelector('.card-bg');
    
    gsap.to(bg, {
        scale: 1.1,
        scrollTrigger: {
            trigger: card,
            start: "top bottom",
            end: "bottom top",
            scrub: true,
        }
    });
});

// Card Content Fade In
document.querySelectorAll('.card-content').forEach((content) => {
    gsap.from(content, {
        y: 50,
        opacity: 0,
        duration: 1,
        scrollTrigger: {
            trigger: content,
            start: "top 80%",
            toggleActions: "play none none reverse"
        }
    });
});