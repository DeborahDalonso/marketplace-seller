# Os assets saem do Vite numa etapa só com Node; a imagem final não leva npm
FROM node:22-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
COPY resources ./resources
RUN npm ci --no-audit --no-fund && npm run build


FROM php:8.3-cli-alpine

RUN docker-php-ext-install pdo_mysql
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev \
    && php artisan package:discover \
    && chown -R www-data:www-data storage bootstrap/cache

USER www-data

ENV PORT=3004
EXPOSE 3004

# "artisan serve" é o servidor embutido do PHP: um processo, sem nginx nem fpm.
# Para o painel de demonstração numa VM só, é o suficiente e cabe numa linha;
# com tráfego de verdade, o caminho é php-fpm atrás de um proxy.
CMD ["sh", "-c", "php artisan migrate --force && php artisan storage:link --force && php artisan serve --host=0.0.0.0 --port=${PORT}"]
