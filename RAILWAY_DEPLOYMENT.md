# Deploying IWADCO to Railway.app

This guide will help you deploy your PHP application to Railway.app.

## 🎯 Why Railway?

- ✅ **Free tier available** (with $5 credit monthly)
- ✅ **Built-in MySQL/PostgreSQL** databases
- ✅ **Automatic HTTPS**
- ✅ **Simple deployment** from GitHub
- ✅ **Environment variables** management
- ✅ **Persistent file storage** (unlike Render)
- ✅ **No credit card required** for free tier

## 📋 Prerequisites

1. **GitHub Account** - Railway connects via GitHub
2. **Railway Account** - Sign up at [railway.app](https://railway.app) (free tier available)
3. **Your SQL file** - `iwadco2_db (1).sql`

## 🚀 Step-by-Step Deployment

### Step 1: Prepare Your Repository

1. **Push your code to GitHub** (if not already done):
   ```bash
   git init
   git add .
   git commit -m "Ready for Railway deployment"
   git remote add origin https://github.com/yourusername/iwadco.git
   git push -u origin main
   ```

2. **Your `db_connect.php` is already configured** to use environment variables, so it will work with Railway automatically! ✅

### Step 2: Create Railway Project

1. **Log into Railway**
   - Go to [railway.app](https://railway.app)
   - Sign up/login with GitHub

2. **Create New Project**
   - Click "New Project"
   - Select "Deploy from GitHub repo"
   - Choose your `iwadco` repository
   - Railway will detect it's a PHP project automatically

### Step 3: Add MySQL Database

1. **Add MySQL Service**
   - In your Railway project, click "+ New"
   - Select "Database" → "Add MySQL"
   - Railway will provision a MySQL database automatically
   - Wait for it to be ready (green status)

2. **Get Database Credentials**
   - Click on your MySQL service
   - Go to "Variables" tab
   - You'll see these variables (Railway generates them):
     - `MYSQLHOST` (database host)
     - `MYSQLPORT` (port, usually 3306)
     - `MYSQLDATABASE` (database name)
     - `MYSQLUSER` (username)
     - `MYSQLPASSWORD` (password)

### Step 4: Configure Web Service

1. **Configure PHP Service**
   - Railway should auto-detect PHP
   - Go to your web service → Settings
   - Set **Start Command**: `php -S 0.0.0.0:$PORT -t iwadco`
   - Set **Root Directory**: Leave empty (or `iwadco` if needed)

2. **Add Environment Variables**
   - Go to your web service → Variables tab
   - Click "New Variable"
   - Add these variables one by one (Railway provides them from MySQL service):
     ```
     DB_HOST=${{MySQL.MYSQLHOST}}
     DB_USER=${{MySQL.MYSQLUSER}}
     DB_PASS=${{MySQL.MYSQLPASSWORD}}
     DB_NAME=${{MySQL.MYSQLDATABASE}}
     DB_PORT=${{MySQL.MYSQLPORT}}
     ```
   - **Important**: Use `${{Service.Variable}}` syntax to reference MySQL service variables
   - This creates a connection between your web service and MySQL service

### Step 5: Import Your Database

1. **Connect to Railway MySQL**

   **Option A: Using Railway CLI (Recommended)**
   ```bash
   # Install Railway CLI
   npm i -g @railway/cli
   
   # Login to Railway
   railway login
   
   # Link to your project
   railway link
   
   # Connect to MySQL (this opens a MySQL shell)
   railway connect mysql
   
   # Or get connection string
   railway variables
   ```

   **Option B: Using MySQL Client**
   - Use MySQL Workbench, DBeaver, or any MySQL client
   - Get connection details from Railway MySQL service → Variables
   - Connect using those credentials

   **Option C: Using Railway's Web Interface**
   - Railway provides a web-based SQL editor
   - Go to MySQL service → Data tab
   - Copy and paste your SQL file contents

2. **Import SQL File**
   
   **Using Railway CLI:**
   ```bash
   # Get connection details
   railway variables
   
   # Import SQL file
   mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < "iwadco2_db (1).sql"
   ```
   
   **Using MySQL Client:**
   - Connect to Railway MySQL
   - Import `iwadco2_db (1).sql` file
   - Or copy/paste SQL content

3. **Fix Admin Email Typo**
   After import, run:
   ```sql
   UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';
   ```

### Step 6: Deploy

1. **Railway auto-deploys** when you push to GitHub
2. **Or manually deploy** from Railway dashboard
3. **Check deployment logs** for any errors
4. **Your app will be live** at: `https://your-app-name.up.railway.app`

### Step 7: Configure Custom Domain (Optional)

1. Go to your web service → Settings
2. Click "Generate Domain" (Railway provides free subdomain)
3. Or add custom domain under "Custom Domains"
4. Follow DNS configuration instructions

## 🔧 Configuration Files

### railway.json (Optional)

Railway auto-detects PHP, but you can create `railway.json` for custom config:

```json
{
  "$schema": "https://railway.app/railway.schema.json",
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "php -S 0.0.0.0:$PORT -t iwadco",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

This file is already created in your project! ✅

## ✅ Post-Deployment Checklist

- [ ] Application is accessible via Railway URL
- [ ] Database connection is working
- [ ] Login functionality works
- [ ] Admin panel is accessible
- [ ] File uploads work (check `uploads/` directory)
- [ ] Sessions are working
- [ ] All pages load correctly
- [ ] Custom domain configured (if applicable)

## 🐛 Troubleshooting

### Issue: "Database connection failed"
- **Solution**: 
  - Verify environment variables are set correctly
  - Check that you're using `${{MySQL.MYSQLHOST}}` syntax (not `$MYSQLHOST`)
  - Verify MySQL service is running (green status)
  - Check Railway logs for connection errors
  - Ensure MySQL service is in the same project

### Issue: "404 Not Found"
- **Solution**: 
  - Verify `index.php` exists in `iwadco/` directory
  - Check Start Command: `php -S 0.0.0.0:$PORT -t iwadco`
  - Verify Root Directory is set correctly (or leave empty)
  - Check Railway logs for routing errors

### Issue: "Sessions not working"
- **Solution**: 
  - Railway handles sessions automatically
  - Ensure `session_start()` is called in files
  - Check if sessions directory is writable
  - Verify session configuration

### Issue: "File uploads not working"
- **Solution**: 
  - Railway's filesystem is persistent (unlike Render)
  - Files should persist across deploys
  - Check file permissions
  - Verify `uploads/` directory exists and is writable
  - Check Railway logs for permission errors

### Issue: "Application shows blank page"
- **Solution**: 
  - Check Railway logs for PHP errors
  - Enable error reporting in development
  - Verify all includes are correct
  - Check if `db_connect.php` is loading correctly

### Issue: "Environment variables not working"
- **Solution**: 
  - Verify you're using `${{MySQL.Variable}}` syntax
  - Check that MySQL service name matches (case-sensitive)
  - Ensure variables are in the web service, not MySQL service
  - Redeploy after adding variables

## 📊 Railway Free Tier

- **$5 credit monthly** (free tier)
- **512 MB RAM** per service
- **Persistent storage** (files persist!)
- **Unlimited deploys**
- **Automatic HTTPS**
- **No sleep** (always on with credit)
- **No credit card required**

## 🔐 Security Best Practices

1. **Never commit credentials** to Git
2. **Use environment variables** for all sensitive data
3. **Enable HTTPS** (automatic on Railway)
4. **Use strong database passwords** (Railway generates these automatically)
5. **Regular backups** of your database (Railway provides backups)
6. **Keep dependencies updated**

## 💰 Cost Considerations

- **Free Tier**: $5 credit/month (usually enough for small apps)
- **Hobby Plan**: $5/month + usage
- **Pro Plan**: $20/month + usage
- **Monitor usage** in Railway dashboard to stay within limits

## 📚 Additional Resources

- [Railway PHP Documentation](https://docs.railway.app/languages/php)
- [Railway MySQL Documentation](https://docs.railway.app/databases/mysql)
- [Railway Environment Variables](https://docs.railway.app/develop/variables)
- [Railway CLI Documentation](https://docs.railway.app/develop/cli)

## 🆘 Need Help?

- Check Railway logs in dashboard
- Review deployment logs
- Contact Railway support (very responsive!)
- Check application logs for specific errors
- Railway Discord community

---

**Ready to deploy?** Follow the steps above and your application will be live on Railway! 🚂
