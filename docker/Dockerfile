ARG PHP_EXT="bcmath exif gd ldap imap mysqli pcntl pdo_mysql soap zip"

FROM node:20 AS frontend
WORKDIR /app
COPY package.json package-lock.json vite.config.js /app/
RUN npm install
COPY classes /app/classes
COPY resources /app/resources
COPY www /app/www
RUN npm run build

FROM php:8.1-apache AS web_server
ARG PHP_EXT

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions ${PHP_EXT} && \
    a2enmod rewrite headers && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/php "$PHP_INI_DIR/conf.d/"

FROM web_server AS openxe

COPY --chown=www-data:www-data --exclude=userdata . /var/www/html
COPY --chown=www-data:www-data --from=frontend /app/www/dist /var/www/html/www/dist