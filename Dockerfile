FROM php:7.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd zip

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure Apache DocumentRoot
ENV APACHE_DOCUMENT_ROOT /var/www/html
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Copy application files
COPY . /var/www/html/

# Set permissions for uploads directory (if it exists)
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads && \
    chmod -R 775 /var/www/html/uploads

# Set permissions for application/cache directory
RUN mkdir -p /var/www/html/application/cache && \
    chown -R www-data:www-data /var/www/html/application/cache && \
    chmod -R 775 /var/www/html/application/cache

# Set permissions for application/logs directory
RUN mkdir -p /var/www/html/application/logs && \
    chown -R www-data:www-data /var/www/html/application/logs && \
    chmod -R 775 /var/www/html/application/logs

# Set permissions for application/sessions directory
RUN mkdir -p /var/www/html/application/sessions && \
    chown -R www-data:www-data /var/www/html/application/sessions && \
    chmod -R 775 /var/www/html/application/sessions

# Start Apache
CMD ["apache2-foreground"]

