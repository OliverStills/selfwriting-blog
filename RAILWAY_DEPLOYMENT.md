# 🚀 Railway Deployment Guide

## Configuration Complete ✅

Your code is now properly configured for Railway deployment with:
- ✅ `railway.toml` - Railway configuration
- ✅ `nixpacks.toml` - PHP build configuration  
- ✅ `Procfile` - Start command (backup)
- ✅ `config.php` - Environment variable support
- ✅ `config.local.php` - Local development (gitignored)

---

## 📋 Pre-Deployment Checklist

### 1. **GitHub Repository**
Your code should already be on GitHub:
- Repository: https://github.com/OliverStills/selfwriting-blog
- All files committed (except sensitive data)
- `.gitignore` properly configured

### 2. **Required Environment Variables**
You'll need these API keys ready:
- ✅ CLAUDE_API_KEY (from Anthropic)
- ✅ NANO_BANANA_API_KEY (from Google)
- ✅ AMAZON_AFFILIATE_ID (from Amazon Associates)
- ✅ CRON_SECRET (generate random string)

---

## 🚂 Railway Deployment Steps

### Step 1: Create New Project

1. Go to [Railway.app](https://railway.app)
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Choose repository: `OliverStills/selfwriting-blog`
5. Railway will auto-detect PHP and start building

### Step 2: Configure Environment Variables

In Railway Dashboard → Variables tab, add:

```env
# Claude AI (for content generation)
CLAUDE_API_KEY=your_claude_api_key_here

# Nano Banana / Google Imagen (for image generation)
NANO_BANANA_API_KEY=AIzaSyD0tYbHJhSineNg0OLp4XSMIM7lAZQXrUE

# Amazon Affiliate
AMAZON_AFFILIATE_ID=thefifthstate-20

# Security
CRON_SECRET=generate_random_32_char_string

# Environment
ENVIRONMENT=production
CRON_INTERVAL=1440
```

**Generate CRON_SECRET:**
```bash
# Mac/Linux
openssl rand -hex 32

# Or use: https://www.random.org/strings/
```

### Step 3: Add Persistent Storage (Volumes)

Railway → Service → Settings → Volumes

**Volume 1: Database**
- Mount Path: `/app/database`
- Size: 1 GB
- Purpose: SQLite database persistence

**Volume 2: Generated Images**
- Mount Path: `/app/public/images`
- Size: 2 GB  
- Purpose: AI-generated post images

**Important:** Without volumes, your database and images will reset on every deploy!

### Step 4: Verify Build Configuration

Railway should detect these files automatically:
- `railway.toml` - Main config
- `nixpacks.toml` - PHP setup
- `Procfile` - Start command

**Expected Build Process:**
1. Install PHP 8.2
2. Run `composer install`
3. Create directories
4. Start server on port $PORT

### Step 5: Deploy!

1. Railway will auto-deploy on first push
2. Watch build logs in Railway dashboard
3. Once deployed, Railway assigns a URL like:
   `https://your-app.up.railway.app`

---

## 🔍 Verify Deployment

### Check 1: Homepage Loads
Visit your Railway URL:
- ✅ Video banner appears
- ✅ Phase cards (STILL, GRIT, REFLECTION, ASCEND)
- ✅ Blog posts grid (if any exist)

### Check 2: Generate First Post
Trigger post generation:
```
https://your-app.up.railway.app/public/cron-trigger.php?secret=YOUR_CRON_SECRET
```

Expected response:
```json
{
  "success": true,
  "action": "generated",
  "message": "Post generated successfully",
  "post_id": 1
}
```

### Check 3: View Generated Post
Visit homepage - should see new post with:
- ✅ Title
- ✅ AI-generated image
- ✅ Excerpt
- ✅ Phase badge (STILL/GRIT/REFLECTION/ASCEND)

---

## ⚙️ Configuration Details

### Railway.toml Explained

```toml
[build]
builder = "NIXPACKS"  # Uses Nixpacks for PHP detection

[deploy]
startCommand = "php -S 0.0.0.0:$PORT -t Layrid-Clone"
# Serves from Layrid-Clone folder
# Uses Railway's $PORT environment variable
# 0.0.0.0 allows external connections

healthcheckPath = "/"
# Railway pings / to check if app is running

restartPolicyType = "ON_FAILURE"
# Auto-restart if app crashes
```

### Nixpacks.toml Explained

```toml
[phases.setup]
nixPkgs = ["php82", "php82Packages.composer"]
# Installs PHP 8.2 and Composer

[phases.install]
cmds = ["composer install --no-dev --optimize-autoloader"]
# Installs dependencies, optimized for production

[phases.build]
cmds = [
  "mkdir -p database",
  "mkdir -p public/images/posts",
  "chmod -R 755 database",
  "chmod -R 755 public/images"
]
# Creates necessary directories with correct permissions

[start]
cmd = "php -S 0.0.0.0:$PORT -t Layrid-Clone"
# Start command
```

### Config.php Explained

```php
// Checks for config.local.php (local dev)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
    return; // Use local config
}

// Otherwise uses Railway environment variables
define('CLAUDE_API_KEY', getenv('CLAUDE_API_KEY') ?: $_ENV['CLAUDE_API_KEY'] ?? '');
```

This means:
- **Local:** Uses `config.local.php` (your actual keys)
- **Railway:** Uses environment variables from Railway dashboard

---

## 🤖 Automated Post Generation

### Option 1: Cron-job.org (Recommended)

1. Sign up at [cron-job.org](https://cron-job.org) (free)
2. Create new cron job:
   - **Title:** The Fifth State - Post Generator
   - **URL:** `https://your-app.up.railway.app/public/cron-trigger.php?secret=YOUR_CRON_SECRET`
   - **Schedule:** Every 24 hours
   - **Method:** GET
3. Save and enable
4. Test with "Run now" button

### Option 2: Railway Cron (If Available)

Check if Railway offers cron jobs in your plan:
```bash
# Railway CLI
railway run php cron/generate-post.php
```

### Option 3: GitHub Actions (Alternative)

Create `.github/workflows/generate-post.yml`:
```yaml
name: Generate Daily Post
on:
  schedule:
    - cron: '0 12 * * *'  # Daily at noon UTC

jobs:
  generate:
    runs-on: ubuntu-latest
    steps:
      - name: Trigger post generation
        run: |
          curl "https://your-app.up.railway.app/public/cron-trigger.php?secret=${{ secrets.CRON_SECRET }}"
```

---

## 🐛 Troubleshooting

### Build Fails

**Error:** "Composer install failed"
- **Fix:** Check `composer.json` is valid
- **Fix:** Ensure all dependencies compatible with PHP 8.2

**Error:** "Permission denied"
- **Fix:** Add volumes for `/app/database` and `/app/public/images`

### App Crashes After Deploy

**Check Railway Logs:**
```
Railway Dashboard → Deployments → View Logs
```

**Common Issues:**
1. Missing environment variables
2. Database directory not writable (add volume)
3. Port binding incorrect (should use $PORT)

### Images Not Generating

**Check API Key:**
- Verify `NANO_BANANA_API_KEY` in Railway variables
- Test API key locally first

**Check Logs:**
```bash
# View in Railway logs or check database/cron.log
```

**Test Manually:**
```bash
# SSH into Railway container (if available)
railway run php generate-post-images.php
```

### Database Resets on Deploy

**Problem:** Database deleted after each deploy

**Fix:** Add volume for `/app/database`
- Railway → Volumes → Add Volume
- Mount path: `/app/database`
- Size: 1 GB

---

## 📊 Monitoring

### Railway Dashboard

**Metrics to Watch:**
- CPU usage
- Memory usage
- Request count
- Error rate

**Set Alerts:**
- High memory usage (>80%)
- Frequent crashes
- Slow response times

### Logs

**View Logs:**
```
Railway Dashboard → Deployments → Logs
```

**Look for:**
- ✅ "Listening on 0.0.0.0:$PORT"
- ✅ "Post generated successfully"
- ❌ "Database connection failed"
- ❌ "Image generation failed"

---

## 💰 Cost Estimate

### Railway
- **Free Tier:** $5 credit/month (hobby plan)
- **Paid:** ~$5-10/month depending on usage

### APIs
- **Claude:** ~$0.01-0.03 per post
- **Nano Banana:** ~$0.01-0.05 per image
- **Total:** ~$1-2/month for daily posts

### Estimated Total
**$6-12/month** for production deployment with daily posts

---

## 🔒 Security Best Practices

### Environment Variables
✅ Never commit API keys to GitHub  
✅ Use Railway's environment variables  
✅ Rotate keys periodically  
✅ Use different keys for dev/prod  

### CRON_SECRET
✅ Generate strong random string  
✅ Keep it private  
✅ Change if exposed  
✅ Don't share in public repos  

### Database
✅ Regular backups (download from Railway volume)  
✅ Not publicly accessible (good!)  
✅ Monitor for unusual activity  

---

## 🚀 Post-Deployment Checklist

### Immediately After Deploy
- [ ] Visit Railway URL - homepage loads
- [ ] Check video banner plays
- [ ] Verify phase cards display
- [ ] Generate test post via cron trigger
- [ ] Verify post appears on homepage
- [ ] Click into post - full page loads
- [ ] Check image generated properly
- [ ] Test book Amazon links work

### Within First Week
- [ ] Set up automated cron job
- [ ] Monitor Railway logs daily
- [ ] Check database size
- [ ] Verify images saving correctly
- [ ] Test on mobile devices
- [ ] Set up custom domain (optional)

### Ongoing
- [ ] Weekly database backups
- [ ] Monitor API usage/costs
- [ ] Check Railway resource usage
- [ ] Update content topics as needed
- [ ] Review generated content quality

---

## 📚 Additional Resources

### Railway Documentation
- [Railway Docs](https://docs.railway.com)
- [PHP on Railway](https://docs.railway.com/guides/php)
- [Volumes Guide](https://docs.railway.com/reference/volumes)

### Your Project Files
- `LAYRID_DEPLOYMENT_GUIDE.md` - Design deployment
- `FRAMEWORK_ANALYSIS.md` - Content frameworks
- `CONTENT_CUSTOMIZATION.md` - Customize content

---

## ✅ You're Ready to Deploy!

### Quick Commands:

**Test Locally:**
```bash
cd Layrid-Clone
php -S localhost:8000
```

**Push to GitHub:**
```bash
git add .
git commit -m "Ready for Railway deployment"
git push origin main
```

**Deploy on Railway:**
1. Connect GitHub repo
2. Add environment variables
3. Add volumes
4. Deploy automatically!

**Your production URL:**
`https://your-app.up.railway.app`

---

**Questions or Issues?**
- Check Railway logs
- Review troubleshooting section
- Test locally first
- Verify all environment variables set

**Happy deploying!** 🚀



