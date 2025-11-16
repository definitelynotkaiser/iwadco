# Quick Start: Publishing Your Database Online

## Fast Track (5 Steps)

### 1. Get Your SQL File Ready
- Locate your `iwadco2_db (1).sql` file
- Make sure it's accessible

### 2. Access Your Hosting Control Panel
- Log into your web hosting account
- Find **phpMyAdmin** or **MySQL Databases** section

### 3. Create Database
- Create a new MySQL database
- Create a database user with password
- **Write down these credentials:**
  - Database name: `_________________`
  - Username: `_________________`
  - Password: `_________________`
  - Host: `_________________` (usually `localhost`)

### 4. Import SQL File
- Open phpMyAdmin
- Select your database
- Click **Import** tab
- Choose your `iwadco2_db (1).sql` file
- Click **Go**

### 5. Update Configuration
- Edit `iwadco/db_connect.php` on your server
- Replace with your production credentials:
  ```php
  $host = 'localhost'; // or your host
  $user = 'your_username';
  $pass = 'your_password';
  $dbname = 'your_database_name';
  ```

## Common Hosting Providers

### cPanel Hosting
- Database host: Usually `localhost`
- Access: cPanel → MySQL Databases → phpMyAdmin

### Shared Hosting (Hostinger, Bluehost, etc.)
- Database host: Usually `localhost` or provided in cPanel
- Access: cPanel → phpMyAdmin

### Cloud Hosting
- Check your provider's documentation for database connection details

## Troubleshooting

**Can't connect?**
- Verify credentials are correct
- Check if database host is correct (not always `localhost`)
- Ensure database user has proper permissions

**Import failed?**
- Check file size limits
- Try importing via command line if available
- Split large SQL files if needed

**Need more help?**
- See `DEPLOYMENT_GUIDE.md` for detailed instructions
- Contact your hosting provider's support

