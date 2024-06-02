#FROM ubuntu:latest
FROM nginx:latest

#LABEL authors="filch"

#ENTRYPOINT ["top", "-b"]

FROM php:8.2-fpm
RUN docker-php-ext-install mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli