FROM php:8.1-apache

ARG BRANCH=main
ARG REPO_URL=local

# Copy IMAP extension from mlocati/php-extension-installer image (pre-built)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    libkrb5-dev \
    libldap2-dev \
    mariadb-client \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install IMAP extension using php-extension-installer
RUN install-php-extensions imap

# Configure PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm \
    && docker-php-ext-configure ldap

# Install PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    soap \
    ldap

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy application files if REPO_URL is "local"
COPY . /var/www/html-local/

# Set working directory
WORKDIR /var/www/html

# Copy entrypoint script
COPY docker-entrypoint-init.sh /docker-entrypoint-init.sh
RUN chmod +x /docker-entrypoint-init.sh

# Set permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

ENTRYPOINT ["/docker-entrypoint-init.sh"]
