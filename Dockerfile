#FROM fhsinchy/php-nginx-base:php8.1.3-fpm-nginx1.20.2-alpine3.15
FROM tangramor/nginx-php8-fpm

# copy application code
WORKDIR /var/www/html
COPY .  .

# set composer related environment variables
ENV PATH="/composer/vendor/bin:$PATH" \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_VENDOR_DIR=/var/www/html/vendor \
    COMPOSER_HOME=/composer

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --ansi --version --no-interaction

# install application dependencies
#WORKDIR /var/www
COPY composer.json composer.lock* ./
RUN composer install --no-scripts --no-autoloader --ansi --no-interaction

# add custom php-fpm pool settings, these get written at entrypoint startup
ENV FPM_PM_MAX_CHILDREN=45 \
    FPM_PM_START_SERVERS=2 \
    FPM_PM_MIN_SPARE_SERVERS=1 \
    FPM_PM_MAX_SPARE_SERVERS=3 \
    RUN_SCRIPTS="1" \
    TZ="Africa/Lagos" \
    PHP_ERRORS_STDERR="1" \
    LOG_CHANNEL=stdout \
    ERRORS="1"


RUN usermod -u 1000 www-data



# copy entrypoint files

COPY ./docker/docker-php-* /usr/local/bin/

RUN chmod +x /usr/local/bin/docker-php-*
RUN dos2unix /usr/local/bin/docker-php-entrypoint
RUN dos2unix /usr/local/bin/docker-php-entrypoint-dev

# copy nginx configuration
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/default.conf /etc/nginx/conf.d/default.conf

#copy custom scripts that gets executed on startup
COPY ./docker/scripts /var/www/html/scripts/
COPY ./docker/queue-worker.conf /etc/supervisor/conf.d/queue-worker.conf

RUN composer update --no-scripts \
    && composer dump-autoload -o \
    && chown -R :www-data /var/www/html \
    && chown -Rf nginx:nginx /var/www/html/storage \
    && chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache


EXPOSE 80

