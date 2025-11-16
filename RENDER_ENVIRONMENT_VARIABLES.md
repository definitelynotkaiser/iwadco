# Environment Variables para sa Render

## 📋 Environment Variables na Kailangan

### Para sa PostgreSQL Database Connection:

Idagdag ang mga sumusunod sa **Web Service** → **Environment** tab sa Render:

```
DB_TYPE=postgresql
DB_HOST=dpg-xxxxxxxxxxxxx-a.render.com
DB_USER=iwadco_user
DB_PASS=your_password_here
DB_NAME=iwadco_db
DB_PORT=5432
APP_ENV=production
```

## 🔍 Saan Makukuha ang Values?

### Step 1: Buksan ang PostgreSQL Database sa Render

1. Pumunta sa Render dashboard
2. I-click ang **PostgreSQL database** service mo
3. Pumunta sa **"Connections"** tab

### Step 2: Kunin ang Database Credentials

Makikita mo ang:

**Option A: Individual Credentials**
- **Host**: `dpg-xxxxxxxxxxxxx-a.render.com` (halimbawa)
- **Port**: `5432`
- **Database**: `iwadco_db` (o anumang pangalan mo)
- **User**: `iwadco_user` (o anumang username)
- **Password**: `your_password_here` (auto-generated ng Render)

**Option B: Internal Database URL**
```
postgresql://iwadco_user:password@dpg-xxxxx-a.render.com:5432/iwadco_db
```

**Option C: External Database URL** (para sa local access)
```
postgresql://iwadco_user:password@dpg-xxxxx-a.render.com:5432/iwadco_db
```

## 📝 Paano I-set ang Environment Variables

### Sa Render Dashboard:

1. **Pumunta sa Web Service** (hindi sa PostgreSQL service)
2. I-click ang **"Environment"** tab
3. I-click ang **"Add Environment Variable"** button
4. Idagdag ang bawat variable **isa-isa**:

   **Variable 1:**
   - **Key**: `DB_TYPE`
   - **Value**: `postgresql`
   - I-click **"Save"**

   **Variable 2:**
   - **Key**: `DB_HOST`
   - **Value**: `dpg-xxxxxxxxxxxxx-a.render.com` (kopyahin mula sa Connections tab)
   - I-click **"Save"**

   **Variable 3:**
   - **Key**: `DB_USER`
   - **Value**: `iwadco_user` (o anumang username mula sa Connections)
   - I-click **"Save"**

   **Variable 4:**
   - **Key**: `DB_PASS`
   - **Value**: `your_password_here` (kopyahin mula sa Connections)
   - I-click **"Save"**

   **Variable 5:**
   - **Key**: `DB_NAME`
   - **Value**: `iwadco_db` (o anumang database name)
   - I-click **"Save"**

   **Variable 6:**
   - **Key**: `DB_PORT`
   - **Value**: `5432`
   - I-click **"Save"**

   **Variable 7:**
   - **Key**: `APP_ENV`
   - **Value**: `production`
   - I-click **"Save"**

## 🔐 Security Tips

1. **Huwag i-commit ang credentials** sa GitHub
2. **Gamitin ang Internal Database URL** kung available (mas secure)
3. **I-keep ang passwords secure** - huwag i-share
4. **I-verify** na tama ang lahat ng values

## ✅ Verification

Pagkatapos mag-set ng environment variables:

1. **I-redeploy** ang web service
2. **I-check ang logs** para sa connection errors
3. **I-test** ang application kung gumagana na

## 🎯 Quick Copy-Paste Template

Kapag nakuha mo na ang credentials mula sa Render, i-replace ang values:

```
DB_TYPE=postgresql
DB_HOST=YOUR_HOST_HERE
DB_USER=YOUR_USERNAME_HERE
DB_PASS=YOUR_PASSWORD_HERE
DB_NAME=YOUR_DATABASE_NAME_HERE
DB_PORT=5432
APP_ENV=production
```

## 📸 Visual Guide

```
Render Dashboard
├── Web Service (iwadco)
│   └── Environment Tab
│       ├── DB_TYPE = postgresql
│       ├── DB_HOST = dpg-xxxxx.render.com
│       ├── DB_USER = iwadco_user
│       ├── DB_PASS = ********
│       ├── DB_NAME = iwadco_db
│       ├── DB_PORT = 5432
│       └── APP_ENV = production
│
└── PostgreSQL Database
    └── Connections Tab
        └── (Dito mo makikita ang credentials)
```

## ⚠️ Important Notes

1. **Web Service** ang dapat i-edit, hindi ang PostgreSQL service
2. **Tiyakin** na tama ang spelling ng variable names
3. **Walang spaces** sa values (maliban kung part ng value)
4. **Case-sensitive** ang variable names (DB_HOST, hindi db_host)
5. **I-redeploy** pagkatapos mag-edit ng environment variables

## 🆘 Troubleshooting

**"Database connection failed"**
- I-verify na tama ang lahat ng credentials
- I-check kung running ang PostgreSQL service
- I-verify ang DB_HOST (dapat may `.render.com`)

**"Table does not exist"**
- I-verify na na-import na ang database schema
- I-check kung tama ang DB_NAME

**"Access denied"**
- I-verify ang DB_USER at DB_PASS
- I-check kung may permissions ang user

---

**Ready na!** I-set mo lang ang environment variables at gagana na ang database connection! 🚀

