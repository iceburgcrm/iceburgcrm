#!/bin/bash

set -e

cd /var/www/html

echo "======================================"
echo "Starting Iceburg CRM"
echo "======================================"

# Create .env if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env from .env.example..."
    cp .env.example .env
fi

# Configure database for Docker
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env
sed -i 's/^DB_HOST=.*/DB_HOST=db/' .env
sed -i 's/^DB_PORT=.*/DB_PORT=3306/' .env
sed -i 's/^DB_DATABASE=.*/DB_DATABASE=iceburg/' .env
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=iceburg_user/' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=secret/' .env

# Generate APP_KEY if it doesn't exist
if ! grep -q '^APP_KEY=.\+' .env; then
    echo "Generating Laravel application key..."
    php artisan key:generate --force
fi

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

echo "Waiting for MySQL..."

until php -r "
try {
    \$pdo = new PDO(
        'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'),
        getenv('DB_USERNAME'),
        getenv('DB_PASSWORD')
    );
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
"; do
    echo "MySQL is not ready yet..."
    sleep 2
done

echo "MySQL is ready."

echo "Running Laravel package discovery..."
php artisan package:discover --ansi

echo "Clearing Laravel configuration cache..."
php artisan config:clear

echo "======================================"
echo "Iceburg CRM is ready!"
echo "======================================"

exec apache2-foreground