FROM php:8.1-apache

# Enable required Apache modules
RUN a2enmod rewrite headers

# Install PHP extensions needed by CodeIgniter
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mysqli mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install CodeIgniter 3 via Composer (downloads the system/ folder)
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Symlink CI3 system folder if composer script didn't create it
RUN [ -d "system" ] || ln -s vendor/codeigniter/framework/system system

# Write Apache virtual host — AllowOverride All makes .htaccess mod_rewrite work
RUN echo '<VirtualHost *:${PORT:-80}>\n\
    DocumentRoot /var/www/html\n\
    ServerName _\n\
    <Directory /var/www/html>\n\
        Options -Indexes +FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog /dev/stderr\n\
    CustomLog /dev/stdout combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Script to patch Apache port at container start (Render injects $PORT)
RUN echo '#!/bin/bash\n\
PORT=${PORT:-80}\n\
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf\n\
sed -i "s/\${PORT:-80}/${PORT}/g" /etc/apache2/sites-available/000-default.conf\n\
chown -R www-data:www-data /var/www/html/application/cache /var/www/html/application/logs\n\
chmod -R 775 /var/www/html/application/cache /var/www/html/application/logs\n\
exec apache2-foreground' > /start.sh && chmod +x /start.sh

# Set correct permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/application/cache \
    && chmod -R 775 /var/www/html/application/logs

CMD ["/start.sh"]
