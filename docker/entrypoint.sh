#!/bin/sh
set -e

# Default PORT to 10000 if not set (Render default)
export PORT=${PORT:-10000}

echo "==> Configuring Nginx to listen on port ${PORT}..."
mkdir -p /run/nginx
envsubst '${PORT}' < /etc/nginx/templates/nginx.conf.template > /etc/nginx/http.d/default.conf

# Clean stale bootstrap caches if any
rm -f /var/www/html/bootstrap/cache/packages.php \
      /var/www/html/bootstrap/cache/services.php \
      /var/www/html/bootstrap/cache/config.php

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Production optimisations and cache
echo "==> Preparing Laravel cache..."
php artisan storage:link --force || true
php artisan package:discover --ansi || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run migrations if enabled (default true)
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Running database migrations..."
    php artisan migrate --force || echo "Warning: Migration failed or database not ready."
fi

# Run database seeder if explicitly requested (useful for first-time deploy on Render)
if [ "${RUN_SEEDER:-false}" = "true" ]; then
    echo "==> Running database seeders..."
    php artisan db:seed --force || echo "Warning: Database seeding failed."
fi

# Execute CMD passed to container or default to supervisord
if [ "$#" -gt 0 ]; then
    exec "$@"
else
    echo "==> Starting web application via supervisord..."
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
fi
