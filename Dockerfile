FROM php:8.2-fpm

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    nginx nodejs npm git curl zip unzip \
    libpng-dev libonig-dev libxml2-dev \
    unixodbc-dev

# Instalar extensiones ODBC para SQL Server
RUN curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/11/prod.list \
        > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Otras extensiones PHP
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Compilar Vue
RUN npm install && npm run build

# Permisos Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]