# Hostinger Deployment Guide - دليل النشر على Hostinger

## 🚀 Complete Deployment Guide

**Platform**: Hostinger  
**Framework**: Laravel 12  
**Database**: MySQL  
**Date**: October 16, 2025

---

## Part 1: Pre-Deployment Preparation 📋

### Step 1: Complete Testing
- [ ] ✅ Complete `PRE_DEPLOYMENT_TESTING.md` checklist
- [ ] ✅ Fix all critical issues
- [ ] ✅ Optimize images (26MB → 1MB)
- [ ] ✅ Clear all caches locally

### Step 2: Optimize Application
```bash
# 1. Optimize images (CRITICAL!)
./optimize-images.sh --replace

# 2. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Run production optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Build production assets
npm run build
```

### Step 3: Update Environment File

Create production `.env`:

```env
APP_NAME="Tunisian Olive Hub"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE  # Keep from local .env
APP_DEBUG=false  # IMPORTANT: false in production!
APP_TIMEZONE=UTC
APP_URL=https://yourdomain.com  # Your Hostinger domain

APP_LOCALE=ar
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=ar_TN

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

# Database (Get from Hostinger)
DB_CONNECTION=mysql
DB_HOST=localhost  # Usually localhost on Hostinger
DB_PORT=3306
DB_DATABASE=your_database_name  # From Hostinger
DB_USERNAME=your_database_user  # From Hostinger
DB_PASSWORD=your_database_password  # From Hostinger

# Session & Cache
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
CACHE_STORE=database
CACHE_PREFIX=

# Mail (Configure later)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# Security
VITE_APP_NAME="${APP_NAME}"
```

---

## Part 2: Prepare Deployment Package 📦

### Method A: Create Clean ZIP (Recommended)

```bash
# 1. Create temporary directory
mkdir ~/tuni-olive-hub-deploy
cd ~/tuni-olive-hub-deploy

# 2. Copy necessary files
rsync -av --exclude-from='../.gitignore' \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  ../tuni-olive-hub/ ./

# 3. Create storage directories
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache

# 4. Set proper permissions structure
chmod -R 755 storage bootstrap/cache

# 5. Create .htaccess for public folder (if not exists)
cat > public/.htaccess << 'EOF'
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
EOF

# 6. Create ZIP file
zip -r ../tuni-olive-hub-production.zip . -x "*.git*"

echo "✅ Deployment package created: ~/tuni-olive-hub-production.zip"
```

### Method B: Manual File Selection

**Include these folders/files:**
```
✅ app/
✅ bootstrap/
✅ config/
✅ database/ (migrations, seeders, factories)
✅ public/ (with compiled assets from build)
✅ resources/ (views, lang files)
✅ routes/
✅ storage/ (framework/, app/public/)
✅ vendor/ (if not running composer on server)
✅ artisan
✅ composer.json
✅ composer.lock
✅ package.json (optional)
✅ .htaccess (root)
✅ .env.example
```

**Exclude these:**
```
❌ .git/
❌ node_modules/
❌ .env (create new on server)
❌ storage/logs/* (keep directory, remove files)
❌ storage/framework/cache/*
❌ storage/framework/sessions/*
❌ storage/framework/views/*
❌ tests/
❌ .phpunit.xml
❌ *.md files (optional, keep for reference)
```

---

## Part 3: Hostinger Setup 🔧

### Step 1: Create MySQL Database

1. **Login to Hostinger hPanel**
2. Go to **Databases → MySQL Databases**
3. Click **Create Database**
   - Database name: `u123456_tuniolive` (example)
   - Click **Create**
4. **Create Database User**
   - Username: `u123456_admin` (example)
   - Password: [generate strong password]
   - Click **Create User**
5. **Add User to Database**
   - Select user and database
   - Grant **All Privileges**
   - Click **Add**
6. **Note down credentials** for `.env` file

### Step 2: Configure PHP Settings

1. Go to **Advanced → PHP Configuration**
2. Set **PHP Version**: 8.2 or 8.3
3. Update **PHP Options**:
   ```
   memory_limit = 256M
   max_execution_time = 300
   max_input_time = 300
   upload_max_filesize = 10M
   post_max_size = 10M
   ```
4. **Enable PHP Extensions**:
   - ✅ BCMath
   - ✅ Ctype
   - ✅ Fileinfo
   - ✅ JSON
   - ✅ Mbstring
   - ✅ OpenSSL
   - ✅ PDO
   - ✅ PDO_MySQL
   - ✅ Tokenizer
   - ✅ XML
   - ✅ GD (for image processing)
   - ✅ Zip

### Step 3: Upload Files

**Option A: File Manager (Recommended for beginners)**

1. Go to **Files → File Manager**
2. Navigate to `public_html/` (or your domain folder)
3. **Delete default files** (index.html, etc.)
4. Click **Upload**
5. Upload `tuni-olive-hub-production.zip`
6. Right-click → **Extract**
7. Move all files from extracted folder to root (public_html)

