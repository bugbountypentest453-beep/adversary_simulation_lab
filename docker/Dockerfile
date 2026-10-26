FROM php:8.4-apache

RUN a2enmod rewrite

WORKDIR /var/www/html

# crucial to ster in .htacces rewrite
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' \
    /etc/apache2/apache2.conf

EXPOSE 80
