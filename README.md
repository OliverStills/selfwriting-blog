# The Fifth State - Self-Writing Blog for Men Who Feel Stuck

An automated blog system that uses Claude AI to generate daily posts for ambitious men in their 20s and 30s who feel stuck, behind in life, or directionless but want to evolve. The blog automatically creates content, selects relevant book recommendations with Amazon affiliate links, and features a modern dark theme design.

## Features

- 🤖 **Fully Automated Content Generation** - Claude Sonnet 4 writes complete blog posts with unique voice
- 📚 **Smart Book Recommendations** - AI selects 3 relevant self-development books per post
- 🎨 **Modern Dark Theme Design** - Inspired by Vence template with dark mode aesthetic
- 💪 **The Fifth State Framework** - Content organized around Still, Grit, Reflection, Ascend
- ⚡ **SQLite Database** - Lightweight, no separate database server needed
- 🔄 **Flexible Scheduling** - 5-minute intervals for testing, 24-hour for production
- 🚀 **Railway Ready** - Easy deployment with included configuration

## Technology Stack

- **PHP 8.2+** - Modern PHP with type declarations
- **SQLite** - Lightweight, embedded database
- **Claude API** - Content generation via Anthropic's Claude
- **Composer** - PHP dependency management
- **Railway** - Deployment platform

## Project Structure

```
├── public/
│   ├── index.php           # Main blog feed
│   ├── post.php            # Individual post view
│   ├── styles.css          # Minimal CSS
│   └── .htaccess           # Apache configuration
├── src/
│   ├── Database.php        # SQLite connection & queries
│   ├── PostGenerator.php   # Claude API integration
│   ├── BookSelector.php    # Book recommendation logic
│   └── config.php          # Configuration settings
├── cron/
│   └── generate-post.php   # Automated post generation
├── database/
│   └── blog.db             # SQLite database (auto-created)
├── composer.json           # PHP dependencies
├── railway.json            # Railway configuration
├── nixpacks.toml           # Nixpacks build config
└── env.example             # Environment variables template
```

## Local Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Claude API key ([Get one here](https://console.anthropic.com/))
- Amazon Affiliate ID (optional for testing)

### Installation Steps

1. **Clone the repository**
   ```bash
   git clone <your-repo-url>
   cd Self-Writing-Blog-Stuck-In-Adulthood
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Configure environment variables**
   ```bash
   # Copy the example file
   cp env.example .env
   
   # Edit .env with your credentials
   # (Use your preferred text editor)
   ```

4. **Edit .env file**
   ```env
   CLAUDE_API_KEY=sk-ant-your-actual-key-here
   AMAZON_AFFILIATE_ID=your-affiliate-id
   ENVIRONMENT=testing
   CRON_INTERVAL=5
   ```

5. **Initialize the database**
   The database will be automatically created on first run. To manually initialize:
   ```bash
   php -r "require 'vendor/autoload.php'; require 'src/config.php'; new App\Database();"
   ```

6. **Start the local development server**
   ```bash
   cd public
   php -S localhost:8000
   ```

7. **Open in browser**
   Navigate to `http://localhost:8000`

## Testing Locally

### Generate Your First Post

Run the cron script manually to generate your first post:

```bash
php cron/generate-post.php
```

You should see output like:
```
[2025-11-24 18:15:00] === Starting post generation ===
[2025-11-24 18:15:00] No posts found. Generating first post.
[2025-11-24 18:15:00] Calling PostGenerator...
[2025-11-24 18:15:15] Post generated successfully: The Circle of Fifths: A Visual Guide...
[2025-11-24 18:15:15] Post saved with ID: 1
[2025-11-24 18:15:15] Calling BookSelector...
[2025-11-24 18:15:20] Saved 3 book recommendations
[2025-11-24 18:15:20] === Post generation complete ===
```

### Testing Automated Generation

In testing mode (ENVIRONMENT=testing, CRON_INTERVAL=5), the system generates a new post every 5 minutes.

**Option 1: Manual testing**
```bash
# Run every 5 minutes manually
php cron/generate-post.php
```

**Option 2: Simulate cron locally (Unix/Mac)**
```bash
# Add to crontab
*/5 * * * * cd /path/to/project && php cron/generate-post.php
```

**Option 3: Simulate cron locally (Windows)**
```powershell
# Create a scheduled task that runs every 5 minutes
# Task Scheduler > Create Basic Task > Set trigger and action
```

### View Logs

Check the generation logs:
```bash
# View cron logs
cat database/cron.log

# Or on Windows
type database\cron.log
```

## Deployment to Railway

### Step 1: Prepare for Deployment

1. **Commit your code to GitHub**
   ```bash
   git add .
   git commit -m "Initial commit: Self-writing blog"
   git push origin main
   ```

2. **Switch to production mode**
   - Set ENVIRONMENT=production in Railway
   - Set CRON_INTERVAL=1440 (24 hours)

### Step 2: Deploy to Railway

