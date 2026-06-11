FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required by CodeIgniter 4
RUN docker-php-ext-configure intl \
    && docker-php-ext-install intl mysqli pdo_mysql zip

# Enable Apache Mod Rewrite
RUN a2enmod rewrite

# Copy custom Apache configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Install Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Adjust permissions
RUN chown -w /var/www/html
