# Pre-Import Checklist for iwadco2_db

## Before Importing Your SQL File

### ✅ Database Structure Overview
Your SQL file contains:
- **3 tables**: `users`, `billing`, `application`
- **1 admin user** (username: `admin`)
- **Foreign key constraints** between `billing` and `users`

### ⚠️ Issues to Fix Before/After Import

#### 1. Admin Email Typo
The admin user has a typo in the email:
- Current: `nishcruz8@gmail.com@gmail.com` (double @gmail.com)
- Should be: `nishcruz8@gmail.com`

**Fix after import:**
```sql
UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';
```

#### 2. Admin Password
The admin password is hashed. Default password is unknown from the SQL file.
- You may need to reset the admin password after import
- Or use the password reset feature in your application

**To reset admin password (if needed):**
```sql
-- This will set password to 'admin123' (change as needed)
UPDATE users SET password = '$2y$10$YourHashedPasswordHere' WHERE username = 'admin';
```

### 📋 Import Steps

1. **Create Database**
   - Name: `iwadco2_db` (or your preferred name)
   - Character set: `utf8mb4`
   - Collation: `utf8mb4_general_ci`

2. **Import SQL File**
   - Use phpMyAdmin Import feature
   - Or command line: `mysql -u user -p database_name < iwadco2_db\ \(1\).sql`

3. **Verify Import**
   - Run `verify_database.php` script
   - Check all tables exist
   - Verify admin user exists

4. **Fix Issues**
   - Fix admin email typo (see SQL above)
   - Test admin login
   - Reset password if needed

5. **Update Configuration**
   - Update `db_connect.php` with production credentials
   - Test connection

### 🔍 Verification Queries

After import, run these in phpMyAdmin to verify:

```sql
-- Check tables exist
SHOW TABLES;

-- Check admin user
SELECT * FROM users WHERE username = 'admin';

-- Check table structures
DESCRIBE users;
DESCRIBE billing;
DESCRIBE application;

-- Check foreign keys
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

### 🚨 Common Import Issues

1. **"Table already exists"**
   - Solution: Drop existing tables or use a new database

2. **"Foreign key constraint fails"**
   - Solution: Import in order, or temporarily disable foreign key checks:
   ```sql
   SET FOREIGN_KEY_CHECKS = 0;
   -- Import your SQL
   SET FOREIGN_KEY_CHECKS = 1;
   ```

3. **"Unknown collation"**
   - Solution: Ensure MySQL version supports utf8mb4_general_ci

4. **"Access denied"**
   - Solution: Check database user permissions

### 📝 Post-Import Tasks

- [ ] Verify all tables imported correctly
- [ ] Fix admin email typo
- [ ] Test admin login
- [ ] Update `db_connect.php` with production credentials
- [ ] Run `verify_database.php` script
- [ ] Delete verification script after use
- [ ] Test application functionality
- [ ] Create backup of imported database

### 🔐 Security Reminders

- Change default admin password after first login
- Use strong passwords for database users
- Keep database credentials secure
- Don't commit credentials to version control
- Enable SSL for database connections if available

