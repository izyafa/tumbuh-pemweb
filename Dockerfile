# =========================================================================
# Stage 1: Build Frontend Assets (Vite)
# =========================================================================
FROM node:22-alpine AS frontend-builder
WORKDIR /app

COPY package*.json ./
RUN npm ci

COPY . .
RUN npm run build

# =========================================================================
# Stage 2: Install PHP Composer Dependencies
# =========================================================================
FROM composer:2 AS composer-builder
WORKDIR /app

COPY composer.json composer.lock* ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-req=php \
    --no-scripts

COPY . .
RUN composer dump-autoload --optimize --no-dev --ignore-platform-req=php

# =========================================================================
# Stage 3: Runtime Image (Alpine 3.20 + Pre-compiled PHP 8.3 + Nginx)
# =========================================================================
FROM alpine:3.20 AS runner

# Install system dependencies, Nginx, Supervisor, gettext, and pre-built PHP 8.3
RUN apk add --no-cache \
    nginx \
    supervisor \
    gettext \
    curl \
    bash \
    php83 \
    php83-fpm \
    php83-pdo \
    php83-pdo_pgsql \
    php83-pgsql \
    php83-bcmath \
    php83-opcache \
    php83-pcntl \
    php83-zip \
    php83-exif \
    php83-mbstring \
    php83-tokenizer \
    php83-xml \
    php83-curl \
    php83-openssl \
    php83-session \
    php83-dom \
    php83-fileinfo \
    php83-ctype

# Symlink php83 and php-fpm83 to standard names, configure PHP-FPM to run as user nginx
RUN ln -sf /usr/bin/php83 /usr/bin/php && \
    ln -sf /usr/sbin/php-fpm83 /usr/sbin/php-fpm && \
    sed -i 's/user = nobody/user = nginx/g' /etc/php83/php-fpm.d/www.conf && \
    sed -i 's/group = nobody/group = nginx/g' /etc/php83/php-fpm.d/www.conf

# Prepare Nginx & Supervisor folders
RUN mkdir -p /etc/nginx/templates /etc/nginx/http.d /etc/supervisor/conf.d /etc/php83/conf.d

# Copy custom configurations
COPY docker/php.ini /etc/php83/conf.d/99-custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/nginx.conf.template /etc/nginx/templates/nginx.conf.template
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set application directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Copy vendor from composer builder
COPY --from=composer-builder /app/vendor /var/www/html/vendor

# Copy compiled assets from frontend builder
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Ensure proper permissions for web server (nginx:nginx in Alpine)
RUN chown -R nginx:nginx /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose default Render port
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
