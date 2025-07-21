FROM php:8.1-apache

# Instalar extensiones necesarias de PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite si usa .htaccess
RUN a2enmod rewrite