**Option B: FTP (Faster for large files)**

1. Get FTP credentials from **Files → FTP Accounts**
2. Use FileZilla or similar FTP client
3. Connect to Hostinger
4. Upload all files to `public_html/`

---

## Part 4: Configure Laravel on Hostinger 🛠️

### Step 1: Update Document Root

**CRITICAL: Point domain to public folder**

**Method A: Via hPanel (Recommended)**
1. Go to **Hosting → Manage**
2. Click **Advanced → Set Document Root**
3. Set to: `public_html/public`
4. Save

**Method B: Create .htaccess in root**

If you can't change document root, create `.htaccess` in `public_html/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Step 2: Create .env File

1. In File Manager, navigate to project root
2. Copy `.env.example` → `.env`
3. Edit `.env` with your database credentials:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456_tuniolive  # Your database name
DB_USERNAME=u123456_admin  # Your database user
DB_PASSWORD=your_password  # Your database password
```

### Step 3: Set Permissions

Via File Manager or SSH:

```bash
# Make storage and bootstrap/cache writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Or via File Manager: Right-click → Permissions → 775
```

### Step 4: Create Storage Symlink

**Via SSH** (if available):
```bash
cd /home/u123456/domains/yourdomain.com/public_html
php artisan storage:link
```

**Via File Manager** (if no SSH):
1. Navigate to `public/`
2. Right-click → **Create Symbolic Link**
3. Link name: `storage`
4. Link target: `../storage/app/public`

**Manual method** (if above don't work):
Create `public/storage-symlink.php`:
```php
<?php
$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

if (!file_exists($link)) {
    symlink($target, $link);
    echo "Storage link created successfully!";
} else {
    echo "Storage link already exists.";
}
```
Visit: `https://yourdomain.com/storage-symlink.php`  
Then delete the file.

### Step 5: Run Migrations

**Via SSH**:
```bash
cd /home/u123456/domains/yourdomain.com/public_html
php artisan migrate --force
```

**Via Web Interface** (if no SSH):

Create `public/migrate.php`:
```php
<?php
// SECURITY: Delete this file after use!
define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArrayInput([
        'command' => 'migrate',
        '--force' => true,
    ]),
    new Symfony\Component\Console\Output\BufferedOutput
);

echo "<pre>";
echo $kernel->output();
echo "</pre>";
```

Visit: `https://yourdomain.com/migrate.php`  
**DELETE THIS FILE IMMEDIATELY AFTER USE!**

### Step 6: Optimize for Production

**Via SSH**:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Via web** (create `public/optimize.php`):
```php
<?php
// DELETE THIS FILE AFTER USE!
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Caching config...\n";
$kernel->call('config:cache');
echo "Caching routes...\n";
$kernel->call('route:cache');
echo "Caching views...\n";
$kernel->call('view:cache');
echo "✅ Optimization complete!";
```

---

## Part 5: Configure Email (Optional) 📧

### Using Hostinger Email

1. **Create Email Account**
   - Go to **Emails → Create Email**
   - Email: `noreply@yourdomain.com`
   - Password: [strong password]

2. **Update .env**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Tunisian Olive Hub"
```

3. **Test Email**:
```php
// Create public/test-email.php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

Mail::raw('Test email from Tunisian Olive Hub', function($msg) {
    $msg->to('your@email.com')->subject('Test Email');
});

echo "Email sent! Check your inbox.";
// DELETE THIS FILE AFTER TESTING!
```

---

## Part 6: Security Configuration 🔒

### 1. Secure .env File

Create/update `.htaccess` in project root:

```apache
# Deny access to .env
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>

# Deny access to sensitive files
<FilesMatch "(composer\.json|composer\.lock|package\.json|\.git)">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 2. Force HTTPS

Add to `public/.htaccess` (top of file):

```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Update `.env`:
```env
APP_URL=https://yourdomain.com
```

### 3. Disable Directory Listing

Add to `public/.htaccess`:
```apache
Options -Indexes
```

### 4. Set Secure Headers

Add to `public/.htaccess`:
```apache
# Security Headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
```

---

## Part 7: Post-Deployment Testing ✅

### Immediate Checks

1. **Visit your domain**: `https://yourdomain.com`
   - [ ] ✅ Homepage loads
   - [ ] ✅ No errors displayed
   - [ ] ✅ Images load
   - [ ] ✅ Navigation works

2. **Test Registration**:
   - [ ] ✅ Register new account
   - [ ] ✅ Redirects to dashboard
   - [ ] ✅ Data saved to database

3. **Test Login**:
   - [ ] ✅ Login works
   - [ ] ✅ Session persists

4. **Test File Uploads**:
   - [ ] ✅ Profile picture upload
   - [ ] ✅ Cover photos upload
   - [ ] ✅ Images display correctly

