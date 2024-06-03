#LABEL authors="filch"

#ENTRYPOINT ["top", "-b"]

FROM php:fpm:latest

RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli
