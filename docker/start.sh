#!/bin/bash
set -e

# Fix permissions on writable directories at runtime
chown -R www-data:www-data /var/www/html/application/cache \
                            /var/www/html/application/logs
chmod -R 775 /var/www/html/application/cache \
             /var/www/html/application/logs

echo "[start.sh] Starting Apache on port 8080"
exec apache2-foreground
