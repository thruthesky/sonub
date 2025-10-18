# Install PHP 8.3.6-fpm. It is the same version of DB server.
FROM php:8.3.6-fpm



# Update package lists and install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    libcurl4-openssl-dev \
    libonig-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libwebp-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*


# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mysqli \
    curl


# Install PHP extensions
RUN docker-php-ext-install \
    mbstring


# Install GD extension with support for JPEG, PNG, WebP, and FreeType
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd

# 환경 설정 파일을 Production 의 것으로 복사한다.
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# PHP-FPM 설정 파일 복사 (XML 파일 처리를 위한 설정 포함)
COPY ./etc/php-fpm.d/custom.conf /usr/local/etc/php-fpm.d/custom.conf