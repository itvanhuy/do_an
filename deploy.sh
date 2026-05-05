#!/bin/bash
# Deploy script for TechShop
echo "=== TechShop Deploy Script ==="

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run migrations
php artisan migrate --force

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "=== Deploy Complete ==="
