FROM php:8.2-apache

# Install ALL database drivers for maximum compatibility
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql

# Enable Apache Rewrite module
RUN a2enmod rewrite

# Change DocumentRoot to public/ for security and efficiency
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . /var/www/html/

# Expose standard web port
EXPOSE 80
