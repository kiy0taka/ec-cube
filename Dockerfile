FROM eccube/php-ext-apache

RUN a2enmod ssl
RUN ln -s ${APACHE_CONFDIR}/sites-available/default-ssl.conf ${APACHE_CONFDIR}/sites-enabled/default-ssl.conf

