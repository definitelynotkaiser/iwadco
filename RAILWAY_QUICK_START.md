# Quick Start: I-deploy sa Railway

## 🚀 Mabilis na Paraan (5 Hakbang)

### 1. I-push sa GitHub

```bash
git init
git add .
git commit -m "Ready for Railway"
git remote add origin https://github.com/yourusername/iwadco.git
git push -u origin main
```

**Paalala:** Kung wala ka pang GitHub repository, gawin muna ito:
1. Pumunta sa [github.com](https://github.com)
2. Gumawa ng bagong repository na may pangalang `iwadco`
3. Sundin ang commands sa itaas

### 2. Gumawa ng Railway Project

1. Pumunta sa [railway.app](https://railway.app)
2. Mag-sign up o mag-login gamit ang GitHub account mo
3. I-click ang "New Project" (sa top right o sa dashboard)
4. Piliin ang "Deploy from GitHub repo"
5. Piliin ang `iwadco` repository mo
6. Hintayin na ma-detect ni Railway na PHP project ito
7. **Makikita mo ang Web Service** - automatic na mag-create si Railway nito pagkatapos mag-connect ng GitHub repo

### 3. Magdagdag ng MySQL Database

1. Sa Railway project dashboard, i-click ang "+ New" button
2. Piliin ang "Database"
3. I-click ang "Add MySQL"
4. Hintayin na ma-create ni Railway ang database (makikita mo ang green status)
5. **Mahalaga:** Tandaan ang pangalan ng MySQL service (halimbawa: "MySQL" o "mysql")

### 4. I-configure ang Environment Variables

**Mahalagang hakbang ito para makakonekta ang application sa database!**

**📍 Saan makikita ang Web Service?**

1. **Pumunta sa Railway Dashboard:**
   - Pagkatapos mag-create ng project at mag-add ng MySQL, makikita mo ang Railway project dashboard
   - Makikita mo ang dalawang services:
     - **Web Service** (PHP/Application) - ito ang application mo
     - **MySQL Service** (Database) - ito ang database

2. **Paano makilala ang Web Service:**
   - Karaniwang may pangalan na katulad ng project name o repository name
   - May icon na web/globe (hindi database icon)
   - Kapag na-deploy na, may URL na makikita (halimbawa: `https://your-app.up.railway.app`)
   - May "Deployments" tab na makikita

3. **Kung hindi mo makita:**
   - Tiyakin na na-deploy na ang project mula sa GitHub
   - Kapag nag-deploy ka from GitHub repo, automatic na mag-create si Railway ng web service
   - Kung wala pa, i-check kung na-push na ba ang code sa GitHub

**Ngayon, i-configure ang Environment Variables:**

1. Sa Railway dashboard, i-click ang **web service** (hindi ang MySQL service)
2. Pumunta sa **"Variables"** tab
3. I-click ang **"New Variable"** button
4. Idagdag ang mga sumusunod na variables **isa-isa**:

   ```
   DB_HOST=${{MySQL.MYSQLHOST}}
   ```
   *(Palitan ang "MySQL" kung iba ang pangalan ng MySQL service mo)*

   ```
   DB_USER=${{MySQL.MYSQLUSER}}
   ```

   ```
   DB_PASS=${{MySQL.MYSQLPASSWORD}}
   ```

   ```
   DB_NAME=${{MySQL.MYSQLDATABASE}}
   ```

   ```
   DB_PORT=${{MySQL.MYSQLPORT}}
   ```

5. **Settings (sa web service → Settings tab):**
   
   **Option 1: Gamit ang Root Directory (RECOMMENDED)**
   - **Root Directory:** `iwadco`
   - **Start Command:** `php -S 0.0.0.0:$PORT`
   - I-save ang settings
   
   **Option 2: Gamit ang Start Command lang**
   - **Root Directory:** Iwanang blangko
   - **Start Command:** `cd iwadco && php -S 0.0.0.0:$PORT`
   - I-save ang settings
   
   **Paalala:** Pumili lang ng isang option. Mas madali ang Option 1.

**Paalala:** Gamitin ang eksaktong syntax na `${{MySQL.Variable}}` - mahalaga ang `{{` at `}}`!

### 5. I-import ang Database at I-deploy

**ITO ANG PINAKA-MAHALAGANG HAKBANG!**

Kailangan mong i-import ang `iwadco2_db (1).sql` file sa Railway MySQL database. Pumili ng isang paraan:

---

## 📥 DETALYADONG PARAAN NG DATABASE IMPORT

### **Paraan A: Gamit ang Railway CLI (Pinakamadali) ⭐ RECOMMENDED**

**Step 1: I-install ang Railway CLI**

1. I-install ang Node.js kung wala ka pa: [nodejs.org](https://nodejs.org)
2. Buksan ang Command Prompt o Terminal
3. I-type ang command:
   ```bash
   npm i -g @railway/cli
   ```
4. Hintayin na matapos ang installation

**Step 2: I-login at i-link ang project**

1. Sa terminal, i-type:
   ```bash
   railway login
   ```
2. Bubuksan ang browser para sa authentication - i-approve lang
3. Pagkatapos, i-type:
   ```bash
   railway link
   ```
4. Piliin ang Railway project mo mula sa listahan

**Step 3: Kunin ang database credentials**

1. I-type:
   ```bash
   railway variables
   ```
2. Makikita mo ang lahat ng environment variables kasama ang MySQL credentials
3. Kopyahin ang mga sumusunod:
   - `MYSQLHOST`
   - `MYSQLUSER`
   - `MYSQLPASSWORD`
   - `MYSQLDATABASE`
   - `MYSQLPORT`

**Step 4: I-import ang SQL file**

1. Pumunta sa folder kung saan naka-save ang `iwadco2_db (1).sql` file
2. I-type ang command (palitan ang values):
   ```bash
   mysql -h YOUR_MYSQLHOST -u YOUR_MYSQLUSER -pYOUR_MYSQLPASSWORD YOUR_MYSQLDATABASE < "iwadco2_db (1).sql"
   ```
   
   **Halimbawa:**
   ```bash
   mysql -h mysql.railway.internal -u root -pMyPassword123 iwadco2_db < "iwadco2_db (1).sql"
   ```
   
   **Paalala:** 
   - Walang space pagkatapos ng `-p` at password
   - Kung may error sa file name dahil sa spaces, gamitin ang quotes: `"iwadco2_db (1).sql"`
   - Kung wala kang `mysql` command, i-install muna ang MySQL client

**Step 5: Ayusin ang admin email**

1. Pagkatapos ng import, kumonekta ulit sa database:
   ```bash
   mysql -h YOUR_MYSQLHOST -u YOUR_MYSQLUSER -pYOUR_MYSQLPASSWORD YOUR_MYSQLDATABASE
   ```
2. I-type ang SQL command:
   ```sql
   UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';
   ```
3. I-type `exit` para lumabas

---

### **Paraan B: Gamit ang MySQL Client (MySQL Workbench/DBeaver)**

**Step 1: I-download at i-install ang MySQL Client**

- **MySQL Workbench:** [mysql.com/products/workbench](https://dev.mysql.com/downloads/workbench/)
- **DBeaver:** [dbeaver.io](https://dbeaver.io/) (libre at mas madali)

**Step 2: Kunin ang connection details mula sa Railway**

1. Sa Railway dashboard, i-click ang **MySQL service**
2. Pumunta sa **"Variables"** tab
3. Kopyahin ang mga sumusunod:
   - `MYSQLHOST` - halimbawa: `mysql.railway.internal` o external host
   - `MYSQLPORT` - karaniwang `3306`
   - `MYSQLUSER` - username
   - `MYSQLPASSWORD` - password
   - `MYSQLDATABASE` - database name

**Step 3: Kumonekta sa database**

**Para sa MySQL Workbench:**
1. Buksan ang MySQL Workbench
2. I-click ang "+" button para gumawa ng bagong connection
3. Ilagay ang credentials:
   - **Connection Name:** Railway MySQL
   - **Hostname:** `MYSQLHOST` value
   - **Port:** `MYSQLPORT` value
   - **Username:** `MYSQLUSER` value
   - **Password:** I-click ang "Store in Keychain" at ilagay ang password
4. I-click ang "Test Connection" para i-verify
5. I-click ang "OK" para i-save

**Para sa DBeaver:**
1. Buksan ang DBeaver
2. I-click ang "New Database Connection" button
3. Piliin ang "MySQL"
4. Ilagay ang credentials sa connection settings
5. I-click ang "Test Connection"
6. I-click ang "Finish"

**Step 4: I-import ang SQL file**

**Para sa MySQL Workbench:**
1. Kumonekta sa database
2. I-click ang "Server" → "Data Import"
3. Piliin ang "Import from Self-Contained File"
4. I-browse at piliin ang `iwadco2_db (1).sql` file
5. Sa "Default Target Schema," piliin ang database name
6. I-click ang "Start Import"
7. Hintayin na matapos (makikita mo ang success message)

**Para sa DBeaver:**
1. Right-click sa database name
2. Piliin ang "SQL Editor" → "Open SQL Script"
3. Buksan ang `iwadco2_db (1).sql` file
4. I-click ang "Execute SQL Script" button (play icon)
5. Hintayin na matapos

**Step 5: Ayusin ang admin email**

1. Sa SQL editor, i-type:
   ```sql
   UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';
   ```
2. I-execute ang command
3. I-verify na na-update:
   ```sql
   SELECT email FROM users WHERE username = 'admin';
   ```

---

### **Paraan C: Gamit ang Railway Web Interface**

**Step 1: Buksan ang Railway Data Tab**

1. Sa Railway dashboard, i-click ang **MySQL service**
2. Pumunta sa **"Data"** tab
3. Makikita mo ang web-based SQL editor

**Step 2: I-import ang SQL file**

1. Buksan ang `iwadco2_db (1).sql` file sa text editor (Notepad, VS Code, etc.)
2. Kopyahin ang LAHAT ng contents (Ctrl+A, Ctrl+C)
3. Bumalik sa Railway Data tab
4. I-paste ang SQL content sa SQL editor
5. I-click ang "Run" o "Execute" button
6. Hintayin na matapos (makikita mo ang success message)

**Step 3: Ayusin ang admin email**

1. Sa parehong SQL editor, i-type:
   ```sql
   UPDATE users SET email = 'nishcruz8@gmail.com' WHERE username = 'admin';
   ```
2. I-click ang "Run" button
3. Dapat makita mo ang "1 row affected" message

---

## ✅ Pagkatapos ng Import

**I-verify na successful ang import:**

1. Sa SQL editor o client, i-run:
   ```sql
   SHOW TABLES;
   ```
   Dapat makita mo ang: `users`, `billing`, `application`

2. I-check ang admin user:
   ```sql
   SELECT * FROM users WHERE username = 'admin';
   ```
   Dapat may 1 row na result

3. I-verify ang email:
   ```sql
   SELECT email FROM users WHERE username = 'admin';
   ```
   Dapat: `nishcruz8@gmail.com` (hindi na may double @gmail.com)

---

## 🚀 I-deploy ang Application

**Option 1: Automatic Deploy (Recommended)**
- Kapag nag-push ka sa GitHub, automatic na mag-deploy si Railway
- Hintayin lang na matapos ang deployment (makikita sa logs)

**Option 2: Manual Deploy**
1. Sa Railway dashboard, pumunta sa web service
2. I-click ang "Deploy" button
3. Hintayin na matapos

**I-check ang logs:**
- Pumunta sa web service → "Deployments" tab
- I-click ang latest deployment
- I-check kung may errors

---

## ✅ Tapos Na!

Ang application mo ay nasa: `https://your-app-name.up.railway.app`

**I-verify na gumagana:**
1. Bisitahin ang app URL: `https://your-app-name.up.railway.app/login.php`
2. Subukan mag-login gamit ang admin account
3. I-check kung gumagana ang database connection
4. Subukan ang iba pang features

---

## 🔧 Mabilis na Troubleshooting

**Hindi makakonekta ang database?**
- Tiyakin na tama ang syntax ng environment variables: `${{MySQL.Variable}}`
- I-verify na running ang MySQL service (green status)
- I-check ang Railway logs para sa errors
- Tiyakin na pareho ang project ng web service at MySQL service

**404 errors o "directory iwadco does not exist"?**
- **Option 1:** I-set ang **Root Directory** sa `iwadco` at Start Command sa `php -S 0.0.0.0:$PORT`
- **Option 2:** I-set ang Start Command sa `cd iwadco && php -S 0.0.0.0:$PORT` (Root Directory blangko)
- I-check kung may `index.php` sa `iwadco/` folder
- I-verify na na-push na ang `iwadco` folder sa GitHub
- I-redeploy ang service pagkatapos mag-edit ng settings

**Blank page o errors?**
- I-check ang Railway logs (web service → Logs tab)
- I-verify na na-import ang database
- Tiyakin na tama ang database credentials

**Kailangan ng mas detalyadong guide?** Tingnan ang `RAILWAY_DEPLOYMENT.md`

---

## 📝 Checklist

Bago ka mag-deploy, tiyakin na:

- [ ] Na-push na sa GitHub
- [ ] Na-create na ang Railway project
- [ ] Na-add na ang MySQL database
- [ ] Na-configure na ang environment variables
- [ ] Na-import na ang SQL file
- [ ] Na-ayos na ang admin email
- [ ] Na-verify na successful ang import
- [ ] Na-deploy na ang application
- [ ] Gumagana na ang login
- [ ] Gumagana na ang database connection

**Good luck sa deployment! 🚀**
