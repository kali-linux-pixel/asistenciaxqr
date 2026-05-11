FROM php:8.2-apache

# Install PDO MySQL driver
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache Rewrite module (needed for your .htaccess)
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all project files into the image
COPY . /var/www/html/

# Expose the standard web port
EXPOSE 80
