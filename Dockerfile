#LABEL authors="filch"

#ENTRYPOINT ["top", "-b"]

FROM php:latest-fpm

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
