# Important Notes for Render Deployment

## ⚠️ Ephemeral Filesystem

**Render's filesystem resets on every deploy.** This means:

- ✅ **Code files**: Persist (from Git)
- ❌ **Uploaded files**: Will be lost on redeploy
- ❌ **Session files**: May be lost (but sessions still work)
- ❌ **Temporary files**: Will be lost

## 📁 File Uploads Solution

Your application has an `uploads/` directory. On Render, you have these options:

### Option 1: Use Cloud Storage (Recommended)

**AWS S3, Cloudinary, or similar:**
- Upload files to cloud storage
- Store URLs in database
- Files persist across deploys

### Option 2: Use Render Disk (Paid Plans)

- Render offers persistent disk on paid plans
- Mount disk to `uploads/` directory
- Files persist across deploys

### Option 3: Accept Temporary Files (Free Tier)

- Files work until next deploy
- Good for testing only
- Not suitable for production

## 🔧 Quick Fix for Testing

For initial testing, you can keep the current upload system. Files will work until you redeploy.

To implement cloud storage later, update your upload code in:
- `iwadco/apply.php` (ID file uploads)
- Any other file upload functionality

## 📊 Database

- ✅ **Database persists** (external MySQL)
- ✅ **Data is safe** across deploys
- ✅ **No data loss** on redeploy

## 🔄 Deployment Process

1. **Push to GitHub** → Auto-deploys on Render
2. **Code updates** → New deployment
3. **Environment variables** → Update in dashboard
4. **Database** → External, always available

## 💡 Best Practices

1. **Use environment variables** for all config
2. **Store uploads in cloud** (not local filesystem)
3. **Backup database regularly**
4. **Monitor Render logs** for errors
5. **Test after each deploy**

## 🆓 Free Tier Limitations

- **Sleeps after 15 min** of inactivity
- **First request** may be slow (cold start)
- **No persistent disk** (free tier)
- **512 MB RAM** limit

## 💰 Upgrade Considerations

**Starter Plan ($7/month):**
- ✅ No sleep (always on)
- ✅ Persistent disk available
- ✅ Better performance

Consider upgrading if:
- You need persistent file uploads
- You want always-on service
- You need better performance

