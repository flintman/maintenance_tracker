FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

COPY docker-entrypoint-init.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint-init.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint-init.sh"]