FROM php:8.1-apache

# Install required packages and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends default-mysql-client libzip-dev unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mysqli \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy project files into the container
COPY . /var/www/html

# Fix permissions for Apache
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
