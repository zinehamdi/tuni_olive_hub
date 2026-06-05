#!/bin/bash
export SSHPASS="Zine2026$"
sshpass -e ssh -o StrictHostKeyChecking=no -p 65002 u346640129@147.93.54.167 << 'REMOTE'
set -e
echo "🚀 Running database migrations..."
cd domains/zintoop.com/public_html
php artisan migrate --force
echo "✅ Migrations completed successfully"
REMOTE
