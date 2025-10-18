# =============================================================================
# 🧭 DOCKERFILE — ENTORNO PHP + APACHE PARA MOCAPIC
# =============================================================================

FROM php:8.2-apache

# Extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
      libpng-dev libjpeg62-turbo-dev libfreetype6-dev libzip-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo pdo_mysql mysqli gd zip exif \
  && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar módulos de Apache
RUN a2enmod rewrite headers deflate expires

# Copiar configuración del VirtualHost
COPY ./vhost.conf /etc/apache2/sites-available/000-default.conf

# Copiar configuración PHP (seguridad)
COPY ./app/config/security.ini /usr/local/etc/php/conf.d/security.ini

# Directorio de trabajo
WORKDIR /var/www/html

# =============================================================================
# ✅ FIN DEL DOCKERFILE
# =============================================================================
