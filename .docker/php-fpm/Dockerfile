# Utilisation de PHP 7.4 avec FPM
FROM php:7.4-fpm

# Installation des dépendances requises
RUN apt-get update && apt-get install -y \
    default-mysql-client \
    netcat-openbsd \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql opcache \
    && rm -rf /var/lib/apt/lists/*  # Nettoyage pour réduire la taille de l'image

# Installation de Composer depuis une image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installation de Redis
RUN pecl install redis && docker-php-ext-enable redis

# Définir le répertoire de travail
WORKDIR /var/www

# Copier uniquement les fichiers nécessaires
COPY . /var/www

# Exposer le port PHP-FPM
EXPOSE 9000

# Lancer PHP-FPM
CMD ["php-fpm"]
