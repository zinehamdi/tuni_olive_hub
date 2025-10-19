# Quick Deployment to Hostinger - Step by Step

## 🚀 3 Simple Methods (Choose One)

---

## ⚡ METHOD 1: Hostinger GitHub Auto-Deploy (EASIEST)

### ✅ Prerequisites
- Hostinger Business or Premium plan (supports Git)
- Your GitHub repo: `https://github.com/zinehamdi/tuni_olive_hub`

### 📝 Steps

1. **Login to Hostinger Panel**
   ```
   https://hpanel.hostinger.com
   ```

2. **Navigate to Git**
   ```
   Hosting → Manage → Advanced → Git
   ```

3. **Connect GitHub**
   - Click "Connect GitHub Account"
   - Authorize Hostinger

4. **Create Deployment**
   - Repository: `zinehamdi/tuni_olive_hub`
   - Branch: `main`
   - Deploy to: `/public_html` (or your domain folder)
   - Enable: "Auto-deploy on push"

5. **First Deploy**
   - Click "Deploy Now"
   - Wait for deployment to complete

6. **Configure Environment**
   - Go to File Manager
   - Copy `.env.example` to `.env`
   - Edit `.env` with database credentials (see below)

7. **Run Setup Commands**
   - Go to: SSH Access → Enable SSH
   - Connect via terminal:
     ```bash
     ssh -p 65002 username@ssh.hostinger.com
     cd domains/yourdomainname.com/public_html
     
     # Generate key
     php artisan key:generate
     
     # Run migrations
     php artisan migrate --force
     
     # Optimize
     php artisan optimize
     ```

8. **Done!** 🎉
   - Visit: `https://yourdomain.com`
   - Future updates: Just `git push` and Hostinger auto-deploys!

---

## 🔧 METHOD 2: SSH + Git Clone (RECOMMENDED)

### 📝 Steps

1. **Enable SSH on Hostinger**
   ```
   Hostinger Panel → Advanced → SSH Access → Enable
   ```

2. **Connect via Terminal**
   ```bash
   ssh -p 65002 your_username@ssh.hostinger.com
   ```

3. **Clone Repository**
   ```bash
   cd domains/yourdomainname.com
   rm -rf public_html
   git clone https://github.com/zinehamdi/tuni_olive_hub.git public_html
   cd public_html
   ```

4. **Install Dependencies**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

5. **Configure Environment**
   ```bash
   cp .env.example .env
   nano .env  # Edit with database credentials
   ```

6. **Run Setup**
   ```bash
   php artisan key:generate
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache
   php artisan migrate --force
   php artisan optimize
   ```

7. **Upload Pre-built Assets** (via FTP)
   - Copy `public/build` folder from your local machine
   - Upload to server's `public/build`

8. **Done!** 🎉

---

## 📁 METHOD 3: FTP Upload (IF NO SSH)

### 📝 Steps

1. **Prepare Locally**
   ```bash
   cd /Users/zinehamdi/Sites/localhost/tuni-olive-hub
   
   # Build assets
   npm run build
   
   # Install production dependencies
   composer install --no-dev --optimize-autoloader
   ```

2. **Connect FTP Client**
   - Download: FileZilla or Cyberduck
   - Host: `ftp.yourdomain.com`
   - Username: From Hostinger panel
   - Password: From Hostinger panel
   - Port: 21

3. **Upload All Files**
   - Upload entire project folder to `/public_html`
   - Include hidden files (`.htaccess`, `.env.example`)

4. **Configure via File Manager**
   - Hostinger Panel → File Manager
   - Copy `.env.example` → `.env`
   - Edit `.env` with database credentials
   - Set permissions:
     - `storage`: 775
     - `bootstrap/cache`: 775

5. **Use Online Terminal** (if available)
   ```
   Hostinger Panel → Advanced → Terminal
   ```
   Run:
   ```bash
   php artisan key:generate
   php artisan migrate --force
   php artisan optimize
   ```

6. **Done!** 🎉

---

## 🗄️ Database Configuration

### Get Database Credentials

1. **Hostinger Panel → Databases**
2. **Create New Database**
   - Name: `u123_toop` (Hostinger adds prefix)
   - User: Create or use existing
   - Password: Strong password

3. **Update `.env`**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u123_toop
   DB_USERNAME=u123_toop_user
   DB_PASSWORD=your_password
   ```

---

## 🔒 SSL Certificate

1. **Hostinger Panel → SSL**
2. **Click "Install SSL"** (Free Let's Encrypt)
3. **Enable "Force HTTPS"**

---

## ⚙️ Document Root Configuration

**Important**: Laravel's entry point is the `public` folder

### Option A: Configure in Hostinger Panel
```
Hosting → Manage → Advanced → PHP Configuration
Document Root: public_html/public
```

### Option B: Use .htaccess in Root
Create `.htaccess` in `public_html`:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## ✅ Post-Deployment Checklist

- [ ] `.env` configured correctly
- [ ] Database created and connected
- [ ] Migrations run successfully
- [ ] SSL certificate installed
- [ ] Document root set to `public`
- [ ] Test login: `https://yourdomain.com/login`
- [ ] Test register: `https://yourdomain.com/register`
- [ ] Test image upload (profile picture)
- [ ] Test phone login: `22123123`
- [ ] Test language switcher (AR/FR/EN)

---

## 🧪 Quick Test URLs

After deployment, test these:

```
Homepage:       https://yourdomain.com
About:          https://yourdomain.com/about
Login:          https://yourdomain.com/login
Register:       https://yourdomain.com/register
Dashboard:      https://yourdomain.com/dashboard (after login)
```

---

## 🔄 Update After Changes

### Method 1 (Auto-Deploy):
```bash
git add .
git commit -m "Update"
git push origin main
# Hostinger auto-deploys! ✨
```

### Method 2 (SSH):
```bash
ssh -p 65002 username@ssh.hostinger.com
cd domains/yourdomainname.com/public_html
git pull origin main
composer install --no-dev
php artisan migrate --force
php artisan optimize
```

### Method 3 (FTP):
- Upload changed files via FTP
- Clear cache via File Manager or Terminal

---

## 🆘 Quick Troubleshooting

**500 Error?**
```bash
chmod -R 775 storage bootstrap/cache
php artisan config:clear
```

**Database Error?**
- Check `.env` DB credentials
- Verify database exists in Hostinger panel

**Images Not Loading?**
```bash
php artisan storage:link
```

**CSS/JS Not Loading?**
- Upload `public/build` folder
- Clear browser cache

---

## 📞 Need Help?

1. **Check full guide**: `HOSTINGER_DEPLOYMENT_GUIDE.md`
2. **Hostinger Support**: https://www.hostinger.com/cpanel-login
3. **Developer**: Zinehamdi8@gmail.com

---

**Estimated Time**: 15-30 minutes  
**Difficulty**: ⭐⭐☆☆☆ (Easy with auto-deploy)  
**Status**: Ready to Deploy! 🚀
