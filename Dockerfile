FROM php:8.1-apache

# ── System dependencies ────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libssl-dev \
        zip \
        unzip \
        git \
        curl \
    && docker-php-ext-install \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ── PHP config ─────────────────────────────────────────────────────────────
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

# ── Apache config ──────────────────────────────────────────────────────────
# Enable mod_rewrite and headers — required for CI3 routing
RUN a2enmod rewrite headers

# Set Apache to listen on 8080 instead of 80
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

# Replace default vhost with ours (AllowOverride All = .htaccess is respected)
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# ── Composer ───────────────────────────────────────────────────────────────
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ── Application ────────────────────────────────────────────────────────────
WORKDIR /var/www/html

COPY . .

# Install CodeIgniter 3 and dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Create system/ symlink if Composer post-install script didn't
RUN if [ ! -e system ]; then \
        ln -s vendor/codeigniter/framework/system system; \
    fi

# ── Permissions ────────────────────────────────────────────────────────────
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && chmod -R 775 /var/www/html/application/cache \
                    /var/www/html/application/logs

# ── Startup ────────────────────────────────────────────────────────────────
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]
