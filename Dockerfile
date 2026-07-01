FROM php:8.2-apache

# Cài đặt extension MySQLi cho PHP
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật mod_rewrite của Apache phục vụ cấu hình .htaccess (nếu có)
RUN a2enmod rewrite

EXPOSE 80