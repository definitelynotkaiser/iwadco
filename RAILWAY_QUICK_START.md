# Quick Start: Deploy to Railway

## 🚀 Fast Track (5 Steps)

### 1. Push to GitHub
```bash
git init
git add .
git commit -m "Ready for Railway"
git remote add origin https://github.com/yourusername/iwadco.git
git push -u origin main
```

### 2. Create Railway Project

1. Go to [railway.app](https://railway.app)
2. Sign up/login with GitHub
3. Click "New Project" → "Deploy from GitHub repo"
4. Select your `iwadco` repository

### 3. Add MySQL Database

1. In Railway project, click "+ New"
2. Select "Database" → "Add MySQL"
3. Railway creates database automatically

### 4. Configure Environment Variables

In your **web service** → Variables tab, add:
```
DB_HOST=${{MySQL.MYSQLHOST}}
DB_USER=${{MySQL.MYSQLUSER}}
DB_PASS=${{MySQL.MYSQLPASSWORD}}
DB_NAME=${{MySQL.MYSQLDATABASE}}
DB_PORT=${{MySQL.MYSQLPORT}}
```

**Settings (in web service → Settings tab):**
- Start Command: `php -S 0.0.0.0:$PORT -t iwadco`
- Root Directory: Leave empty (or `iwadco` if needed)
- Railway auto-detects PHP, but verify Start Command is set

### 5. Import Database & Deploy

**Import SQL (Choose one method):**

**Method A: Railway CLI (Easiest)**
```bash
# Install Railway CLI
npm i -g @railway/cli

# Login and link to your project
railway login
railway link

# Get database credentials
railway variables

# Import SQL file (replace with your actual file path)
mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < "iwadco2_db (1).sql"
```

**Method B: MySQL Client (MySQL Workbench/DBeaver)**
1. Get connection details from Railway MySQL service → Variables tab
2. Connect using those credentials
3. Import `iwadco2_db (1).sql` file
4. Run: `UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';`

**Method C: Railway Web Interface**
1. Go to MySQL service → Data tab
2. Copy contents of `iwadco2_db (1).sql`
3. Paste and execute in SQL editor
4. Fix admin email: `UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';`

**Deploy:**
- Railway auto-deploys on git push
- Or deploy manually from dashboard
- Check logs to ensure everything works

## ✅ Done!

Your app: `https://your-app-name.up.railway.app`

**Verify it works:**
- Visit your app URL
- Try logging in with admin account
- Check that database connection is working

## 🔧 Quick Fixes

**Database not connecting?**
- Check environment variables use `${{MySQL.Variable}}` syntax
- Verify MySQL service is running
- Check Railway logs

**404 errors?**
- Verify Start Command: `php -S 0.0.0.0:$PORT -t iwadco`
- Check `index.php` exists in `iwadco/` folder

**Need details?** See `RAILWAY_DEPLOYMENT.md`