1. **Create a new Railway project**
   - Go to [Railway](https://railway.app)
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Choose your repository

2. **Add environment variables**
   In Railway dashboard, add these variables:
   ```
   CLAUDE_API_KEY=sk-ant-your-key-here
   AMAZON_AFFILIATE_ID=your-affiliate-id
   CRON_SECRET=generate-a-random-string-here
   ENVIRONMENT=production
   CRON_INTERVAL=1440
   ```
   
   **Generate a secure CRON_SECRET**: Use a random string generator like [random.org/strings](https://www.random.org/strings/) or run:
   ```bash
   # On Mac/Linux
   openssl rand -hex 32
   
   # Or just make up a long random string
   ```

3. **Add a volume for the database**
   - In Railway dashboard, go to your service
   - Click "Variables" tab
   - Add a volume mount:
     - Mount Path: `/app/database`
     - Size: 1GB (should be plenty)

4. **Deploy**
   Railway will automatically:
   - Detect PHP
   - Run composer install
   - Start the web server
   - Assign a public URL

### Step 3: Set Up Cron Job with cron-job.org

The blog uses [cron-job.org](https://cron-job.org) (a free external service) to trigger automated posts.

1. **Get your CRON_SECRET**
   - This is in your Railway environment variables
   - It's the random string you set earlier (e.g., `abc123xyz456`)

2. **Create a cron-job.org account**
   - Go to [cron-job.org](https://cron-job.org)
   - Sign up for free (no credit card required)

3. **Create a new cron job**
   - Click "Create Cron Job"
   - Title: `Stuck in Adulthood - Post Generator`
   - URL: `https://your-railway-url.up.railway.app/cron-trigger.php?secret=YOUR_CRON_SECRET`
   - Schedule: Every 24 hours (or your preferred interval)
   - Save and enable

4. **Test the trigger**
   - Click "Run now" in cron-job.org
   - Check the response shows `{"success":true,"action":"generated"...}`
   - Visit your blog to see the new post!

**Security Note**: The `CRON_SECRET` prevents unauthorized people from triggering post generation. Keep it private!

### Step 4: Verify Deployment

1. Visit your Railway URL
2. Wait for the cron to run (or trigger manually via cron service)
3. Refresh to see new posts

## Configuration

### Environment Variables

| Variable | Description | Default | Testing | Production |
|----------|-------------|---------|---------|------------|
| `CLAUDE_API_KEY` | Anthropic API key | Required | Required | Required |
| `AMAZON_AFFILIATE_ID` | Amazon affiliate ID | Optional | Optional | Required |
| `CRON_SECRET` | Secret for cron-job.org | Required | Required | Required |
| `ENVIRONMENT` | Environment mode | production | testing | production |
| `CRON_INTERVAL` | Minutes between posts | 1440 | 5 | 1440 |

### Customization

**Change posting frequency:**
- Edit `CRON_INTERVAL` in .env
- Adjust your cron schedule accordingly

**Modify blog topics:**
- Edit the `$topics` array in `src/PostGenerator.php`
- Topics are organized into four phases: Still, Grit, Reflection, Ascend
- Add your own topics following the framework

**Customize design:**
- Edit `public/styles.css`
- Modify CSS variables at the top of the file

**Change AI behavior:**
- Edit prompts in `src/PostGenerator.php` and `src/BookSelector.php`
- Adjust `max_tokens` for longer/shorter posts

## Database Schema

The SQLite database includes three tables:

**posts**
- `id` - Unique post ID
- `title` - Post title
- `content` - Full HTML content
- `excerpt` - Short excerpt for feed
- `created_at` - Publication timestamp
- `published` - Visibility flag

**books**
- `id` - Unique book ID
- `post_id` - Related post
- `title` - Book title
- `author` - Book author
- `amazon_link` - Affiliate link
- `relevance_note` - Why this book is relevant

**generation_log**
- `id` - Log entry ID
- `attempted_at` - When generation was attempted
- `success` - Whether it succeeded
- `error_message` - Error details if failed

## Troubleshooting

### "Database connection failed"
- Check that the `database` directory exists and is writable
- On Railway, ensure the volume is properly mounted

### "Claude API request failed"
- Verify your API key is correct
- Check you have API credits remaining
- Ensure you have network connectivity

### "No posts appearing"
- Run `php cron/generate-post.php` manually
- Check `database/cron.log` for errors
- Verify database was created: `ls database/`

### Posts not generating automatically
- Verify your cron job is running
- Check cron service logs
- Ensure CRON_INTERVAL is set correctly

### Railway deployment fails
- Check build logs in Railway dashboard
- Ensure composer.json is valid
- Verify PHP version compatibility

## Cost Considerations

### Development/Testing
- Claude API: ~$0.01-0.03 per post (including books)
- 5-minute intervals = ~288 posts/day (not recommended for real testing!)
- Suggest: Run manually or use longer intervals for testing

### Production
- Claude API: ~$0.01-0.03 per post
- 1 post/day = ~$0.30-0.90/month
- Railway: Free tier or ~$5/month
- **Total: ~$5-6/month**

## License

This project is open source. Feel free to use, modify, and distribute as needed.

## Credits

- Design inspired by [Visualize Value](https://visualizevalue.com)
- Voice influences: [Robert Oliver](https://www.instagram.com/robthebank/), [Dan Koe](https://thedankoe.com), Daily Stoic
- Framework: The Fifth State (Still → Grit → Reflection → Ascend)
- Powered by [Claude AI](https://anthropic.com)
- Deployed on [Railway](https://railway.app)

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review Railway logs
3. Check Claude API status
4. Create an issue in this repository

---

**Happy auto-blogging!** 🎵✨

