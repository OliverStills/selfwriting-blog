# Layrid Clone Setup Guide

## ✅ Changes Applied

### 1. **Background Images Added**
- Each card now has a large background image
- Dark overlay (65% opacity) for dimmed effect
- Using Unsplash placeholder images (replace with your own)

### 2. **Styling for Creative Card**
```css
.card-bg {
    position: absolute;
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
}

/* Dark overlay */
.card-bg::after {
    background: rgba(0, 0, 0, 0.65); /* Adjust 0.5-0.8 for brightness */
}
```

### 3. **Animations Added**
- Parallax scale effect on background (1.1x zoom on scroll)
- Fade-in animation for card content
- Smooth scrolling via Lenis

---

## 🖼️ How to Add Your Own Images

### Option 1: Use Local Images
1. Create an `images` folder in `Layrid-Clone/`
2. Add your images (e.g., `creative-bg.jpg`)
3. Update `style.css`:

```css
#card-1 .card-bg { 
    background-image: url('./images/creative-bg.jpg');
}
```

### Option 2: Use External URLs
Replace the Unsplash URLs in `style.css` lines 122-132:

```css
#card-1 .card-bg { 
    background-image: url('YOUR_IMAGE_URL_HERE');
}
```

### Option 3: Use Your Blog Video
You could even use the `fifth-state-video.mp4` as a background:

```html
<!-- In index.html, replace .card-bg div for #card-1 -->
<div class="card-bg">
    <video autoplay loop muted playsinline style="width:100%; height:100%; object-fit:cover;">
        <source src="../fifth-state-video.mp4" type="video/mp4">
    </video>
</div>
```

---

## 🎨 Customization Options

### Adjust Dimness
In `style.css`, change the overlay opacity:

```css
.card-bg::after {
    background: rgba(0, 0, 0, 0.5); /* Lighter */
    background: rgba(0, 0, 0, 0.7); /* Darker */
    background: rgba(0, 0, 0, 0.8); /* Very dark */
}
```

### Change Card Text Size
```css
.card-content h2 {
    font-size: 80px; /* Adjust to your liking */
}
```

### Adjust Parallax Speed
In `script.js`, change the scale value:

```javascript
gsap.to(bg, {
    scale: 1.1, // Change to 1.05 (subtle) or 1.2 (dramatic)
    scrollTrigger: { scrub: true }
});
```

---

## 🚀 Testing Locally

### Option 1: Simple HTTP Server (Python)
```bash
# In the Layrid-Clone folder
cd Layrid-Clone
python -m http.server 8001
```
Then visit: http://localhost:8001

### Option 2: PHP Server
```bash
cd Layrid-Clone
php -S localhost:8001
```

### Option 3: Live Server (VS Code Extension)
1. Install "Live Server" extension in VS Code
2. Right-click `index.html` → "Open with Live Server"

---

## 🔄 Switching Between Blog & Layrid Clone

### Current Setup:
- **Blog**: http://localhost:8000 (running in PHP server)
- **Layrid Clone**: http://localhost:8001 (needs separate server)

### To Switch:

**View Blog:**
```bash
# Already running on port 8000
# Just visit: http://localhost:8000
```

**View Layrid Clone:**
```bash
# Start new server in Layrid-Clone folder
cd Layrid-Clone
python -m http.server 8001
# Visit: http://localhost:8001
```

**Or use different ports:**
- Blog: 8000
- Layrid: 8001
- Keep both running simultaneously!

---

## 📁 File Structure

```
Self-Writing-Blog-Stuck-In-Adulthood/
├── public/               # Your blog
│   ├── index.php
│   ├── styles.css
│   └── fifth-state-video.mp4
│
├── Layrid-Clone/         # New Layrid style
│   ├── index.html       ✅ Updated
│   ├── style.css        ✅ Background images added
│   ├── script.js        ✅ Animations added
│   └── images/          (create this for local images)
```

---

## 🎯 Current Features

### Layrid Clone Has:
- ✅ Sticky card stacking effect
- ✅ Smooth scroll (Lenis)
- ✅ Large background images with dark overlay
- ✅ Centered text
- ✅ Parallax zoom effect
- ✅ Fade-in animations
- ✅ Responsive design

### Your Blog Has:
- ✅ AI-generated content
- ✅ Framework-based advice
- ✅ Video banner
- ✅ Dark theme
- ✅ Amazon affiliate links
- ✅ SQLite database

---

## 🔧 Quick Fixes

### If Images Don't Show:
1. Check browser console for errors (F12)
2. Verify image URLs are accessible
3. Check that `.card-bg` divs exist in HTML

### If Animations Don't Work:
1. Ensure GSAP and Lenis scripts are loading
2. Check browser console for errors
3. Try hard refresh (Ctrl+Shift+R)

### If Overlay is Too Dark/Light:
Adjust in `style.css`:
```css
.card-bg::after {
    background: rgba(0, 0, 0, 0.65); /* Change this value */
}
```

---

## 📸 Example Image Sources

### Free Stock Photos:
- **Unsplash**: https://unsplash.com
- **Pexels**: https://pexels.com
- **Pixabay**: https://pixabay.com

### Recommended Image Specs:
- **Size**: 1920x1080 or higher
- **Format**: JPG (smaller file size) or WebP (better compression)
- **Quality**: High quality for best results

---

## 🎨 Next Steps

1. **Test the current setup**: Start a server and view it
2. **Replace placeholder images**: Add your own images
3. **Adjust dimness**: Tweak the overlay opacity
4. **Customize colors**: Change accent colors in `:root`
5. **Add more cards**: Copy the sticky-card structure
6. **Integrate with blog**: Could merge styles eventually

---

## 🚨 Backup & Revert

### Current Blog is Safe!
Your blog files are untouched in the `public/` folder.

### To Revert Layrid Changes:
The Layrid clone is in its own folder, so no revert needed. Just:
- Keep working in `Layrid-Clone/` for this style
- Keep working in `public/` for your blog

### To Merge Later:
You could potentially:
1. Use Layrid style for homepage
2. Use blog style for content pages
3. Or keep them separate as different projects

---

## 📝 Current Placeholder Images

**Card 1 (Creative)**: Abstract gradient  
**Card 2 (Exploration)**: Mountains/nature  
**Card 3 (Growth)**: Mountain peak  

Replace these in `style.css` lines 122-132!

---

## Need Help?

- **Can't see images?** Check the URLs and browser console
- **Want different effect?** Adjust opacity/scale values
- **Need more cards?** Copy the HTML structure and add IDs
- **Want video instead?** Use the HTML5 video method above

Your Layrid clone is ready to test! 🚀




