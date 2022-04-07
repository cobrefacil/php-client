FROM php:7.1-fpm

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git

# Install Xdebug
RUN pecl install xdebug-2.9.8
RUN docker-php-ext-enable xdebug
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/php.ini
RUN echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/php.ini

# Get latest Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Create a symbolic link
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Set user to run commands
USER $user

# Set working directory
WORKDIR /var/www
