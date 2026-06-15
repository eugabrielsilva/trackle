FROM php:8.0-apache
RUN a2enmod rewrite
RUN apt-get update && apt-get install -y libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf
COPY . /var/www/html/
RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html/data
EXPOSE 80
