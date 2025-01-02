FROM php:8.1-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    vim \
    libpq-dev \
    libxml2-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    gd \
    zip \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PDO, MySQL, and other extensions one by one
RUN docker-php-ext-install pdo && echo "Step 3: PDO extension installed"
RUN docker-php-ext-install pdo_mysql && echo "Step 4: PDO MySQL extension installed"
RUN docker-php-ext-install mbstring && echo "Step 5: mbstring extension installed"
RUN docker-php-ext-install exif && echo "Step 6: exif extension installed"
RUN docker-php-ext-install pcntl && echo "Step 7: pcntl extension installed"
RUN docker-php-ext-install bcmath && echo "Step 8: bcmath extension installed"

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/* \
    && echo "Step 9: Clean up completed"

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]

