# IWADCO Database Deployment - Complete Guide

## 📋 Quick Summary

You have a MySQL database file (`iwadco2_db (1).sql`) from XAMPP that needs to be published online. This repository contains all the tools and guides you need.

## 📁 Files Created for You

### 📖 Documentation
- **`DEPLOYMENT_GUIDE.md`** - Complete step-by-step deployment guide
- **`QUICK_START.md`** - Fast-track 5-step guide
- **`PRE_IMPORT_CHECKLIST.md`** - Pre-import checklist and known issues

### 🔧 Helper Scripts
- **`iwadco/verify_database.php`** - Verify database after import
- **`iwadco/fix_admin_email.php`** - Fix admin email typo automatically
- **`iwadco/db_connect_production.php.example`** - Production config template
- **`iwadco/db_connect_switch.php`** - Auto-detect local/production environment

### 🔒 Security
- **`iwadco/.gitignore`** - Prevents committing sensitive credentials

## 🚀 Quick Start (3 Steps)

### 1. Import Database
- Log into your hosting phpMyAdmin
- Create a new database
- Import `iwadco2_db (1).sql` file

### 2. Update Configuration
- Edit `iwadco/db_connect.php` with your production database credentials:
  ```php
  $host = 'localhost'; // or your host
  $user = 'your_username';
  $pass = 'your_password';
  $dbname = 'your_database_name';
  ```

### 3. Verify & Fix
- Upload `verify_database.php` and run it
- Upload `fix_admin_email.php` and run it to fix the email typo
- Delete both scripts after use

## ⚠️ Known Issues in Your SQL File

1. **Admin Email Typo**: `nishcruz8@gmail.com@gmail.com` (double @gmail.com)
   - **Fix**: Use `fix_admin_email.php` or run:
     ```sql
     UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';
     ```

2. **Admin Password**: Password is hashed, default password unknown
   - You may need to reset it after first login

## 📊 Your Database Structure

Your database contains:
- **3 tables**: `users`, `billing`, `application`
- **1 admin user**: username `admin`
- **Foreign keys**: `billing` → `users`

## 🔐 Security Checklist

- [ ] Update `db_connect.php` with production credentials
- [ ] Use strong database passwords
- [ ] Delete verification/fix scripts after use
- [ ] Don't commit credentials to Git
- [ ] Test all functionality after deployment
- [ ] Set up regular database backups

## 📚 Detailed Guides

- **New to deployment?** → Start with `QUICK_START.md`
- **Need detailed steps?** → Read `DEPLOYMENT_GUIDE.md`
- **Before importing?** → Check `PRE_IMPORT_CHECKLIST.md`

## 🆘 Troubleshooting

### Can't connect to database?
- Verify credentials in `db_connect.php`
- Check database host (not always `localhost`)
- Ensure database user has proper permissions

### Import failed?
- Check file size limits in phpMyAdmin
- Try command line import if available
- Verify SQL file is not corrupted

### Need more help?
- Check your hosting provider's documentation
- Review PHP error logs
- Contact hosting support

## ✅ Post-Deployment Checklist

- [ ] Database imported successfully
- [ ] `db_connect.php` updated with production credentials
- [ ] Verified database structure (using `verify_database.php`)
- [ ] Fixed admin email typo
- [ ] Tested admin login
- [ ] Tested user registration
- [ ] Tested all application features
- [ ] Deleted helper scripts (`verify_database.php`, `fix_admin_email.php`)
- [ ] Set up database backups
- [ ] Application is live and working

---

**Ready to deploy?** Start with `QUICK_START.md` or `DEPLOYMENT_GUIDE.md`!

