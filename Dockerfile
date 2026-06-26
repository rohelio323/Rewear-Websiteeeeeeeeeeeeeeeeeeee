FROM php:8.2-cli

# Install PHP extension helper (handles all dependencies automatically)
RUN curl -sSLf \
    -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions

# Install PHP extensions
RUN install-php-extensions pdo_mysql pdo_sqlite mbstring exif pcntl bcmath gd zip

# Install Node.js 20 LTS and system tools
RUN apt-get update && apt-get install -y git curl unzip ca-certificates \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

# Install PHP deps without dev/testing packages (skips laravel/dusk ext-zip issue)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets
RUN npm ci && npm run build

# Setup writable directories
RUN mkdir -p database && touch database/database.sqlite \
    && mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
                storage/framework/testing storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan db:seed --force && php artisan storage:link --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]
