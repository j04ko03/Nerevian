# ==========================================
# Etapa 1: Construcción del Backend (Composer)
# ==========================================
FROM composer:2.7 AS vendor
WORKDIR /app

# 1. Copiamos SOLO los archivos de dependencias primero. 
# Así, si no hay paquetes nuevos, Docker usa la caché y se salta la descarga je
COPY composer.json composer.lock ./
RUN composer install --no-dev --ignore-platform-reqs --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# ==========================================
# Etapa 2: Construcción del Frontend (Modificada para Wayfinder)
# ==========================================
# Usamos una imagen ligera de PHP porque el plugin de Vite necesita ejecutar 'artisan'
FROM php:8.4-alpine AS frontend
WORKDIR /app

# 1. Instalamos Node.js y npm dentro de esta imagen de PHP
RUN apk add --no-cache nodejs npm

# 2. Copiamos las dependencias de Node
COPY package.json package-lock.json ./
RUN npm ci

# 3. Copiamos todo el código fuente (necesario para Vite y Artisan)
COPY . .

# 4. Traje la carpeta 'vendor' de la Etapa 1.
# Sin esto, el comando 'php artisan' del plugin fallaría.
COPY --from=vendor /app/vendor/ ./vendor/

# 5. Compilamos.
RUN npm run build

# ==========================================
# Etapa 3: Imagen Final (Producción)
# ==========================================
FROM php:8.4-fpm

# 3. Instalamos SOLO lo necesario para ejecutar la app (quitamos nodejs, npm, git, etc.)
RUN apt-get update && apt-get install -y \
    nginx curl gnupg2 libpng-dev libonig-dev libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones ODBC para SQL Server
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 unixodbc-dev \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv \
    && rm -rf /var/lib/apt/lists/*

# Otras extensiones PHP necesarias para Laravel
RUN docker-php-ext-install mbstring exif pcntl bcmath gd

WORKDIR /var/www

# 4. Copiamos el código fuente base de la aplicación
COPY . .

# 5. Traemos la carpeta "vendor" ya compilada de la Etapa 1
COPY --from=vendor /app/vendor/ /var/www/vendor/

# 6. Traemos los assets públicos (js, css de Vue) ya compilados de la Etapa 2
# (Asegúrate de que la ruta coincida con donde compila tu frontend, usualmente public/build)
COPY --from=frontend /app/public/ /var/www/public/

# Permisos para Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Archivos de configuración
COPY docker/nginx.conf /etc/nginx/sites-available/default
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]