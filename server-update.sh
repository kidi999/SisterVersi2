#!/bin/bash

# ====================================
# SISTER v2.0 - Server Update Script
# ====================================
# 
# Script untuk update aplikasi di server setelah git pull
# Run di server: bash server-update.sh
#

echo "========================================="
echo "🔄 SISTER v2.0 - Server Update"
echo "========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

print_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${YELLOW}ℹ️  $1${NC}"
}

# Confirm
echo "Script ini akan:"
echo "  1. Pull latest code dari GitHub"
echo "  2. Install composer dependencies"
echo "  3. Run migration database"
echo "  4. Clear & cache semua"
echo "  5. Restart services"
echo ""
read -p "Lanjutkan? (y/n): " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_error "Update dibatalkan"
    exit 1
fi

echo ""
print_info "Step 1: Pull latest code..."
git pull origin main
if [ $? -eq 0 ]; then
    print_success "Code updated"
else
    print_error "Git pull failed!"
    exit 1
fi

echo ""
print_info "Step 2: Install dependencies..."
composer install --no-dev --optimize-autoloader
if [ $? -eq 0 ]; then
    print_success "Dependencies installed"
else
    print_error "Composer install failed!"
    exit 1
fi

echo ""
print_info "Step 3: Run migrations..."
php artisan migrate --force
if [ $? -eq 0 ]; then
    print_success "Migrations completed"
else
    print_error "Migration failed!"
    exit 1
fi

echo ""
print_info "Step 4: Clear cache..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
print_success "Cache cleared"

echo ""
print_info "Step 5: Optimize..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Application optimized"

echo ""
print_info "Step 6: Fix permissions..."
sudo chown -R www-data:www-data .
sudo find . -type d -exec chmod 755 {} \;
sudo find . -type f -exec chmod 644 {} \;
sudo chmod -R 775 storage bootstrap/cache
print_success "Permissions fixed"

echo ""
print_info "Step 7: Restart services..."
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
print_success "Services restarted"

echo ""
echo "========================================="
print_success "✅ Update Completed!"
echo "========================================="
echo ""
echo "📝 Post-Update Checklist:"
echo "  [ ] Website accessible"
echo "  [ ] Login works (email/password)"
echo "  [ ] Google SSO works"
echo "  [ ] Profile page displays academic info"
echo "  [ ] All modules working (KRS, Nilai, Jadwal)"
echo ""
echo "📊 Check logs if needed:"
echo "  - Laravel: tail -f storage/logs/laravel.log"
echo "  - Nginx: tail -f /var/log/nginx/error.log"
echo "  - PHP: tail -f /var/log/php8.2-fpm.log"
echo ""
