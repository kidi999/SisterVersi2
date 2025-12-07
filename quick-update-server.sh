#!/bin/bash

# ====================================
# SISTER v2.0 - Quick Server Update
# ====================================

echo "========================================="
echo "🔄 SISTER v2.0 - Server Update"
echo "========================================="
echo ""

# Step 1: Check current status
echo "📊 Checking current status..."
echo "Current directory: $(pwd)"
echo "Current branch: $(git branch --show-current)"
echo "Last commit: $(git log -1 --oneline)"
echo ""

# Step 2: Backup reminder
echo "⚠️  IMPORTANT: Backup Reminder"
echo "Have you backed up the database? (y/n)"
read -r backup_confirm
if [[ ! $backup_confirm =~ ^[Yy]$ ]]; then
    echo "❌ Please backup database first:"
    echo "   mysqldump -u username -p sister_db > backup_$(date +%Y%m%d).sql"
    exit 1
fi

# Step 3: Pull latest code
echo ""
echo "📥 Step 1: Pulling latest code from GitHub..."
git fetch origin
git pull origin main
if [ $? -ne 0 ]; then
    echo "❌ Git pull failed! Please resolve conflicts manually."
    exit 1
fi
echo "✅ Code updated successfully"

# Step 4: Install dependencies
echo ""
echo "📦 Step 2: Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
if [ $? -ne 0 ]; then
    echo "❌ Composer install failed!"
    exit 1
fi
echo "✅ Dependencies installed"

# Step 5: Run migrations
echo ""
echo "🗄️  Step 3: Running database migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "❌ Migration failed!"
    exit 1
fi
echo "✅ Migrations completed"

# Step 6: Clear cache
echo ""
echo "🧹 Step 4: Clearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo "✅ Cache cleared"

# Step 7: Optimize
echo ""
echo "⚡ Step 5: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Optimization completed"

# Step 8: Fix permissions
echo ""
echo "🔒 Step 6: Setting permissions..."
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
echo "✅ Permissions set"

echo ""
echo "========================================="
echo "✅ Update Completed Successfully!"
echo "========================================="
echo ""
echo "📝 Next Steps:"
echo "1. Update .env with Google OAuth credentials:"
echo "   nano .env"
echo "   Add: GOOGLE_CLIENT_ID=..."
echo "   Add: GOOGLE_CLIENT_SECRET=..."
echo ""
echo "2. Test the application:"
echo "   - Visit your website"
echo "   - Test login with email/password"
echo "   - Test Google SSO login"
echo "   - Test profile page"
echo ""
echo "3. Check logs if needed:"
echo "   tail -f storage/logs/laravel.log"
echo ""
