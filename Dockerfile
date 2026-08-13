FROM php:8.4-fpm

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    poppler-utils \
    libreoffice \
    imagemagick \
    tesseract-ocr \
    ghostscript \
    && rm -rf /var/lib/apt/lists/*

# Configuración de GD
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg

# Extensiones PHP
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    bcmath \
    intl \
    zip \
    gd \
    exif \
    pcntl \
    opcache

# Extensión Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Node.js LTS
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www

EXPOSE 9000

CMD ["php-fpm"]
