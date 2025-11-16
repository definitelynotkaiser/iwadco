# Deploying IWADCO to Render.com

This guide will help you deploy your PHP application to Render.com using PostgreSQL.

## 🎯 Why Render?

- ✅ **Free tier available** (750 hours/month)
- ✅ **Built-in PostgreSQL** database
- ✅ **Automatic HTTPS**
- ✅ **Simple deployment** from GitHub
- ✅ **Environment variables** management

## 📋 Prerequisites

1. **GitHub Account** - Render connects via GitHub
2. **Render Account** - Sign up at [render.com](https://render.com) (free tier available)
3. **PostgreSQL SQL file** - `iwadco2_db_postgresql.sql` (provided)

## 🚀 Step-by-Step Deployment

### Step 1: Prepare Your Repository

1. **Push your code to GitHub** (if not already done):
   ```bash
   git add .
   git commit -m "Ready for Render deployment"
   git push
   ```

### Step 2: Create Render PostgreSQL Database

1. **Log into Render**
   - Go to [render.com](https://render.com)
   - Sign up/login with GitHub

2. **Create PostgreSQL Database**
   - Click "New +" → "PostgreSQL"
   - Name it: `iwadco-db` (or your preferred name)
   - Select "Free" plan (or paid if needed)
   - Choose region closest to you
   - Click "Create Database"
   - Wait for it to be ready (green status)

3. **Get Database Credentials**
   - Click on your PostgreSQL database
   - Go to "Connections" tab
   - Note down:
     - **Internal Database URL** (for Render services)
     - **External Database URL** (for local connection)
     - Or individual credentials:
       - Host
       - Port (usually 5432)
       - Database name
       - Username
       - Password

### Step 3: Import Database Schema

**Option A: Using Render's Web Interface (Easiest)**

1. Go to your PostgreSQL database → "Data" tab
2. Click "Connect" or use the SQL Editor
3. Copy contents of `iwadco2_db_postgresql.sql`
4. Paste into SQL editor
5. Click "Run" or "Execute"
6. Verify tables were created

**Option B: Using psql Command Line**

1. Install PostgreSQL client tools
2. Use External Database URL from Render:
   ```bash
   psql "postgresql://user:password@host:port/database" -f iwadco2_db_postgresql.sql
   ```

**Option C: Using pgAdmin or DBeaver**

1. Connect using External Database URL
2. Open SQL editor
3. Copy and paste `iwadco2_db_postgresql.sql`
4. Execute

### Step 4: Create Web Service

1. **Create New Web Service**
   - In Render dashboard, click "New +" → "Web Service"
   - Connect your GitHub repository
   - Select your `iwadco` repository

2. **Configure Service**
   - **Name**: `iwadco` (or your preferred name)
   - **Environment**: `PHP`
   - **Build Command**: Leave empty (or `composer install` if using Composer)
   - **Start Command**: `php -S 0.0.0.0:$PORT -t iwadco`
   - **Root Directory**: Leave empty (or `iwadco` if needed)

### Step 5: Configure Environment Variables

1. **In your Web Service** → "Environment" tab
2. **Add these variables:**

   ```
   DB_HOST=your-postgres-host.render.com
   DB_USER=your-username
   DB_PASS=your-password
   DB_NAME=your-database-name
   DB_PORT=5432
   APP_ENV=production
   ```

   **OR use Render's Internal Database URL:**
   
   If Render provides an Internal Database URL, you can parse it:
   ```
   DATABASE_URL=postgresql://user:password@host:port/database
   ```

3. **Update db_connect.php to use PostgreSQL:**
   
   You have two options:
   
   **Option 1: Use the provided PostgreSQL connection file**
   - Rename `db_connect_postgresql.php` to `db_connect.php`
   - Or update all includes to use `db_connect_postgresql.php`
   
   **Option 2: Update existing db_connect.php**
   - Replace MySQL connection with PostgreSQL PDO connection
   - See `db_connect_postgresql.php` for reference

### Step 6: Update Database Connection in Code

Since your code uses MySQLi, you need to either:

**Option A: Convert to PDO (Recommended)**
- Use the provided `db_connect_postgresql.php`
- Update all files that use `$conn->query()` to use PDO methods
- This is more work but better for PostgreSQL

**Option B: Use PostgreSQL-compatible MySQLi wrapper**
- Keep MySQLi syntax but use PostgreSQL adapter
- More complex, not recommended

**Option C: Use both (Hybrid)**
- Keep MySQL for local development
- Use PostgreSQL for production
- Requires conditional logic

### Step 7: Deploy

1. **Render auto-deploys** when you push to GitHub
2. **Or manually deploy** from Render dashboard
3. **Check deployment logs** for any errors
4. **Your app will be live** at: `https://your-app-name.onrender.com`

## 🔧 Important: Code Changes Needed

Since your application uses MySQLi and PostgreSQL uses PDO, you'll need to update your code:

### Quick Conversion Guide:

**MySQLi:**
```php
$result = $conn->query("SELECT * FROM users");
$row = $result->fetch_assoc();
```

**PostgreSQL PDO:**
```php
$stmt = $conn->query("SELECT * FROM users");
$row = $stmt->fetch();
```

**MySQLi Prepared:**
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
```

**PostgreSQL PDO:**
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();
```

## ✅ Post-Deployment Checklist

- [ ] Database schema imported successfully
- [ ] Environment variables configured
- [ ] Database connection working
- [ ] All pages loading correctly
- [ ] Forms submitting properly
- [ ] Login functionality working
- [ ] Admin panel accessible

## 🐛 Troubleshooting

### Issue: "Database connection failed"
- **Solution**: 
  - Verify environment variables are set correctly
  - Check database host allows connections
  - Verify credentials are correct
  - Ensure database is running

### Issue: "Table does not exist"
- **Solution**: 
  - Verify SQL file was imported correctly
  - Check table names (PostgreSQL is case-sensitive for quoted names)
  - Re-import the schema

### Issue: "Column does not exist"
- **Solution**: 
  - PostgreSQL is case-sensitive for quoted identifiers
  - Check column names match exactly
  - Use lowercase or quoted identifiers consistently

### Issue: "Syntax error"
- **Solution**: 
  - PostgreSQL syntax differs from MySQL
  - Check LIMIT syntax (use LIMIT/OFFSET)
  - Check date functions
  - Check string concatenation (use || instead of CONCAT)

## 📚 Additional Resources

- [Render PHP Documentation](https://render.com/docs/php)
- [Render PostgreSQL Documentation](https://render.com/docs/databases/postgresql)
- [PostgreSQL PHP PDO](https://www.php.net/manual/en/ref.pdo-pgsql.php)

## ⚠️ Important Notes

1. **PostgreSQL vs MySQL**: Your code uses MySQLi, which needs conversion to PDO for PostgreSQL
2. **Case Sensitivity**: PostgreSQL is case-sensitive for quoted identifiers
3. **Syntax Differences**: Some SQL syntax differs between MySQL and PostgreSQL
4. **Migration Effort**: Converting from MySQLi to PDO requires code changes

## 💡 Recommendation

If you want to avoid code changes, consider:
- **Stick with Railway** (uses MySQL, no code changes needed)
- **Or use external MySQL** on Render (PlanetScale, AWS RDS, etc.)

---

**Ready to deploy?** Follow the steps above and your application will be live on Render!

