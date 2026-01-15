FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libsqlite3-dev \
    autoconf \
    build-essential \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install zip pdo pdo_mysql pdo_sqlite sockets pcntl
RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . /app

RUN composer clear-cache || true
RUN composer install --no-interaction --prefer-source --optimize-autoloader || true

RUN mkdir -p storage/logs bootstrap/cache database && \
    chmod -R 777 storage bootstrap/cache || true

RUN chmod +x docker/entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["sh", "docker/entrypoint.sh"]
