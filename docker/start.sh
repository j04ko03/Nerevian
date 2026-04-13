#!/bin/bash

# Lo he creado para que tanto el feont como el back arranquen a la vez.

# Iniciar PHP-FPM en segundo plano
php-fpm -D

# Iniciar Nginx en primer plano para mantener el contenedor vivo
nginx -g "daemon off;"