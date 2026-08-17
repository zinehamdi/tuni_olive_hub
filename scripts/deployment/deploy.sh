#!/bin/bash

# Deployment Readiness Check Script
# تحقق من جاهزية النشر

echo "🔍 Tunisian Olive Hub - Deployment Readiness Check"
echo "=================================================="
echo ""

ERRORS=0
WARNINGS=0

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Check Laravel Version
echo "1️⃣  Checking Laravel Version..."
php artisan --version
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Laravel is working${NC}"
else
    echo -e "${RED}❌ Laravel command failed${NC}"
    ((ERRORS++))
fi
echo ""

# 2. Check PHP Version
echo "2️⃣  Checking PHP Version..."
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "PHP Version: $PHP_VERSION"
if [[ "$PHP_VERSION" > "8.1" ]]; then
    echo -e "${GREEN}✅ PHP version is compatible${NC}"
else
    echo -e "${RED}❌ PHP version should be 8.1+${NC}"
    ((ERRORS++))
fi
echo ""

# 3. Check Required PHP Extensions
echo "3️⃣  Checking Required PHP Extensions..."
REQUIRED_EXTENSIONS=("pdo" "mbstring" "openssl" "tokenizer" "xml" "ctype" "json" "bcmath" "fileinfo")

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -qi "^$ext$"; then
        echo -e "${GREEN}✅ $ext${NC}"
    else
        echo -e "${RED}❌ $ext (missing)${NC}"
        ((ERRORS++))
    fi
done
echo ""

# 4. Check .env File
echo "4️⃣  Checking .env Configuration..."
if [ -f ".env" ]; then
    echo -e "${GREEN}✅ .env file exists${NC}"
    
    # Check critical env variables
    if grep -q "APP_KEY=base64:" .env; then
        echo -e "${GREEN}✅ APP_KEY is set${NC}"
    else
        echo -e "${RED}❌ APP_KEY is not set${NC}"
        echo "   Run: php artisan key:generate"
        ((ERRORS++))
    fi
    
    if grep -q "APP_ENV=production" .env; then
        echo -e "${YELLOW}⚠️  APP_ENV is set to production${NC}"
    else
        echo -e "${YELLOW}⚠️  APP_ENV is not production (should be for deployment)${NC}"
        ((WARNINGS++))
    fi
    
    if grep -q "APP_DEBUG=false" .env; then
        echo -e "${GREEN}✅ APP_DEBUG is false${NC}"
    else
        echo -e "${YELLOW}⚠️  APP_DEBUG should be false in production${NC}"
        ((WARNINGS++))
    fi
    
    if grep -q "DB_DATABASE=" .env && ! grep -q "DB_DATABASE=$" .env; then
        echo -e "${GREEN}✅ Database configuration found${NC}"
    else
        echo -e "${RED}❌ Database not configured${NC}"
        ((ERRORS++))
    fi
else
    echo -e "${RED}❌ .env file not found${NC}"
    echo "   Copy .env.example to .env and configure it"
    ((ERRORS++))
fi
echo ""

# 5. Check Storage Permissions
echo "5️⃣  Checking Storage Permissions..."
if [ -d "storage" ]; then
    if [ -w "storage" ]; then
        echo -e "${GREEN}✅ storage/ is writable${NC}"
    else
        echo -e "${RED}❌ storage/ is not writable${NC}"
        echo "   Run: chmod -R 775 storage"
        ((ERRORS++))
    fi
else
    echo -e "${RED}❌ storage/ directory not found${NC}"
    ((ERRORS++))
fi

if [ -d "bootstrap/cache" ]; then
    if [ -w "bootstrap/cache" ]; then
        echo -e "${GREEN}✅ bootstrap/cache/ is writable${NC}"
    else
        echo -e "${RED}❌ bootstrap/cache/ is not writable${NC}"
        echo "   Run: chmod -R 775 bootstrap/cache"
        ((ERRORS++))
    fi
