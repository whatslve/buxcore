FROM php:8.3-fpm
WORKDIR /var/www/backend
ARG UID=1001
ARG GID=1001

RUN groupadd -g ${GID} appgroup \
 && useradd -m -u ${UID} -g appgroup buxcore \

# Зависимости
RUN apt-get update && apt-get install -y \
    git curl libpq-dev zip unzip libonig-dev libxml2-dev libzip-dev \
    nodejs npm sudo \
    && docker-php-ext-install pdo pdo_pgsql mbstring xml bcmath zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Рабочая папка и права
RUN mkdir -p /var/www/backend \
    && chown -R www-data:www-data /var/www/backend

USER www-data


CMD ["php-fpm"]
