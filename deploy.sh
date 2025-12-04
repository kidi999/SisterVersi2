#!/bin/bash

# SISTER Deployment Script
# Script untuk deploy otomatis ke server production

echo "🚀 Starting deployment..."

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration - Edit sesuai dengan server Anda
SERVER_USER="username"
SERVER_HOST="your-server.com"
SERVER_PATH="/var/www/html/sister"
SSH_PORT="22"

# Functions
function print_success {
    echo -e "${GREEN}✓ $1${NC}"
}

function print_error {
    echo -e "${RED}✗ $1${NC}"
}

function print_info {
    echo -e "${YELLOW}ℹ $1${NC}"
}

# Check if git is installed
if ! command -v git &> /dev/null; then
    print_error "Git is not installed"
    exit 1
fi

# Check for uncommitted changes
if [[ -n $(git status -s) ]]; then
    print_info "You have uncommitted changes. Commit them first? (y/n)"
    read -r response
    if [[ "$response" == "y" ]]; then
        print_info "Enter commit message:"
        read -r commit_message
        git add .
        git commit -m "$commit_message"
        print_success "Changes committed"
    else
        print_error "Deployment cancelled. Please commit your changes first."
        exit 1
    fi
fi

# Push to repository
print_info "Pushing to repository..."
git push origin main || git push origin master
print_success "Code pushed to repository"

# Deploy to server via SSH
print_info "Deploying to server $SERVER_HOST..."

ssh -p $SSH_PORT $SERVER_USER@$SERVER_HOST << 'ENDSSH'
    # Navigate to project directory
    cd /var/www/html/sister || exit 1
    
    echo "📥 Pulling latest changes..."
    git pull origin main || git pull origin master
    
    echo "📦 Installing composer dependencies..."
    composer install --no-dev --optimize-autoloader --no-interaction
    
    echo "🔧 Running Laravel commands..."
    php artisan config:clear
    php artisan cache:clear
    php artisan view:clear
    php artisan route:clear
    
    echo "🗄️ Running migrations..."
    php artisan migrate --force
    
    echo "⚡ Optimizing application..."
    php artisan optimize
    
    echo "🔐 Setting permissions..."
    chmod -R 775 storage bootstrap/cache
    sudo chown -R www-data:www-data storage bootstrap/cache
    
    echo "✅ Deployment completed successfully!"
ENDSSH

if [ $? -eq 0 ]; then
    print_success "Deployment completed successfully!"
    print_info "Your application is now live at https://$SERVER_HOST"
else
    print_error "Deployment failed. Please check the error messages above."
    exit 1
fi
