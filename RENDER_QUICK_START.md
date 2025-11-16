# Quick Start: Deploy to Render

## 🚀 Fast Track (5 Steps)

### 1. Push to GitHub
```bash
git init
git add .
git commit -m "Ready for Render deployment"
git remote add origin https://github.com/yourusername/iwadco.git
git push -u origin main
```

### 2. Set Up MySQL Database

**Option A: PlanetScale (Free)**
1. Sign up at [planetscale.com](https://planetscale.com)
2. Create database
3. Import `iwadco2_db (1).sql`
4. Get connection string

**Option B: Other MySQL Host**
- Use any MySQL hosting service
- Import your SQL file
- Get credentials

### 3. Create Render Web Service

1. Go to [dashboard.render.com](https://dashboard.render.com)
2. Click "New +" → "Web Service"
3. Connect GitHub repository
4. Configure:
   - **Name**: `iwadco`
   - **Environment**: `PHP`
   - **Build Command**: (leave empty)
   - **Start Command**: `php -S 0.0.0.0:$PORT -t iwadco`
   - **Root Directory**: (leave empty)

### 4. Add Environment Variables

In Render dashboard, add these:
```
DB_HOST=your-mysql-host.com
DB_USER=your-username
DB_PASS=your-password
DB_NAME=iwadco2_db
DB_PORT=3306
```

### 5. Deploy!

Click "Create Web Service" and wait for deployment.

## ✅ Done!

Your app will be live at: `https://your-app-name.onrender.com`

## 🔧 Quick Fixes

**Database not connecting?**
- Check environment variables are set correctly
- Verify MySQL host allows external connections
- Check credentials

**404 errors?**
- Verify `index.php` exists in `iwadco/` folder
- Check Start Command: `php -S 0.0.0.0:$PORT -t iwadco`

**Need more details?** See `RENDER_DEPLOYMENT.md`

