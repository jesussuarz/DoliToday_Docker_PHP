FROM php:8.2-apache

# Instalar curl CLI
RUN apt-get update && apt-get install -y curl

# Habilitar mod_rewrite (opcional)
RUN a2enmod rewrite

# Copiar código
COPY app/ /var/www/html/

# Permisos
RUN chown -R www-data:www-data /var/www/html
