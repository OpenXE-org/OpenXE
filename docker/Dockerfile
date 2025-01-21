ARG PHP_EXT="gd ldap imap mysqli soap zip"
ARG DEB_PKG="libc-client2007e libaom3 libavif15 libdav1d6 libfreetype6 libjpeg62-turbo libldap-common libpng16-16 libwebp7 libxpm4 libzip4"
ARG DEB_PKG_TMP="cmake gnutls-dev libaom-dev libavif-dev libbz2-dev libc-client-dev libdav1d-dev libfreetype6-dev libjpeg62-turbo-dev libkrb5-dev libldap2-dev libpng-dev libssl-dev libwebp-dev libxml2-dev libxpm-dev libzip-dev zlib1g-dev"

FROM node:20 as frontend
WORKDIR /app
COPY package.json package-lock.json vite.config.js /app/
RUN npm install
COPY classes /app/classes
COPY resources /app/resources
COPY www /app/www
RUN npm run build

#FROM php:8.1-fpm as fpm_server
FROM php:8.1-apache as web_server
ARG PHP_EXT
ARG DEB_PKG
ARG DEB_PKG_TMP

WORKDIR /app
RUN apt-get update && apt-get install -y ${DEB_PKG} ${DEB_PKG_TMP} && \
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install ${PHP_EXT} && \
    apt-get remove --purge -y ${DEB_PKG_TMP} && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php "$PHP_INI_DIR/conf.d/"
COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=frontend /app/www/dist /var/www/html/www/dist