# Database Deployment Guide - IWADCO

This guide will help you publish your `iwadco2_db (1).sql` database file online.

## Prerequisites
- Your `iwadco2_db (1).sql` file from XAMPP
- A web hosting account with MySQL database access
- FTP or file manager access to your hosting account

## Step 1: Choose a Web Hosting Provider

Popular options:
- **cPanel hosting** (most common - Bluehost, Hostinger, etc.)
- **phpMyAdmin** access (usually included with hosting)
- **Cloud providers** (AWS RDS, Google Cloud SQL, Azure Database)

## Step 2: Create Database on Your Hosting

### For cPanel/phpMyAdmin:
1. Log into your hosting control panel (cPanel)
2. Go to **MySQL Databases** or **Database** section
3. Create a new database (e.g., `yourusername_iwadco2_db`)
4. Create a database user and assign it to the database
5. Note down:
   - Database name
   - Database username
   - Database password
   - Database host (usually `localhost` or provided by host)

## Step 3: Import Your SQL File

### Method 1: Using phpMyAdmin (Recommended)
1. Log into phpMyAdmin from your hosting control panel
2. Select your database from the left sidebar
3. Click on the **Import** tab
4. Click **Choose File** and select your `iwadco2_db (1).sql` file
5. Click **Go** to import
6. Wait for the import to complete (you'll see a success message)

### Method 2: Using Command Line (if you have SSH access)
```bash
mysql -u your_username -p your_database_name < iwadco2_db\ \(1\).sql
```

### Method 3: Using MySQL Workbench
1. Connect to your remote database
2. Go to Server → Data Import
3. Select "Import from Self-Contained File"
4. Choose your SQL file
5. Select the target database
6. Click "Start Import"

## Step 4: Verify Database Import

After importing, use the verification script to check everything is correct:

1. Upload `verify_database.php` to your server
2. Access it via: `http://yourdomain.com/iwadco/verify_database.php`
3. Review the verification report
4. **Delete the verification script after use** for security

### Fix Known Issues

Your SQL file has a typo in the admin email (`nishcruz8@gmail.com@gmail.com`). Fix it using:

1. Upload `fix_admin_email.php` to your server
2. Access it via: `http://yourdomain.com/iwadco/fix_admin_email.php`
3. Click "Fix Email" button
4. **Delete the fix script after use** for security

Or manually fix via phpMyAdmin:
```sql
UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';
```

## Step 5: Update Database Configuration

After importing, update your `db_connect.php` file with your online database credentials:

```php
<?php
$host = 'localhost'; // or your hosting provider's database host
$user = 'your_database_username';
$pass = 'your_database_password';
$dbname = 'your_database_name';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

**Important:** 
- Replace `localhost` with your hosting provider's database host if different
- Use the exact database name, username, and password from your hosting account
- Keep the production credentials secure - never commit them to public repositories

## Step 6: Upload Your PHP Files

1. Upload all your PHP files to your hosting account (usually via FTP or File Manager)
2. Make sure `db_connect.php` has the updated credentials
3. Ensure file permissions are correct (usually 644 for files, 755 for directories)

## Step 7: Test Your Application

1. Visit your website URL
2. Test login functionality
3. Verify database connections are working
4. Check if all features are functioning correctly

## Common Issues & Solutions

### Issue: "Access denied for user"
- **Solution:** Double-check your database username and password in `db_connect.php`

### Issue: "Unknown database"
- **Solution:** Verify the database name matches exactly (case-sensitive on Linux servers)

### Issue: "Can't connect to MySQL server"
- **Solution:** Check if the host is correct (some hosts use different hostnames like `mysql.yourhost.com`)

### Issue: SQL file too large
- **Solution:** 
  - Increase upload limit in phpMyAdmin settings
  - Or split the SQL file into smaller chunks
  - Or use command line import method

## Security Recommendations

1. **Use environment variables** for database credentials (see `db_connect_production.php.example`)
2. **Never commit** production credentials to version control
3. **Use strong passwords** for your database
4. **Enable SSL** for database connections if available
5. **Regular backups** of your online database

## Next Steps

After deployment:
- Test all functionality
- Set up regular database backups
- Monitor for any errors
- Update your application as needed

---

**Need Help?**
- Contact your hosting provider's support
- Check hosting provider's documentation
- Review PHP error logs for debugging

