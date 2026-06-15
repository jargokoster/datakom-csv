FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
	nginx supervisor git unzip sqlite3 libsqlite3-dev libzip-dev \
	&& docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist
RUN mkdir -p \
	storage/framework/cache \
	storage/framework/sessions \
	storage/framework/views \
	storage/logs \
	storage/api-docs \
	storage/app/exports \
	bootstrap/cache \
	database \
	&& touch database/database.sqlite \
	&& chown -R www-data:www-data storage bootstrap/cache database \
	&& chmod -R 775 storage bootstrap/cache database

COPY docker/nginx/default.conf /etc/nginx/sites-available/default
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD chown -R www-data:www-data storage bootstrap/cache database \
	&& chmod -R 775 storage bootstrap/cache database \
	&& php artisan migrate --force \
	&& php artisan openapi:generate \
	&& /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
