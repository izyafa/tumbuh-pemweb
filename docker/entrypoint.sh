#!/bin/sh
set -e

# Default PORT to 10000 if not set (Render default)
export PORT=${PORT:-10000}

# Default LOG_CHANNEL to stderr in container so Render captures all Laravel error traces
export LOG_CHANNEL=${LOG_CHANNEL:-stderr}

echo "==> Configuring Nginx to listen on port ${PORT}..."
mkdir -p /run/nginx /var/log/nginx
envsubst '${PORT}' < /etc/nginx/templates/nginx.conf.template > /etc/nginx/http.d/default.conf

# Clean any stale bootstrap cache files
rm -f /var/www/html/bootstrap/cache/*.php

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache \
         /var/www/html/database

# Ensure database directory and fallback sqlite file exist to prevent crashes if DB_CONNECTION=sqlite
touch /var/www/html/database/database.sqlite

# Ensure APP_KEY exists to prevent 500 Server Error if omitted on Render dashboard
if [ -z "$APP_KEY" ]; then
    echo "==> APP_KEY is not set. Generating a dynamic runtime encryption key..."
    export APP_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
fi

chown -R nginx:nginx /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Clear runtime caches so Laravel dynamically reads the latest Render & Neon environment variables
echo "==> Clearing stale caches for dynamic runtime boot..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true
php artisan storage:link --force || true
php artisan package:discover --ansi || true

# Run database migrations asynchronously so Nginx + PHP-FPM start immediately (<1s).
# This prevents Render health check (/up) timeouts during cold starts when Neon serverless PostgreSQL is waking up.
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "==> Running database migrations in background..."
    (
        if php artisan migrate --force; then
            echo "==> Database migrations completed successfully."
            if [ "${RUN_SEEDER:-false}" = "true" ]; then
                echo "==> Running database seeders..."
                php artisan db:seed --force || echo "==> Warning: Database seeding failed."
            fi
        else
            echo "==> Warning: Database migration failed. Check DATABASE_URL/DB_URL credentials."
        fi
    ) &
fi

# Execute CMD passed to container or default to supervisord
if [ "$#" -gt 0 ]; then
    exec "$@"
else
    echo "==> Starting web application via supervisord (Nginx + PHP-FPM)..."
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
fi
