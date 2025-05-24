FROM php:8.2-alpine

RUN apk add --no-cache \
    bash \
    curl \
    git \
    unzip \
    libpng \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    nodejs \
    npm \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
    && npm install \
    && npm run build

COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

COPY wait-for-it.sh /wait-for-it.sh
RUN chmod +x /wait-for-it.sh

CMD ["/entrypoint.sh"]
