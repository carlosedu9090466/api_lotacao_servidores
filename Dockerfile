# configuração dos containers Laravel 
FROM php:8.4.5-apache

# Instala extensões PHP necessárias (PostgreSQL, PDO, etc.)
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia o projeto para o container
WORKDIR /var/www/html
COPY . .

# Instala dependências (ignorando plataforma para evitar conflitos)
RUN composer install --ignore-platform-reqs

# Configura o VirtualHost do Apache
RUN echo "\
<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>\n\
" > /etc/apache2/sites-available/000-default.conf

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

RUN a2enmod rewrite && service apache2 restart

# Expõe a porta 80 (Apache)
EXPOSE 80