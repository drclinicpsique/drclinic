# Etapa 1: dependências Composer (build)
FROM composer:2 AS vendor
WORKDIR /app

# Copia composer.* primeiro para cachear camadas
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress

# Copia o projeto e garante vendor atualizado
COPY . .
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress

# Etapa 2: runtime Nginx + PHP-FPM
FROM richarvey/nginx-php-fpm:php8.2
WORKDIR /app

# Configura raiz web do Laravel
ENV DOCUMENT_ROOT=/app/public
ENV WEBROOT=/app/public
ENV SKIP_COMPOSER=1

# Copia código e vendor
COPY . /app
COPY --from=vendor /app/vendor /app/vendor

# Permissões de gravação
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Entry point: prepara app e sobe nginx+php-fpm
RUN printf '#!/usr/bin/env bash\nset -e\n\
php -v\n\
exec /start.sh\n' > /docker-entrypoint.sh \
 && chmod +x /docker-entrypoint.sh

EXPOSE 80
CMD ["/docker-entrypoint.sh"]