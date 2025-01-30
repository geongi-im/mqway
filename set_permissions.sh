#!/bin/bash

# Ensure correct permissions for /var/www/html
echo "Setting permissions for /var/www/html"
chmod -R 775 /var/www/html

echo "Setting permissions for /var/www/html/storage"
chmod -R 775 storage
chown -R www-data:www-data storage

# Check if composer dependencies are installed
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "Installing Composer dependencies"
    composer install --no-dev --prefer-dist
else
    echo "Composer dependencies are already installed"
fi

# Continue with default command
exec "$@"
