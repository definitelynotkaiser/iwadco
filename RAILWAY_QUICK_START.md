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

**Settings:**
- Start Command: `php -S 0.0.0.0:$PORT -t iwadco`
- Root Directory: `iwadco` (if needed)

### 5. Import Database & Deploy

**Import SQL:**
- Use Railway CLI or MySQL client
- Import `iwadco2_db (1).sql`
- Fix admin email: `UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';`

**Deploy:**
- Railway auto-deploys on git push
- Or deploy manually from dashboard

## ✅ Done!

Your app: `https://your-app-name.up.railway.app`

## 🔧 Quick Fixes

**Database not connecting?**
- Check environment variables use `${{MySQL.Variable}}` syntax
- Verify MySQL service is running
- Check Railway logs

**404 errors?**
- Verify Start Command: `php -S 0.0.0.0:$PORT -t iwadco`
- Check `index.php` exists in `iwadco/` folder

**Need details?** See `RAILWAY_DEPLOYMENT.md`

