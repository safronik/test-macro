# syntax=docker/dockerfile:1
FROM php:8.2-fpm as test

# Easy way to install extensions with all dependencies
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# RUN apt update && apt install cron
RUN install-php-extensions curl opcache pdo_mysql redis sodium iconv mbstring zip fileinfo xdebug
RUN install-php-extensions @composer