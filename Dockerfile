FROM eccube/php-ext-apache

RUN a2enmod rewrite headers ssl
# Enable SSL
RUN ln -s /etc/apache2/sites-available/default-ssl.conf /etc/apache2/sites-enabled/default-ssl.conf
EXPOSE 80 443

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
# Override with custom configuration settings
COPY dockerbuild/php.ini $PHP_INI_DIR/conf.d/

COPY . ${APACHE_DOCUMENT_ROOT}

ENV APACHE_DOCUMENT_ROOT=/var/www/html
WORKDIR ${APACHE_DOCUMENT_ROOT}

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN composer config -g repos.packagist composer https://packagist.jp \
  && composer global require hirak/prestissimo \
  && composer install \
    --no-scripts \
    --no-autoloader \
    --no-dev -d ${APACHE_DOCUMENT_ROOT} \
  && chown -R www-data:www-data ${APACHE_DOCUMENT_ROOT} \
  && chown www-data:www-data /var/www \
  ;

USER www-data
RUN composer dumpautoload -o --apcu --no-dev

RUN if [ ! -f ${APACHE_DOCUMENT_ROOT}/.env ]; then \
        cp -p .env.dist .env \
        ; fi

# trueを指定した場合、DBマイグレーションやECCubeのキャッシュ作成をスキップする。
# ビルド時点でDBを起動出来ない場合等に指定が必要となる。
ARG SKIP_INSTALL_SCRIPT_ON_DOCKER_BUILD=false

RUN if [ ! -f ${APACHE_DOCUMENT_ROOT}/var/eccube.db ] && [ ! ${SKIP_INSTALL_SCRIPT_ON_DOCKER_BUILD} = "true" ]; then \
        composer run-script installer-scripts && composer run-script auto-scripts \
        ; fi

USER root
