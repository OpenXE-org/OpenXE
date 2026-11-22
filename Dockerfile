# syntax=docker/dockerfile:1

# Stage 1: Build frontend assets with Vite
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json vite.config.js ./
RUN npm ci --no-audit --no-fund

COPY classes ./classes
COPY resources ./resources
COPY www ./www
RUN npm run build

# Stage 2: Production PHP + Apache Runtime
ARG PHP_VERSION=8.5
FROM php:${PHP_VERSION}-apache AS production

# Copy extension installer from pre-built image
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install PHP extensions required by OpenXE
RUN install-php-extensions \
    bcmath \
    exif \
    gd \
    imap \
    intl \
    ldap \
    mysqli \
    opcache \
    pcntl \
    pdo_mysql \
    soap \
    zip

# Enable Apache modules
RUN a2enmod rewrite headers remoteip

# Install lightweight mariadb client and utilities for healthchecks & DB init
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    mariadb-client \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Apply PHP and Apache configurations
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php/memory.ini "$PHP_INI_DIR/conf.d/zz-openxe.ini"
COPY docker/apache/openxe.conf /etc/apache2/conf-available/openxe.conf
RUN a2enconf openxe

# Set working directory and copy application source
WORKDIR /var/www/html
COPY . .

# Copy compiled frontend assets from Stage 1
COPY --from=frontend /app/www/dist ./www/dist

# Set up required persistent directories and permissions
RUN mkdir -p userdata/tmp conf www/cache \
    && chown -R www-data:www-data /var/www/html

# Install entrypoint script
COPY docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
