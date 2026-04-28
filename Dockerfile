FROM php:8.2-cli

# Install PostgreSQL development libraries and build tools
RUN apt-get update && apt-get install -y \
    libpq-dev \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

# Install PostgreSQL PHP extension
RUN docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

# Copy php.ini for upload limits
COPY php.ini /usr/local/etc/php/conf.d/uploads.ini

# Copy application files
COPY public /var/www/html/public
COPY src /var/www/html/src
COPY temporal /var/www/html/temporal

# Ensure uploads directory exists and is writable
RUN mkdir -p /var/www/html/public/uploads && chmod 777 /var/www/html/public/uploads

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]