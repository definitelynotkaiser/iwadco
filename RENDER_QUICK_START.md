# Quick Start: Deploy sa Render

## 🚀 Mabilis na Paraan (5 Hakbang)

### 1. I-push sa GitHub
```bash
git add .
git commit -m "Ready for Render"
git push
```

### 2. Gumawa ng PostgreSQL Database sa Render

1. Pumunta sa [render.com](https://render.com)
2. Sign up/login gamit ang GitHub
3. I-click ang "New +" → "PostgreSQL"
4. Piliin ang "Free" plan
5. I-click ang "Create Database"
6. Hintayin na ma-create (green status)

### 3. I-import ang Database

1. Sa PostgreSQL database → "Data" tab
2. I-click ang "Connect" o buksan ang SQL Editor
3. Kopyahin ang lahat ng contents ng `iwadco2_db_postgresql.sql`
4. I-paste sa SQL editor
5. I-click ang "Run" o "Execute"
6. I-verify na na-create ang tables

### 4. Gumawa ng Web Service

1. Sa Render dashboard, i-click ang "New +" → "Web Service"
2. I-connect ang GitHub repository
3. Piliin ang `iwadco` repository
4. I-configure:
   - **Start Command**: `php -S 0.0.0.0:$PORT -t iwadco`
   - **Root Directory**: `iwadco` (o iwanang blangko)

### 5. I-configure ang Environment Variables

Sa web service → "Environment" tab, idagdag:

```
DB_TYPE=postgresql
DB_HOST=your-postgres-host.render.com
DB_USER=your-username
DB_PASS=your-password
DB_NAME=your-database-name
DB_PORT=5432
```

**O kung may Internal Database URL:**
```
DATABASE_URL=postgresql://user:password@host:port/database
```

### 6. I-update ang Database Connection

**Option 1: Gamit ang Smart Connection (RECOMMENDED)**
- I-rename ang `db_connect_render.php` sa `db_connect.php`
- O i-update ang lahat ng includes

**Option 2: Gamit ang PostgreSQL Connection**
- I-rename ang `db_connect_postgresql.php` sa `db_connect.php`

### 7. I-deploy!

- Render auto-deploys kapag nag-push ka sa GitHub
- O i-deploy manually mula sa dashboard

## ⚠️ IMPORTANT: Code Changes Needed

Ang application mo ay gumagamit ng **MySQLi**, pero ang Render ay **PostgreSQL**. Kailangan mong:

1. **I-convert ang MySQLi code sa PDO** (para sa PostgreSQL)
2. **O gumamit ng compatibility layer**

**Mas madali:** Gamitin ang `db_connect_render.php` na may compatibility functions.

## ✅ Tapos Na!

Ang app mo: `https://your-app-name.onrender.com`

**Tingnan ang `RENDER_DEPLOYMENT.md` para sa detailed guide!**

