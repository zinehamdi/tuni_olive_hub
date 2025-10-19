# Hostinger Deployment Guide - GitHub Integration

**Date**: October 18, 2025  
**Project**: Tunisian Olive Oil Platform (TOOP)  
**Repository**: https://github.com/zinehamdi/tuni_olive_hub

---

## 🎯 Prerequisites

- ✅ GitHub repository with latest code (DONE)
- ✅ Hostinger hosting account with SSH access
- ✅ Domain configured (or use temporary Hostinger domain)
- ✅ PHP 8.1+ support on Hostinger
- ✅ MySQL database access

---

## 📋 Method 1: Deploy via Git (Recommended)

### Step 1: Access Hostinger SSH

1. **Login to Hostinger Panel**
   - Go to: https://hpanel.hostinger.com
   - Navigate to: **Hosting → Manage → Advanced → SSH Access**

2. **Enable SSH Access**
   - Toggle "SSH Access" to ON
   - Copy your SSH credentials:
     - Host: `ssh.hostinger.com` (or specific server)
     - Port: `65002` (default)
     - Username: Your Hostinger username
     - Password: Your Hostinger password

3. **Connect via Terminal**
   ```bash
   ssh -p 65002 your_username@ssh.hostinger.com
   ```

### Step 2: Clone Repository on Hostinger

```bash
# Navigate to public_html or your domain folder
cd domains/yourdomainname.com/public_html

# Clone your GitHub repository
git clone https://github.com/zinehamdi/tuni_olive_hub.git temp_clone

# Move files from temp directory to current directory
shopt -s dotglob  # Include hidden files
mv temp_clone/* .
rm -rf temp_clone

# Or clone directly (if folder is empty)
cd ..
rm -rf public_html
git clone https://github.com/zinehamdi/tuni_olive_hub.git public_html
cd public_html
```

### Step 3: Install Dependencies

```bash
# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# If composer not available globally, download it:
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"
php composer.phar install --no-dev --optimize-autoloader

# Install Node.js dependencies (if Node available)
npm install
npm run build

# If Node not available, upload pre-built assets from local
# (See Alternative Build Method below)
```

### Step 4: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit .env file
nano .env
```

**Update these settings in `.env`**:

```env
APP_NAME="Tunisian Olive Oil Platform"
APP_ENV=production
APP_KEY=  # Generate this in next step
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database Settings (get from Hostinger)
DB_CONNECTION=mysql
DB_HOST=localhost  # Or specific host from Hostinger
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Mail Settings (optional)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 5: Generate App Key

```bash
php artisan key:generate
```

### Step 6: Set Permissions

```bash
# Set correct permissions
chmod -R 775 storage bootstrap/cache
chmod -R 775 public

# Create symbolic link for storage (if not exists)
php artisan storage:link
```

### Step 7: Run Migrations

```bash
# Run database migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --force
```

### Step 8: Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Clear any development caches
php artisan optimize
```

### Step 9: Configure Web Server

**Update `.htaccess` in public folder** (if not working):

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Security Headers
<IfModule mod_headers.c>
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>

# PHP Settings
php_value upload_max_filesize 100M
php_value post_max_size 100M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 256M
```

**Update Document Root** (Hostinger Panel):
- Go to: **Hosting → Manage → Advanced → PHP Configuration**
- Set Document Root to: `public_html/public` (if your root is public_html)
- Or configure public folder as the entry point

---

## 📋 Method 2: Deploy via FTP/SFTP

If SSH is not available or you prefer GUI:

### Step 1: Download Repository

```bash
# On your local machine
cd ~/Downloads
git clone https://github.com/zinehamdi/tuni_olive_hub.git
cd tuni_olive_hub

# Build assets locally
npm install
npm run build

# Install production dependencies
composer install --no-dev --optimize-autoloader
```

### Step 2: Upload via FileZilla/FTP

1. **Connect to Hostinger FTP**
   - Host: `ftp.yourdomain.com` or IP from Hostinger
   - Username: Your FTP username
   - Password: Your FTP password
   - Port: 21 (FTP) or 22 (SFTP)

2. **Upload Files**
   - Upload ALL files from local `tuni_olive_hub` folder
   - Upload to: `/public_html` or your domain folder
   - **Important**: Upload hidden files too (`.env.example`, `.htaccess`)

3. **Configure on Server**
   - Use Hostinger File Manager
   - Copy `.env.example` to `.env`
   - Edit `.env` with database credentials
   - Set permissions (via File Manager: right-click → Permissions)
     - `storage`: 775
     - `bootstrap/cache`: 775

---

## 📋 Method 3: Hostinger Git Deployment (Auto-Deploy)

Hostinger supports automatic deployments from GitHub!

### Step 1: Enable Git on Hostinger

1. **Login to Hostinger Panel**
   - Go to: **Hosting → Manage → Advanced**
   - Find: **Git** or **GitHub Integration**

2. **Connect GitHub Account**
   - Click "Connect GitHub"
   - Authorize Hostinger to access your repositories

3. **Select Repository**
   - Choose: `zinehamdi/tuni_olive_hub`
   - Branch: `main`
   - Deploy Path: `/public_html` or your domain folder