5. **Check SSL**:
   - [ ] ✅ HTTPS works
   - [ ] ✅ No mixed content warnings
   - [ ] ✅ Padlock shows in browser

6. **Mobile Test**:
   - [ ] ✅ Responsive design works
   - [ ] ✅ Menu closes by default
   - [ ] ✅ Images load

7. **Performance Test**:
   - [ ] ✅ Page loads < 3 seconds
   - [ ] ✅ Total size < 2MB
   - [ ] ✅ No console errors

---

## Part 8: Troubleshooting Common Issues 🔧

### Issue 1: "500 Internal Server Error"

**Solutions**:
```bash
# Check error logs
# Hostinger: Advanced → Error Logs

# Common causes:
1. Wrong .env configuration
2. Missing .htaccess in public/
3. Wrong file permissions
4. Missing PHP extensions

# Fix permissions:
chmod -R 775 storage bootstrap/cache

# Clear caches:
php artisan cache:clear
php artisan config:clear
```

### Issue 2: "404 Not Found" on All Pages

**Solutions**:
```apache
# Ensure .htaccess exists in public/ folder
# Enable mod_rewrite in PHP settings

# Add to public/.htaccess:
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    # ... rest of Laravel .htaccess
</IfModule>
```

### Issue 3: Images Not Displaying

**Solutions**:
```bash
# 1. Check storage symlink
ls -la public/storage  # Should point to ../storage/app/public

# 2. Recreate symlink
php artisan storage:link

# 3. Check permissions
chmod -R 775 storage/app/public
```

### Issue 4: CSS/JS Not Loading

**Solutions**:
```bash
# 1. Check if assets exist in public/build/
ls -la public/build/assets/

# 2. Re-run build locally and re-upload
npm run build

# 3. Check .env has:
VITE_APP_NAME="${APP_NAME}"
```

### Issue 5: Database Connection Error

**Solutions**:
```env
# Verify .env database settings
DB_HOST=localhost  # Try 'localhost' or '127.0.0.1'
DB_PORT=3306
DB_DATABASE=correct_database_name
DB_USERNAME=correct_username
DB_PASSWORD=correct_password

# Clear config cache:
php artisan config:clear
```

---

## Part 9: Maintenance & Updates 🔄

### Regular Maintenance

**Weekly**:
```bash
# Check error logs
# Monitor storage usage
du -sh storage/

# Clear old sessions (if database)
php artisan session:gc
```

**Monthly**:
```bash
# Update dependencies (test locally first!)
composer update
npm update
npm run build

# Re-upload vendor/ and public/build/
```

### Backup Strategy

**Database Backup** (via Hostinger):
1. Go to **Databases → Backups**
2. Download backup
3. Store securely

**Files Backup**:
1. Download `storage/app/` folder
2. Download `.env` file
3. Store securely

**Automated Backups**:
- Enable automatic backups in Hostinger
- Schedule: Daily or Weekly

---

## Part 10: Performance Optimization 🚀

### 1. Enable OPcache

Via PHP Configuration:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
```

### 2. Enable Gzip Compression

Add to `.htaccess`:
```apache
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>
```

### 3. Browser Caching

Add to `public/.htaccess`:
```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

### 4. Database Optimization

```bash
# Run once a month
php artisan optimize:clear
php artisan optimize
```

---

## Deployment Checklist Summary

### Pre-Deployment:
- [ ] ✅ All functionality tested
- [ ] ✅ Images optimized (26MB → 1MB)
- [ ] ✅ Production .env prepared
- [ ] ✅ Assets compiled (`npm run build`)
- [ ] ✅ Deployment ZIP created

### During Deployment:
- [ ] ✅ Database created on Hostinger
- [ ] ✅ PHP version and extensions configured
- [ ] ✅ Files uploaded via FTP/File Manager
- [ ] ✅ Document root set to `/public`
- [ ] ✅ .env file configured
- [ ] ✅ Permissions set (775 on storage/)
- [ ] ✅ Storage symlink created
- [ ] ✅ Migrations run
- [ ] ✅ Caches optimized

### Post-Deployment:
- [ ] ✅ Homepage loads
- [ ] ✅ Authentication works
- [ ] ✅ File uploads work
- [ ] ✅ HTTPS enabled
- [ ] ✅ Mobile responsive
- [ ] ✅ Performance acceptable (< 3s load)
- [ ] ✅ No errors in logs
- [ ] ✅ Backup configured

---

## Quick Commands Reference

```bash
# Optimization
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Database
php artisan migrate --force
php artisan db:seed --force

# Storage
php artisan storage:link

# Check application
php artisan about
```

---

## Support Resources

- **Hostinger Support**: https://www.hostinger.com/tutorials
- **Laravel Deployment**: https://laravel.com/docs/11.x/deployment
- **Your Documentation**: Check all .md files in project

---

**Deployment Date**: _________________  
**Deployed By**: _________________  
**Domain**: _________________  
**Status**: ✅ / ❌

