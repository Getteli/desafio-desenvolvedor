FROM php:8.3.0-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev

RUN apt-get update -y && apt-get upgrade -y && apt-get install git libssl-dev -y

# Get latest Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install pdo pdo_pgsql
RUN docker-php-ext-install zip
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install exif
RUN docker-php-ext-install pcntl
RUN docker-php-ext-install bcmath

# Install MongoDB extension
RUN pecl install mongodb
RUN docker-php-ext-enable mongodb

# Add extension to php.ini
RUN echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer

# Verify extension installation
RUN php -i | grep mongodb

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy php.ini-production to php.ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

# Update php.ini
RUN sed -i 's/max_execution_time = .*/max_execution_time = 80/' /usr/local/etc/php/php.ini
RUN sed -i 's/max_input_time = .*/max_input_time = 80/' /usr/local/etc/php/php.ini
RUN sed -i 's/memory_limit = .*/memory_limit = 2048M/' /usr/local/etc/php/php.ini
RUN sed -i 's/post_max_size = .*/post_max_size = 50M/' /usr/local/etc/php/php.ini
RUN sed -i 's/upload_max_filesize = .*/upload_max_filesize = 50M/' /usr/local/etc/php/php.ini