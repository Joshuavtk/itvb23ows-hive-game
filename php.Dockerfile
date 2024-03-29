FROM php:8.3.4-apache

RUN docker-php-ext-install pdo pdo_mysql
