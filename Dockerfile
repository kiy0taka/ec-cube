FROM eccube/php-ext-apache:7.1

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

RUN a2enmod rewrite
RUN { \
        echo 'date.timezone = "Asia/Tokyo"'; \
        echo 'post_max_size = "30M"'; \
        echo 'upload_max_filesize = "30M"'; \
} > /usr/local/etc/php/conf.d/eccube.ini

RUN a2enmod ssl
RUN ln -s /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf
EXPOSE 443

COPY . /var/www/html
WORKDIR /var/www/html
RUN chown -R www-data: /var/www

USER www-data
RUN composer install

USER root
VOLUME [ \
    "/var/www/html/app/Plugin", \
    "/var/www/html/app/config", \
    "/var/www/html/app/proxy", \
    "/var/www/html/app/template" \
]