4. **Configure Auto-Deploy**
   - Enable: "Auto-deploy on push"
   - This will automatically deploy when you push to GitHub!

### Step 2: Add Deployment Script

Create `deploy.sh` in your repository root (optional):

```bash
#!/bin/bash

echo "🚀 Starting deployment..."

# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Build assets (if Node available)
if command -v npm &> /dev/null
then
    npm install
    npm run build
fi

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "✅ Deployment complete!"
```

Make it executable:
```bash
chmod +x deploy.sh
```

---

## 🗄️ Database Setup

### Create Database on Hostinger

1. **Login to Hostinger Panel**
   - Go to: **Hosting → Manage → Databases**

2. **Create New Database**
   - Click "Create Database"
   - Database Name: `u123456789_toop` (Hostinger adds prefix)
   - Database User: Create new or use existing
   - Password: Strong password
   - Click "Create"

3. **Import Database** (if you have existing data)
   - Go to **phpMyAdmin** (link in Databases section)
   - Select your database
   - Click "Import"
   - Upload your `.sql` file
   - Click "Go"

4. **Or Run Migrations**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

---

## 🔧 Post-Deployment Configuration

### 1. Set Up Cron Jobs (for scheduled tasks)

**Hostinger Panel → Advanced → Cron Jobs**

Add this cron job:
```
* * * * * cd /home/username/domains/yourdomain.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Configure Email

Update `.env` with Hostinger SMTP:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your@email.com
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### 3. Set Up SSL Certificate

1. **Hostinger Panel → SSL**
2. Click "Install SSL" (free Let's Encrypt)
3. Enable "Force HTTPS"

### 4. Create Admin User

```bash
php artisan make:admin your@email.com
# Or run your seeder
```

---

## ✅ Deployment Checklist

Before going live:

- [ ] `.env` configured with production settings
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Database created and migrated
- [ ] Storage permissions set (775)
- [ ] SSL certificate installed
- [ ] HTTPS forced
- [ ] Cron jobs configured
- [ ] Email tested
- [ ] File uploads working (test with profile image)
- [ ] All pages loading correctly
- [ ] Login/Register working
- [ ] Admin panel accessible
- [ ] Mobile responsiveness tested

---

## 🧪 Testing After Deployment

1. **Homepage**: `https://yourdomain.com`
2. **Login**: `https://yourdomain.com/login`
3. **Register**: `https://yourdomain.com/register`
4. **Dashboard**: `https://yourdomain.com/dashboard`
5. **Admin**: `https://yourdomain.com/admin`
6. **About**: `https://yourdomain.com/about`

Test each feature:
- ✅ Login with email
- ✅ Login with phone (22123123)
- ✅ Image uploads
- ✅ Product listings
- ✅ Language switcher (AR/FR/EN)

---

## 🔄 Update Deployment (After Changes)

### If Using Git on Server:

```bash
# SSH into server
ssh -p 65002 username@ssh.hostinger.com

# Navigate to project
cd domains/yourdomain.com/public_html

# Pull latest changes
git pull origin main

# Install new dependencies
composer install --no-dev --optimize-autoloader

# Run migrations
php artisan migrate --force

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Build assets (if Node available)
npm install
npm run build
```

### If Using Auto-Deploy:

Just push to GitHub:
```bash
git add .
git commit -m "Your changes"
git push origin main
```

Hostinger will automatically deploy! 🎉

---

## 🚨 Troubleshooting

### Issue 1: 500 Internal Server Error

**Check**:
```bash
# View Laravel logs
tail -f storage/logs/laravel.log

# Check permissions
ls -la storage bootstrap/cache
```

**Fix**:
```bash
chmod -R 775 storage bootstrap/cache
php artisan cache:clear
php artisan config:clear
```

### Issue 2: Database Connection Error

**Check** `.env`:
- DB_HOST (might be `localhost` or specific IP)
- DB_DATABASE (check exact name in Hostinger)
- DB_USERNAME and DB_PASSWORD

### Issue 3: Images Not Loading

**Fix**:
```bash
php artisan storage:link
chmod -R 775 storage/app/public
```

### Issue 4: Assets Not Loading (CSS/JS)

**Check**:
- Run `npm run build` locally
- Upload `public/build` folder via FTP
- Clear browser cache

### Issue 5: .env Not Found

**Fix**:
```bash
cp .env.example .env
nano .env  # Edit with correct values
php artisan key:generate
```

---

## 📞 Support

**Hostinger Support**: https://www.hostinger.com/cpanel-login  
**Documentation**: Your project docs (60+ files)  
**Developer**: Hamdi Ezzine - Zinehamdi8@gmail.com

---

## 🎉 After Successful Deployment

1. **Update README.md** with live URL
2. **Share with users**
3. **Monitor error logs**: `storage/logs/laravel.log`
4. **Set up backups** (Hostinger provides automatic backups)
5. **Monitor performance** with Google Analytics

---

**Status**: 📝 Ready for Deployment  
**Last Updated**: October 18, 2025  
**Version**: 1.0.0
