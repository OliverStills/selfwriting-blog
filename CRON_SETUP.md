# Cron Job Setup with cron-job.org

This guide shows you how to set up automated post generation using cron-job.org, a free external cron service.

## Why cron-job.org?

- ✅ **Free** - No credit card required
- ✅ **Reliable** - Monitors job execution and alerts you on failures
- ✅ **Simple** - Just provide a URL and schedule
- ✅ **Web-based** - No server access needed
- ✅ **Flexible** - Easy to change schedule or pause

## Step-by-Step Setup

### 1. Prepare Your Environment

Make sure your Railway deployment has the `CRON_SECRET` environment variable set:

```
CRON_SECRET=abc123xyz456
```

**How to generate a secure secret:**

```bash
# Option 1: Use online generator
# Visit: https://www.random.org/strings/

# Option 2: Use openssl (Mac/Linux)
openssl rand -hex 32

# Option 3: Use PowerShell (Windows)
-join ((48..57) + (65..90) + (97..122) | Get-Random -Count 32 | % {[char]$_})

# Option 4: Just make one up
# Example: mySecretKey2024BlogCron
```

### 2. Sign Up for cron-job.org

1. Go to [cron-job.org](https://cron-job.org)
2. Click "Sign up" (top right)
3. Enter email and password
4. Verify your email
5. Log in

### 3. Create Your Cron Job

1. **Click "Cronjobs" in left sidebar**

2. **Click "Create cronjob" button**

3. **Fill in the form**:

   **Title**: `Stuck in Adulthood - Post Generator`
   
   **URL**: 
   ```
   https://your-railway-app.up.railway.app/cron-trigger.php?secret=YOUR_CRON_SECRET
   ```
   
   Replace:
   - `your-railway-app` with your actual Railway URL
   - `YOUR_CRON_SECRET` with your actual secret from Railway
   
   **Schedule**:
   - For **testing**: Every 5 minutes
     - Pattern: `*/5 * * * *`
   - For **production**: Every 24 hours
     - Pattern: `0 0 * * *` (midnight UTC)
     - Or: `0 12 * * *` (noon UTC)
   
   **Advanced settings**:
   - Request method: `GET`
   - Request timeout: 60 seconds
   - Failed execution: Send email notification
   - Logs: Keep last 10 executions

4. **Click "Create cronjob"**

### 4. Test the Cron Job

1. **Find your new cron job** in the list

2. **Click the play button** (▶) to "Execute now"

3. **Check the response**:
   - Status should be `200 OK`
   - Response should contain JSON like:
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

4. **Visit your blog** to see the new post!

### 5. Monitor Execution

**View execution history**:
- Click on your cron job in the list
- See "Last executions" tab
- Each execution shows:
  - Timestamp
  - HTTP status code
  - Response time
  - Response body

**Common responses**:

```json
// Success - Post generated
{
  "success": true,
  "action": "generated",
  "message": "Post generated successfully"
}

// Success - But skipped (not time yet)
{
  "success": true,
  "action": "skipped",
  "message": "Not time to generate yet",
  "next_post_in_minutes": 120.5
}

// Error - Invalid secret
{
  "success": false,
  "error": "Invalid or missing secret token"
}

// Error - Generation failed
{
  "success": false,
  "action": "error",
  "error": "Claude API request failed: ..."
}
```

## Troubleshooting

### "Invalid or missing secret token" (403)

❌ **Problem**: The `secret` parameter doesn't match your `CRON_SECRET`

✅ **Solution**: 
- Check Railway environment variables
- Make sure URL has `?secret=YOUR_SECRET`
- No typos in the secret

### "CRON_SECRET not configured" (500)

❌ **Problem**: `CRON_SECRET` not set in Railway

✅ **Solution**: 
- Go to Railway → Your service → Variables
- Add `CRON_SECRET` with a random string
- Redeploy if needed

### Cron job runs but no post appears

❌ **Problem**: Might be skipped due to interval

✅ **Solution**: 
- Check the JSON response for `"action": "skipped"`
- Look at `next_post_in_minutes` in response
- If you want to force generate, temporarily set `CRON_INTERVAL=1` in Railway

### Timeout errors

❌ **Problem**: Claude API taking too long

✅ **Solution**: 
- Increase timeout in cron-job.org settings to 120 seconds
- Check Claude API status
- Verify API key has credits

## Testing Locally

Before setting up cron-job.org, test the endpoint locally:

```bash
# Start your local server
php -S localhost:8000 -t public

# In another terminal, trigger the endpoint:

# Mac/Linux (curl)
curl "http://localhost:8000/cron-trigger.php?secret=your_secret_here"

# Windows (PowerShell)
Invoke-WebRequest "http://localhost:8000/cron-trigger.php?secret=your_secret_here"

# Or just open in browser:
# http://localhost:8000/cron-trigger.php?secret=your_secret_here
```

## Changing Schedule

**From testing (5 min) to production (24 hours)**:

1. Go to cron-job.org
2. Click on your cron job
3. Click "Edit"
4. Change schedule pattern:
   - From: `*/5 * * * *`
   - To: `0 0 * * *` (midnight) or `0 12 * * *` (noon)
5. Update `CRON_INTERVAL` in Railway to `1440`
6. Save

## Pausing/Resuming

**To pause**:
- Go to cron-job.org
- Toggle the switch next to your cron job to OFF
- Posts will stop generating

**To resume**:
- Toggle back to ON

## Security Best Practices

1. **Keep CRON_SECRET private** - Don't commit it to Git
2. **Use a strong secret** - At least 20 random characters
3. **Rotate periodically** - Change the secret every few months
4. **Monitor logs** - Check for unauthorized access attempts
5. **Enable notifications** - Get alerted on failures

## Alternative: Using Uptime Monitoring

If you want redundancy, you can also use:
- [UptimeRobot](https://uptimerobot.com) - Free monitoring + cron
- [Cronitor](https://cronitor.io) - Advanced monitoring
- [EasyCron](https://www.easycron.com) - Another cron service

Setup is similar - just provide your cron-trigger.php URL with the secret.

---

**Questions?** Check the main README.md or review the execution logs in cron-job.org.


