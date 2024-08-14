FROM php:8.2-alpine

# Get latest Composer and install
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Configure PHP
RUN sed -i -e "s/upload_max_filesize = .*/upload_max_filesize = 1G/g" \
    -e "s/post_max_size = .*/post_max_size = 1G/g" \
    -e "s/memory_limit = .*/memory_limit = 512M/g" \
    /usr/local/etc/php/php.ini-production \
    && cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Install PHP extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pcntl exif soap gd zip pdo_mysql intl swoole

# Set working directory
WORKDIR /app

# Copy Laravel App
COPY . .

# Install Composer dependencies
RUN composer install

# Install Chokidar
COPY --from=node:22.1.0-alpine3.19 /usr/local/bin /usr/local/bin
COPY --from=node:22.1.0-alpine3.19 /usr/local/lib/node_modules /usr/local/lib/node_modules

RUN npm install --dev chokidar

RUN php artisan octane:install --server=swoole

EXPOSE 8000
CMD [ "php", "artisan", "octane:start", "--server=swoole",  "--host=0.0.0.0", "--port=8000", "--watch" ]

# ENTRYPOINT ["tail"]
# CMD ["-f","/dev/null"]
