ARG DEBIAN_FRONTEND=noninteractive

FROM node:20 AS frontend
WORKDIR /app
COPY package.json package-lock.json vite.config.js /app/
RUN npm install
COPY classes /app/classes
COPY resources /app/resources
COPY www /app/www
RUN npm run build

FROM ubuntu:22.04 AS web_server
ARG DEBIAN_FRONTEND

RUN apt-get update && \
    apt-get install -y apache2 php8.1 libapache2-mod-php8.1 \
    php8.1-gd php8.1-ldap php8.1-imap php8.1-mysql php8.1-soap php8.1-zip php8.1-xml php8.1-cli php8.1-curl && \
    a2enmod php8.1 rewrite && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=frontend /app/www/dist /var/www/html/www/dist
COPY docker/php /etc/php/8.1/apache2/conf.d/

EXPOSE 80
CMD ["apachectl","-D","FOREGROUND"]
