FROM php:8.1-apache

# Copy virtual host into container
COPY default.conf /etc/apache2/sites-available/000-default.conf

# Enable rewrite mode
RUN a2enmod rewrite

# Install necessary packages
RUN apt-get update && \
    apt-get install \
    libzip-dev \
    wget \
    git \
    unzip \
    -y --no-install-recommends

# Install PHP Extensions
RUN docker-php-ext-install zip pdo_mysql

# Copy composer installable
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Copy php.ini
COPY ./php.ini /usr/local/etc/php/

# Change the current working directory
WORKDIR /var/www

# Change the owner of the container document root
RUN chown -R www-data:www-data /var/www

# Start Apache in foreground
CMD ["apache2-foreground"]