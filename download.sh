#!/bin/bash

# LawLegalGuide Download Script
# Usage: ./download.sh
# This script pulls files from the remote production server to the local project directory.

set -e

# Configuration
LOCAL_PATH="/Users/apple/Herd/projects/ims/"
REMOTE_USER="u350944503"
REMOTE_HOST="lawlegalguide.com"
REMOTE_PATH="/home/u350944503/domains/lawlegalguide.com/public_html/ims/"
SSH_PORT="65002"

echo "📥 Starting download from production server..."

# Step 1: Pull files using rsync
# We use -avz to preserve permissions, times, and compress data during transfer.
# We exclude files that shouldn't be overwritten or are environment-specific.
echo "📁 Syncing files from remote to local..."

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
  "$REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH" \
  "$LOCAL_PATH"

echo "✅ Download completed successfully!"
echo "📄 Files have been synced to: $LOCAL_PATH"
