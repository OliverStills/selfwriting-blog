# Quick Start Guide

Get your self-writing blog for men who feel stuck running in 5 minutes!

## 1. Install Dependencies

```bash
composer install
```

## 2. Configure Environment

```bash
# Copy example file
copy env.example .env

# Edit .env with your credentials:
# - CLAUDE_API_KEY (required)
# - AMAZON_AFFILIATE_ID (optional for testing)
# - CRON_SECRET (any random string for testing)
# - ENVIRONMENT=testing
# - CRON_INTERVAL=5
```

## 3. Test Your Setup

```bash
php test-setup.php
```

This will verify:
- PHP version (8.2+)
- Composer dependencies
- Environment configuration
- Database setup
- Claude API connection

## 4. Generate First Post

```bash
php cron/generate-post.php
```

This will:
- Create a new blog post for ambitious men feeling stuck
- Follow The Fifth State framework (Still, Grit, Reflection, Ascend)
- Select 3 relevant self-development books
- Save everything to the database

## 5. Start Local Server

```bash
cd public
php -S localhost:8000
```

Then open: http://localhost:8000

## Testing Mode

In testing mode:
- Posts generate every 5 minutes
- Run `php cron/generate-post.php` manually OR
- Call the web endpoint: `http://localhost:8000/cron-trigger.php?secret=YOUR_SECRET`
- Check `database/cron.log` for generation logs

**Testing the web endpoint locally**:
```bash
# Using curl (Mac/Linux)
curl "http://localhost:8000/cron-trigger.php?secret=your_secret_here"

# Using PowerShell (Windows)
Invoke-WebRequest "http://localhost:8000/cron-trigger.php?secret=your_secret_here"

# Or just visit in browser:
http://localhost:8000/cron-trigger.php?secret=your_secret_here
```

You should see a JSON response like:
```json
{
  "success": true,
  "action": "generated",
  "message": "Post generated successfully",
  "post_id": 1,
  "post_title": "Why Your Brain Won't Shut Up at 2AM",
  "books_count": 3,
  "timestamp": "2025-11-24 18:30:00"
}
```

## Deploy to Railway

1. **Push code to GitHub**
   ```bash
   git add .
   git commit -m "Initial commit"
   git push origin main
   ```

2. **Create Railway project**
   - Go to [Railway.app](https://railway.app)
   - "New Project" → "Deploy from GitHub"
   - Select your repository

3. **Add environment variables**
   ```
   CLAUDE_API_KEY=sk-ant-your-key-here
   AMAZON_AFFILIATE_ID=your-id
   CRON_SECRET=random-secret-string
   ENVIRONMENT=production
   CRON_INTERVAL=1440
   ```

4. **Add volume for database**
   - Mount Path: `/app/database`
   - Size: 1GB

5. **Set up cron-job.org**
   - Sign up at [cron-job.org](https://cron-job.org)
   - Create cron job
   - URL: `https://your-app.up.railway.app/cron-trigger.php?secret=YOUR_CRON_SECRET`
   - Schedule: Every 24 hours
   - Test with "Run now"

See README.md for detailed deployment instructions.

## Troubleshooting

**"Call to undefined function"**
- Run `composer install`

**"Database connection failed"**
- Check that `database/` directory exists and is writable

**"Claude API request failed"**
- Verify API key is correct
- Check you have API credits

**No posts showing**
- Run `php cron/generate-post.php` manually
- Check `database/cron.log` for errors

## Common Commands

```bash
# Test setup
php test-setup.php

# Generate post manually
php cron/generate-post.php

# Start dev server
php -S localhost:8000 -t public

# View logs
type database\cron.log      # Windows
cat database/cron.log       # Mac/Linux

# Reset database (delete all posts)
del database\blog.db        # Windows
rm database/blog.db         # Mac/Linux
```

## Next Steps

- Customize topics in `src/PostGenerator.php`
- Adjust design in `public/styles.css`
- Modify prompts for different AI behavior
- Deploy to Railway for automatic posting

Happy blogging! 🎵

