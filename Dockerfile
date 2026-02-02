FROM php:8.1-apache

# Instalar extensiones necesarias de PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite si usa .htaccess
RUN a2enmod rewrite

# Copiar configuración personalizada de PHP
COPY php-custom.ini /usr/local/etc/php/conf.d/custom.ini

# Configurar Apache para permitir .htaccess
RUN echo '<Directory /var/www/html/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/docker-vhost.conf \
    && a2enconf docker-vhost

# Crear directorio de sesiones y establecer permisos
RUN mkdir -p /tmp/sessions && \
    chmod 1777 /tmp/sessions && \
    chown www-data:www-data /tmp/sessions

# Establecer permisos correctos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Exponer puerto 80
EXPOSE 80