else
    echo -e "${RED}❌ bootstrap/cache/ directory not found${NC}"
    ((ERRORS++))
fi
echo ""

# 6. Check Storage Symlink
echo "6️⃣  Checking Storage Symlink..."
if [ -L "public/storage" ]; then
    echo -e "${GREEN}✅ Storage symlink exists${NC}"
else
    echo -e "${YELLOW}⚠️  Storage symlink not found${NC}"
    echo "   Run: php artisan storage:link"
    ((WARNINGS++))
fi
echo ""

# 7. Check Compiled Assets
echo "7️⃣  Checking Compiled Assets..."
if [ -d "public/build" ] && [ "$(ls -A public/build)" ]; then
    echo -e "${GREEN}✅ Compiled assets found${NC}"
    du -sh public/build
else
    echo -e "${RED}❌ Compiled assets not found${NC}"
    echo "   Run: npm run build"
    ((ERRORS++))
fi
echo ""

# 8. Check Image Sizes
echo "8️⃣  Checking Image Sizes (Performance Critical!)..."
if [ -d "public/images" ]; then
    LARGE_IMAGES=$(find public/images -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" \) -size +1M)
    
    if [ -z "$LARGE_IMAGES" ]; then
        echo -e "${GREEN}✅ All images are optimized (< 1MB)${NC}"
    else
        echo -e "${RED}❌ Large images found (> 1MB):${NC}"
        echo "$LARGE_IMAGES" | while read img; do
            SIZE=$(du -h "$img" | cut -f1)
            echo "   $img - $SIZE"
        done
        echo -e "${YELLOW}   Run: ./optimize-images.sh${NC}"
        ((WARNINGS++))
    fi
else
    echo -e "${YELLOW}⚠️  public/images/ directory not found${NC}"
fi
echo ""

# 9. Check Database Connection
echo "9️⃣  Checking Database Connection..."
if php artisan db:show > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Database connection successful${NC}"
else
    echo -e "${RED}❌ Cannot connect to database${NC}"
    echo "   Check your .env database credentials"
    ((ERRORS++))
fi
echo ""

# 10. Check Routes
echo "🔟 Checking Routes..."
ROUTE_COUNT=$(php artisan route:list --except-vendor 2>/dev/null | grep -c "GET\|POST\|PUT\|DELETE" || echo "0")
if [ "$ROUTE_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✅ $ROUTE_COUNT routes registered${NC}"
else
    echo -e "${RED}❌ No routes found${NC}"
    ((ERRORS++))
fi
echo ""

# 11. Check Vendor Dependencies
echo "1️⃣1️⃣  Checking Vendor Dependencies..."
if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
    echo -e "${GREEN}✅ Vendor dependencies installed${NC}"
else
    echo -e "${RED}❌ Vendor dependencies not found${NC}"
    echo "   Run: composer install --no-dev --optimize-autoloader"
    ((ERRORS++))
fi
echo ""

# 12. Check Migrations
echo "1️⃣2️⃣  Checking Migrations..."
MIGRATION_COUNT=$(find database/migrations -name "*.php" | wc -l)
if [ "$MIGRATION_COUNT" -gt 0 ]; then
    echo -e "${GREEN}✅ $MIGRATION_COUNT migration files found${NC}"
else
    echo -e "${YELLOW}⚠️  No migrations found${NC}"
    ((WARNINGS++))
fi
echo ""

# 13. Check .htaccess Files
echo "1️⃣3️⃣  Checking .htaccess Files..."
if [ -f "public/.htaccess" ]; then
    echo -e "${GREEN}✅ public/.htaccess exists${NC}"
else
    echo -e "${RED}❌ public/.htaccess not found${NC}"
    echo "   Laravel may not work without this file"
    ((ERRORS++))
fi
echo ""

# 14. Security Checks
echo "1️⃣4️⃣  Security Checks..."

# Check if .env is in .gitignore
if grep -q "\.env" .gitignore 2>/dev/null; then
    echo -e "${GREEN}✅ .env is in .gitignore${NC}"
else
    echo -e "${YELLOW}⚠️  .env should be in .gitignore${NC}"
    ((WARNINGS++))
fi

# Check if APP_DEBUG is false for production
if grep -q "APP_DEBUG=false" .env 2>/dev/null; then
    echo -e "${GREEN}✅ APP_DEBUG is false (production ready)${NC}"
else
    echo -e "${YELLOW}⚠️  APP_DEBUG should be false in production${NC}"
    ((WARNINGS++))
fi

# Check if sensitive directories are protected
if [ -f ".htaccess" ]; then
    if grep -q "\.env" .htaccess; then
        echo -e "${GREEN}✅ .htaccess protects .env${NC}"
    else
        echo -e "${YELLOW}⚠️  Add .env protection to .htaccess${NC}"
        ((WARNINGS++))
    fi
fi
echo ""

# 15. File Size Check for Deployment
echo "1️⃣5️⃣  Deployment Package Size..."
TOTAL_SIZE=$(du -sh . 2>/dev/null | cut -f1)
echo "Current directory size: $TOTAL_SIZE"

if [ -d "node_modules" ]; then
    NODE_SIZE=$(du -sh node_modules 2>/dev/null | cut -f1)
    echo -e "${YELLOW}⚠️  node_modules/ found ($NODE_SIZE) - exclude from deployment${NC}"
    ((WARNINGS++))
fi

if [ -d ".git" ]; then
    GIT_SIZE=$(du -sh .git 2>/dev/null | cut -f1)
    echo -e "${YELLOW}⚠️  .git/ found ($GIT_SIZE) - exclude from deployment${NC}"
    ((WARNINGS++))
fi
echo ""

# Summary
echo "=================================================="
echo "📊 SUMMARY"
echo "=================================================="

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✅ ALL CHECKS PASSED!${NC}"
    echo -e "${GREEN}🚀 Ready for deployment!${NC}"
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠️  $WARNINGS Warning(s) found${NC}"
    echo -e "${GREEN}✅ No critical errors${NC}"
    echo -e "${YELLOW}📋 Review warnings before deploying${NC}"
else
    echo -e "${RED}❌ $ERRORS Critical Error(s) found${NC}"
    echo -e "${YELLOW}⚠️  $WARNINGS Warning(s) found${NC}"
    echo -e "${RED}🛑 FIX ERRORS BEFORE DEPLOYING!${NC}"
fi

echo ""
echo "Next Steps:"
if [ $ERRORS -eq 0 ]; then
    echo "1. Fix any warnings (recommended)"
    echo "2. Run: npm run build (if not done)"
    echo "3. Optimize images: ./optimize-images.sh"
    echo "4. Create deployment package"
    echo "5. Follow DEPLOYMENT_GUIDE.md"
    echo ""
    echo "=================================================="
    echo "🌐 Starting Remote Deployment to Hostinger..."
    echo "=================================================="
    export SSHPASS="Zine2026$"
    sshpass -e ssh -o StrictHostKeyChecking=no -p 65002 u346640129@147.93.54.167 << 'REMOTE'
    set -e
    echo "🚀 Starting deployment..."
    cd domains/zintoop.com/public_html
    echo "📦 Pulling latest code..."
    git reset --hard HEAD
    git pull origin main
    echo "🗄️ Running database migrations..."
    php artisan migrate --force
    echo "🔨 Building assets..."
    npm run build
    echo "🧹 Clearing caches..."
    php artisan view:clear
    php artisan config:clear
    php artisan route:clear
    php artisan cache:clear
    echo "♻️ Restarting PHP..."
    killall -9 lsphp || true
    echo "✅ Deployment completed successfully"
REMOTE
else
    echo "1. Fix all critical errors above"
    echo "2. Re-run this script"
    echo "3. Once passing, proceed with deployment"
    echo ""
    echo "❌ Deployment aborted due to errors."
fi

exit $ERRORS
