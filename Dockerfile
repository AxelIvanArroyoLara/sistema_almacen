FROM php:8.1-apache

# Instalar extensiones necesarias de PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilitar mod_rewrite si usa .htaccess
RUN a2enmod rewrite
# Security: Disable dangerous PHP functions
RUN echo "disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source" >> /usr/local/etc/php/conf.d/security.ini

# Security: Set proper session configuration
RUN echo "session.cookie_httponly = On\nsession.cookie_secure = Off\nsession.use_only_cookies = On\nsession.sid_length = 256" >> /usr/local/etc/php/conf.d/security.ini

# Security: Display errors to logs only (not to user)
RUN echo "display_errors = Off\nlog_errors = On\nerror_log = /var/log/php-errors.log" >> /usr/local/etc/php/conf.d/security.ini

# Create logs directory
RUN mkdir -p /var/log && touch /var/log/php-errors.log && chmod 666 /var/log/php-errors.log