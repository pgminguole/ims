#!/bin/bash

# DeferPay CloudPanel Deployment Script
# Usage: ./cloudpanel_deploy.sh

set -e

# ==============================================================================
# CONFIGURATION - PLEASE FILL THESE IN
# ==============================================================================
SSH_USER="lawlegalguide-ims"
SSH_HOST="89.116.22.231"
SSH_PORT="22" # Default SSH port, change if necessary
APP_DOMAIN="ims.lawlegalguide.com"
# CloudPanel standard path for the specific site user
REMOTE_PATH="/home/$SSH_USER/htdocs/$APP_DOMAIN/"
LOCAL_PATH="$(pwd)/"
# ==============================================================================

echo "🚀 Starting deployment to CloudPanel ($APP_DOMAIN)..."

# Step 0: Build assets locally (Optional/Recommended)
if [ -f "package.json" ]; then
    echo "📦 Building assets locally with Vite..."
    npm install && npm run build
fi

# Step 1: Sync files
echo "📁 Syncing files to $REMOTE_PATH..."
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
  "$SSH_USER@$SSH_HOST:$REMOTE_PATH"

# Step 2: Run remote commands
echo "🔧 Running remote setup commands..."
ssh -p $SSH_PORT "$SSH_USER@$SSH_HOST" << EOF
cd $REMOTE_PATH

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Clear and cache config
echo "⚙️ Optimizing application..."
# Ensure SESSION_SECURE_COOKIE is false to prevent 419 errors if SSL mismatch
echo "🔧 Configuring session security..."
if grep -q "SESSION_SECURE_COOKIE" .env; then
    sed -i 's/SESSION_SECURE_COOKIE=true/SESSION_SECURE_COOKIE=false/g' .env
else
    echo "SESSION_SECURE_COOKIE=false" >> .env
fi

# Clear and cache config
echo "⚙️ Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Set proper permissions
echo "🔐 Setting permissions..."
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/


# Run migrations (if needed)
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Seed database to update content
echo "🌱 Seeding database..."
php artisan db:seed --force


# Restart queue workers
echo "🔄 Restarting queue workers..."
php artisan queue:restart

# If you use the keep-alive script
if [ -f "keep-queue-alive.sh" ]; then
    echo "🏃 Ensuring queue worker is running..."
    chmod +x keep-queue-alive.sh
    ./keep-queue-alive.sh
fi

echo "✅ Deployment completed successfully!"
EOF

echo "🎉 Deployment finished! Your application should now be live."
echo "🌐 Visit: https://$APP_DOMAIN"
