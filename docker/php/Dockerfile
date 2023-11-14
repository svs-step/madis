FROM php:8.1-fpm-bullseye

ENV DEBIAN_FRONTEND noninteractive


# PHP INSTALL
# ===========


# Configure PHP

COPY fpm-pool.conf "$PHP_INI_DIR/pool.d/www.conf"

COPY php.ini "$PHP_INI_DIR/php.ini"

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && chmod +x /usr/local/bin/composer

RUN apt-get update && apt-get install -y \
		libfreetype6-dev \
		libjpeg62-turbo-dev \
		libpng-dev \
    libzip-dev zlib1g-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql  \
    && docker-php-ext-install zip

# NPM INSTALL (used for webpack-encore)
# ======================================

RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - && apt-get update && apt-get install -y nodejs


# SECURITY CHECKER
# ======================================
RUN curl -L https://github.com/fabpot/local-php-security-checker/releases/download/v2.0.6/local-php-security-checker_2.0.6_linux_amd64 --output /bin/php-security-checker && chmod 755 /bin/php-security-checker


# INSTALL WKHTMLTOPDF
# ===================
RUN apt-get install -y fontconfig libfreetype6 libjpeg62-turbo libpng16-16 libxrender1 xfonts-75dpi xfonts-base libxext6 netcat-openbsd libssl-dev
RUN dpkgArch="$(dpkg --print-architecture)"; \
    curl -L https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6-1/wkhtmltox_0.12.6-1.buster_${dpkgArch}.deb --output wkhtmltox_0.12.6-1.buster_${dpkgArch}.deb && \
    dpkg -i wkhtmltox_0.12.6-1.buster_${dpkgArch}.deb



# FINISH CONFIGURATION
# ====================

# Clean APT
RUN rm -rf /var/lib/apt/lists/* && chown -R www-data:www-data "/var/www/" && mkdir -p "/var/www/.npm" && chown -R www-data:www-data "/var/www/.npm"

# Define Projet configuration

USER www-data
WORKDIR /var/www/gestion-rgpd

