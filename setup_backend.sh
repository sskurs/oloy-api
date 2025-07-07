#!/bin/bash

# OpenLoyalty Backend Setup Script
# This script sets up the backend database with users and initializes the application

echo "🚀 Setting up OpenLoyalty Backend..."

# Check if we're in the backend directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the backend directory"
    exit 1
fi

# 1. Install dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# 2. Set up database
echo "🗄️  Setting up database..."

# Check if database configuration exists
if [ ! -f "app/config/parameters.yml" ]; then
    echo "📝 Creating database configuration..."
    cp app/config/parameters.yml.dist app/config/parameters.yml
    echo "⚠️  Please update app/config/parameters.yml with your database credentials"
    echo "   Then run this script again"
    exit 1
fi

# 3. Create database schema
echo "🏗️  Creating database schema..."
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:schema:update --force

# 4. Load demo data
echo "📊 Loading demo data..."
php bin/console doctrine:query:sql < demo_data.sql

# 5. Load users
echo "👥 Creating users..."
php bin/console doctrine:query:sql < setup_users.sql

# 6. Clear cache
echo "🧹 Clearing cache..."
php bin/console cache:clear --env=prod
php bin/console cache:warmup --env=prod

# 7. Set permissions
echo "🔐 Setting file permissions..."
chmod -R 777 var/cache
chmod -R 777 var/logs
chmod -R 777 var/sessions

echo "✅ Backend setup completed!"
echo ""
echo "📋 Available Users:"
echo "   Admin: admin@loyaltypro.com / admin123"
echo "   Seller: seller@loyaltypro.com / seller123"
echo "   Customer: customer@loyaltypro.com / customer123"
echo ""
echo "🌐 Start the backend server:"
echo "   php bin/console server:start"
echo ""
echo "🔗 Backend URL: http://localhost:8000" 