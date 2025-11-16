# Deploying IWADCO to Render

This guide will help you deploy your PHP application to Render.com.

## ⚠️ Important: Database Considerations

**Render provides PostgreSQL by default, but your application uses MySQL.** You have two options:

### Option 1: Use External MySQL Database (Recommended)
- **PlanetScale** (free tier available) - Serverless MySQL
- **AWS RDS** - Managed MySQL
- **DigitalOcean Managed Database** - MySQL
- **Any MySQL hosting service**

### Option 2: Convert to PostgreSQL (Advanced)
- Requires converting your SQL schema
- More complex but uses Render's native database

**This guide assumes Option 1 (External MySQL).**

## 📋 Prerequisites

1. **GitHub Account** - Render connects via GitHub
2. **Render Account** - Sign up at [render.com](https://render.com) (free tier available)
3. **MySQL Database** - External MySQL service (see options above)
4. **Your SQL file** - `iwadco2_db (1).sql`

## 🚀 Step-by-Step Deployment

### Step 1: Prepare Your Repository

1. **Push your code to GitHub** (if not already done):
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/yourusername/iwadco.git
   git push -u origin main
   ```

2. **Update `db_connect.php` for Render**:
   - Option A: Use the provided `db_connect_render.php` and update all includes
   - Option B: Update `db_connect.php` to use environment variables (see below)

### Step 2: Set Up External MySQL Database

#### Using PlanetScale (Recommended - Free Tier)

1. Sign up at [planetscale.com](https://planetscale.com)
2. Create a new database
3. Import your SQL file:
   - Use PlanetScale CLI or
   - Use their web interface to import
4. Note down connection details:
   - Host
   - Username
   - Password
   - Database name
   - Port (usually 3306)

#### Using Other MySQL Providers

Follow your provider's instructions to:
- Create database
- Import `iwadco2_db (1).sql`
- Get connection credentials

### Step 3: Deploy to Render

1. **Log into Render Dashboard**
   - Go to [dashboard.render.com](https://dashboard.render.com)

2. **Create New Web Service**
   - Click "New +" → "Web Service"
   - Connect your GitHub repository
   - Select your `iwadco` repository

3. **Configure Service Settings**
   - **Name**: `iwadco` (or your preferred name)
   - **Environment**: `PHP`
   - **Build Command**: Leave empty (or `composer install` if you use Composer)
   - **Start Command**: `php -S 0.0.0.0:$PORT -t iwadco`
   - **Root Directory**: Leave empty (or `iwadco` if your files are in a subdirectory)

4. **Set Environment Variables**
   Click "Advanced" → "Add Environment Variable" and add:
   ```
   DB_HOST=your-mysql-host.com
   DB_USER=your-username
   DB_PASS=your-password
   DB_NAME=iwadco2_db
   DB_PORT=3306
   APP_ENV=production
   ```

5. **Update Database Connection File**
   
   You need to update your `db_connect.php` to use environment variables:
   
   ```php
   <?php
   // Get database credentials from environment variables
   $host = getenv('DB_HOST') ?: 'localhost';
   $user = getenv('DB_USER') ?: 'root';
   $pass = getenv('DB_PASS') ?: '';
   $dbname = getenv('DB_NAME') ?: 'iwadco2_db';
   $port = getenv('DB_PORT') ?: 3306;
   
   $conn = new mysqli($host, $user, $pass, $dbname, $port);
   
   if ($conn->connect_error) {
       error_log("Database connection failed: " . $conn->connect_error);
       die("Database connection failed. Please contact the administrator.");
   }
   
   $conn->set_charset("utf8mb4");
   ?>
   ```
   
   Or use the provided `db_connect_render.php` and update all your `include('db_connect.php')` statements.

6. **Deploy**
   - Click "Create Web Service"
   - Render will build and deploy your application
   - Wait for deployment to complete (usually 2-5 minutes)

### Step 4: Configure Custom Domain (Optional)

1. In your service settings, go to "Custom Domains"
2. Add your domain
3. Follow DNS configuration instructions

## 🔧 Configuration Files

### Using render.yaml (Alternative Method)

If you prefer configuration as code, you can use the provided `render.yaml`:

1. Update `render.yaml` with your settings
2. In Render dashboard, select "Apply render.yaml" when creating service
3. Render will read configuration from the file

### Update All Database Includes

If you created `db_connect_render.php`, you need to update all files that include `db_connect.php`:

**Option 1: Replace in all files**
```bash
# Find all files with db_connect.php
grep -r "db_connect.php" iwadco/

# Manually update each file to use db_connect_render.php
```

**Option 2: Update db_connect.php directly**
- Modify `iwadco/db_connect.php` to use environment variables (see code above)

## 📁 File Structure for Render

Your project structure should be:
```
iwadco/
├── iwadco/
│   ├── index.php (entry point - redirects to login.php)
│   ├── db_connect.php (updated for Render)
│   ├── login.php
│   ├── home.php
│   └── ... (all other PHP files)
├── render.yaml (optional)
└── README.md
```

## ✅ Post-Deployment Checklist

- [ ] Application is accessible via Render URL
- [ ] Database connection is working
- [ ] Login functionality works
- [ ] Admin panel is accessible
- [ ] File uploads work (check `uploads/` directory permissions)
- [ ] Sessions are working
- [ ] All pages load correctly
- [ ] Custom domain configured (if applicable)

## 🐛 Troubleshooting

### Issue: "Database connection failed"
- **Solution**: 
  - Verify environment variables are set correctly in Render dashboard
  - Check database host allows connections from Render's IPs
  - Verify database credentials are correct
  - Check database host and port

### Issue: "404 Not Found"
- **Solution**: 
  - Verify `index.php` exists in `iwadco/` directory
  - Check Root Directory setting in Render
  - Verify Start Command is correct: `php -S 0.0.0.0:$PORT -t iwadco`

### Issue: "Sessions not working"
- **Solution**: 
  - Render handles sessions automatically
  - Ensure `session_start()` is called in files that need sessions
  - Check if sessions directory is writable

### Issue: "File uploads not working"
- **Solution**: 
  - Render's filesystem is ephemeral (resets on deploy)
  - Consider using cloud storage (AWS S3, Cloudinary) for uploads
  - Or use Render's disk storage (paid plans)

### Issue: "Application shows blank page"
- **Solution**: 
  - Check Render logs for PHP errors
  - Enable error reporting in development
  - Verify all includes are correct

## 📊 Render Free Tier Limitations

- **750 hours/month** of runtime (enough for always-on service)
- **512 MB RAM**
- **Ephemeral filesystem** (files reset on deploy)
- **Sleeps after 15 minutes** of inactivity (free tier)
- **No persistent disk** (use external storage for uploads)

## 🔐 Security Best Practices

1. **Never commit credentials** to Git
2. **Use environment variables** for all sensitive data
3. **Enable HTTPS** (automatic on Render)
4. **Use strong database passwords**
5. **Regular backups** of your database
6. **Keep dependencies updated**

## 💰 Cost Considerations

- **Free Tier**: Good for testing and small projects
- **Starter Plan ($7/month)**: No sleep, persistent disk
- **Professional Plan ($25/month)**: More resources, better performance

## 📚 Additional Resources

- [Render PHP Documentation](https://render.com/docs/php)
- [Render Environment Variables](https://render.com/docs/environment-variables)
- [PlanetScale Documentation](https://planetscale.com/docs)

## 🆘 Need Help?

- Check Render logs in dashboard
- Review PHP error logs
- Contact Render support
- Check application logs for specific errors

---

**Ready to deploy?** Follow the steps above and your application will be live on Render!

