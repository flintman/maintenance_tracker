#!/bin/sh
set -e

mkdir -p /var/www/html/assets/uploads /var/www/html/templates_c
chown -R www-data:www-data /var/www/html/assets/uploads /var/www/html/templates_c

exec apache2-foreground
