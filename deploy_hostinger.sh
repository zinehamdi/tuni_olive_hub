#!/bin/bash
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
