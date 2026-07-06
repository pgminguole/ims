#!/bin/bash

# Production server details
REMOTE_USER="u350944503"
REMOTE_HOST="162.0.228.52"
SSH_PORT="65002"

echo "🔍 Checking comments column in production database..."

ssh -p $SSH_PORT "$REMOTE_USER@$REMOTE_HOST" << 'EOF'
cd /home/u350944503/domains/lawlegalguide.com/public_html/ims/

echo "📋 Checking if migration exists..."
ls -la database/migrations/*add_comments_to_assets*

echo ""
echo "🗄️ Checking migrations table..."
php artisan tinker --execute="DB::table('migrations')->where('migration', 'like', '%add_comments_to_assets%')->get()->each(function(\$m) { echo \$m->migration . ' - Ran at: ' . \$m->batch . PHP_EOL; });"

echo ""
echo "📊 Checking assets table schema for comments column..."
php artisan tinker --execute="\$columns = DB::select('DESCRIBE assets'); foreach(\$columns as \$col) { if(str_contains(\$col->Field, 'comment') || str_contains(\$col->Field, 'description')) { echo \$col->Field . ' | ' . \$col->Type . ' | ' . (\$col->Null == 'YES' ? 'Nullable' : 'Not Null') . PHP_EOL; } }"

echo ""
echo "✅ Check complete!"
EOF
