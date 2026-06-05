#!/bin/bash
export SSHPASS="Zine2026$"
sshpass -e ssh -o StrictHostKeyChecking=no -p 65002 u346640129@147.93.54.167 << 'REMOTE'
set -e
echo "🚀 Starting deployment..."
cd domains/zintoop.com/public_html
echo "🧹 Removing conflicting untracked files..."
rm -f extract_articles.php update_article2_remote.php update_articles_remote.php visitor_analytics.php visitor_analytics2.php
echo "📦 Pulling latest code..."
git reset --hard HEAD
git pull origin main
echo "🔨 Building assets..."
npm install
npm run build
echo "🧹 Clearing caches..."
php artisan view:clear
php artisan config:clear
php artisan cache:clear
echo "♻️ Restarting PHP..."
killall -9 lsphp || true
echo "✅ Deployment completed successfully"
REMOTE
