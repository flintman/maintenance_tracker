FROM php:8.2-apache


# Install pdo_mysql and set upload limits
RUN docker-php-ext-install pdo_mysql \
	&& echo "upload_max_filesize=25M" > /usr/local/etc/php/conf.d/uploads.ini \
	&& echo "post_max_size=25M" >> /usr/local/etc/php/conf.d/uploads.ini

COPY docker-entrypoint-init.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint-init.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint-init.sh"]