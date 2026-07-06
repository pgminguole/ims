#!/bin/bash

# LawLegalGuide Deployment Script
# Usage: ./deploy.sh

set -e

# Configuration
LOCAL_PATH="/Users/apple/Herd/projects/ims/"
REMOTE_USER="u350944503"
REMOTE_HOST="lawlegalguide.com"
REMOTE_PATH="/home/u350944503/domains/lawlegalguide.com/public_html/ims/"
SSH_PORT="65002"

echo "🚀 Starting deployment to production server..."

# Step 0: Backup remote database
echo "💾 Creating remote database backup..."
ssh -p $SSH_PORT "$REMOTE_USER@$REMOTE_HOST" << 'EOF'
cd /home/u350944503/domains/lawlegalguide.com/public_html/ims/
mkdir -p storage/app/backups
BACKUP_FILE="storage/app/backups/manual_backup_$(date +%Y%m%d_%H%M%S).sql"
mysqldump -u u350944503_ims -p'Tu6|W$KL8ks' u350944503_ims_db > "$BACKUP_FILE"
echo "✅ Backup created: $BACKUP_FILE"
EOF

# Step 1: Sync files
echo "📁 Syncing files..."
rsync -avz -e "ssh -p $SSH_PORT" \
  --exclude='.env' \
  --exclude='.env.local' \
  --exclude='.htaccess' \
  --exclude='node_modules/' \
  --exclude='.claude/' \
  --exclude='*.zip' \
  --exclude='.git/' \
  --exclude='.DS_Store' \
  --exclude='storage/logs/*.log' \
  --exclude='storage/framework/cache/data/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='bootstrap/cache/*.php' \
  --exclude='vendor/' \
  --exclude='public/storage' \
  --progress \
  "$LOCAL_PATH" \
  "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH"

# Step 2: Run remote commands
echo "🔧 Running remote setup commands..."
ssh -p $SSH_PORT "$REMOTE_USER@$REMOTE_HOST" << 'EOF'
cd /home/u350944503/domains/lawlegalguide.com/public_html/ims/

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Clear and cache config
echo "⚙️ Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
# Use a more portable way to check for existing group or just skip group if problematic
chown -R $USER storage/ 2>/dev/null || echo "Note: chown failed, skipping..."
chown -R $USER bootstrap/cache/ 2>/dev/null || echo "Note: chown failed, skipping..."

# Run migrations (if needed)
echo "🗄️ Running database migrations..."
php artisan migrate --force

echo "✅ Deployment completed successfully!"
EOF

echo "🎉 Deployment finished! Your application should now be live."
echo "🌐 Visit: https://lawlegalguide.com"