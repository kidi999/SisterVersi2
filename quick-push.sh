#!/bin/bash

# Quick Git Push - Otomatis commit dan push ke GitHub
# Gunakan ini untuk cepat upload perubahan

echo "========================================"
echo "   Quick Git Push"
echo "========================================"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Check if git is initialized
if [ ! -d ".git" ]; then
    echo -e "${YELLOW}[INFO]${NC} Git repository not found. Initializing..."
    git init
    git branch -M main
    read -p "Enter GitHub repository URL (e.g., https://github.com/username/sister.git): " REPO_URL
    git remote add origin "$REPO_URL"
    echo -e "${GREEN}[SUCCESS]${NC} Git initialized"
    echo ""
fi

# Get commit message
read -p "Enter commit message (or press Enter for auto message): " COMMIT_MSG

if [ -z "$COMMIT_MSG" ]; then
    # Auto generate commit message with timestamp
    COMMIT_MSG="Update $(date '+%Y-%m-%d %H:%M:%S')"
fi

echo ""
echo -e "${YELLOW}[INFO]${NC} Adding all changes..."
git add .

echo -e "${YELLOW}[INFO]${NC} Committing changes..."
git commit -m "$COMMIT_MSG"

if [ $? -ne 0 ]; then
    echo -e "${YELLOW}[WARNING]${NC} Nothing to commit or commit failed"
    echo -e "${YELLOW}[INFO]${NC} Trying to push anyway..."
fi

echo ""
echo -e "${YELLOW}[INFO]${NC} Pushing to GitHub..."
git push origin main

if [ $? -ne 0 ]; then
    echo -e "${YELLOW}[INFO]${NC} Trying master branch..."
    git push origin master
fi

if [ $? -ne 0 ]; then
    echo ""
    echo -e "${YELLOW}[INFO]${NC} First time push? Setting upstream..."
    git push -u origin main
fi

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}[SUCCESS]${NC} Changes uploaded successfully!"
    echo -e "${YELLOW}[INFO]${NC} GitHub Actions will auto-deploy if configured"
else
    echo ""
    echo -e "${RED}[ERROR]${NC} Failed to push. Check your internet connection and GitHub credentials."
    exit 1
fi

echo ""
