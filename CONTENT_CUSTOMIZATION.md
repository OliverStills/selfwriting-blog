# Content Customization Guide

## How to Change What Your Blog Writes About

**Good News:** No rebuilding needed! Edit PHP files, generate new posts, see changes immediately.

---

## 1. Change Post Topics

**File:** `src/PostGenerator.php`  
**Line:** 11-51

```php
private array $topics = [
    // Add your topics here
    'Your Topic Title',
    'Another Topic',
];
```

**Examples:**
- Mental health topics
- Career advice topics  
- Relationship topics
- Fitness/health topics
- Business/entrepreneurship topics
- Parenting topics

---

## 2. Change Target Audience

**File:** `src/PostGenerator.php`  
**Method:** `buildPrompt()` (line ~104)

### Current:
```php
You are writing for "Stuck in Adulthood" - a blog for ambitious men in their 20s and 30s who feel stuck...
```

### Example Changes:

**For Women:**
```php
You are writing for "Her Ascent" - a blog for ambitious women in their 20s and 30s...
```

**For Entrepreneurs:**
```php
You are writing for "Founder's Journey" - a blog for first-time entrepreneurs...
```

**For Parents:**
```php
You are writing for "Modern Parenthood" - a blog for parents navigating raising kids...
```

**For Creatives:**
```php
You are writing for "The Artist's Way Forward" - a blog for creatives monetizing their craft...
```

---

## 3. Change Writing Voice

**File:** `src/PostGenerator.php`  
**Section:** VOICE & STYLE in `buildPrompt()`

### Current Style:
- Robert Oliver's emotional temperature
- Stoic philosophy
- Direct, challenging tone

### To Change:
Edit the VOICE & STYLE section:

```php
VOICE & STYLE:
- Warm and supportive (like a best friend)
- Use storytelling and personal anecdotes
- Inspirational but practical
- Inclusive and empowering language
```

---

## 4. Change Content Framework

**File:** `src/PostGenerator.php`  
**Section:** THE FRAMEWORK in `buildPrompt()`

### Current: The Fifth State
- STILL → Mental Clarity
- GRIT → Daily Discipline
- REFLECTION → Purpose & Meaning
- ASCEND → Strategic Growth

### Custom Framework Example:

```php
THE FRAMEWORK:
- DISCOVER: Finding what lights you up
- BUILD: Creating systems that work
- SCALE: Growing without burning out
- SUSTAIN: Maintaining long-term success
```

---

## 5. Change Opening Scenarios

**File:** `src/PostGenerator.php`  
**Line:** ~105-121 in `buildPrompt()`

```php
$openingScenarios = [
    "Your custom scenarios...",
    "Another scenario...",
    "Make them specific to your niche...",
];
```

### Examples by Niche:

**Entrepreneurs:**
- "Your competitor just got funded. You're bootstrapping."
- "Launch day: 3 sales, 0 feedback."
- "Your 'advisor' just asked for 10% equity."

**Parents:**
- "It's 8 PM. The house is a mess. You haven't eaten."
- "Your kid just asked why you're always on your phone."
- "Another Pinterest-perfect mom post. You're barely surviving."

**Fitness:**
- "You missed the gym for the third time this week."
- "Your gym buddy just posted their transformation. You're still at week 1."
- "New year, same resolution, different excuse."

---

## 6. Change Book Recommendations Niche

**File:** `src/BookSelector.php`  
**Method:** `buildPrompt()` (line ~50-93)

```php
Recommend 3 real, published books that would help readers take action on this topic. Focus on books about:
- Your niche here
- Your topics here
- Your themes here
```

**Examples:**
- Business: *Zero to One, The Lean Startup, Traction*
- Fitness: *Atomic Habits, Can't Hurt Me, Bigger Leaner Stronger*
- Parenting: *How to Talk So Kids Will Listen, The Whole-Brain Child*

---

## 7. Change Site Name & Branding

**File:** `public/index.php` and `public/post.php`

Search and replace:
- "The Fifth State" → Your site name
- Update meta descriptions
- Update footer text

**Files to update:**
- `public/index.php` (lines 46-67, 169-177)
- `public/post.php` (lines 62-70, 150-158)

---

## 8. Test Your Changes

After making changes:

```bash
# Generate a test post
php cron/generate-post.php

# Or test without saving
php test-new-prompt.php
```

---

## Quick Start Templates

### Template 1: Women Entrepreneurs
1. Change topics to business/entrepreneurship
2. Change audience to "ambitious women building businesses"
3. Change framework to BUILD/SCALE/SUSTAIN
4. Change voice to "supportive but challenging"

### Template 2: Fitness/Health
1. Change topics to training, nutrition, mindset
2. Change audience to "men using fitness for transformation"
3. Keep Stoic philosophy (works great for fitness)
4. Add discipline/consistency focus

### Template 3: Creative Professionals
1. Change topics to creativity, monetization, consistency
2. Change audience to "artists and creators"
3. Change framework to CREATE/SHARE/GROW/SUSTAIN
4. Change voice to inspirational and community-focused

---

## Remember:

✅ **No rebuild needed** - just edit PHP files  
✅ **Changes are immediate** - next post uses new settings  
✅ **Test first** - use `test-new-prompt.php` to preview  
✅ **Regenerate if needed** - update old posts with new style  

---

## Files Reference

| What to Change | File | Section |
|----------------|------|---------|
| Topics | `src/PostGenerator.php` | `$topics` array (line 11) |
| Audience | `src/PostGenerator.php` | `buildPrompt()` method |
| Voice/Style | `src/PostGenerator.php` | VOICE & STYLE section |
| Framework | `src/PostGenerator.php` | THE FRAMEWORK section |
| Book Types | `src/BookSelector.php` | `buildPrompt()` method |
| Site Name | `public/index.php`, `public/post.php` | Header/footer |
| Opening Hooks | `src/PostGenerator.php` | `$openingScenarios` array |

Happy customizing! 🎨



