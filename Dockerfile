#LABEL authors="filch"

#ENTRYPOINT ["top", "-b"]

FROM php:8.3.7-fpm

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
