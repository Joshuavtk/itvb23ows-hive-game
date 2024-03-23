FROM php:8.3.4

# Install mysqli extension
RUN docker-php-ext-install mysqli