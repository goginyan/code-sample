FROM php:7.4-fpm

RUN apt-get update && apt-get install -y mc wget curl git libfreetype6-dev \
libjpeg62-turbo-dev \
libxslt-dev \
libicu-dev \
libmcrypt-dev \
libxml2-dev \
libzip-dev \
libgpgme-dev \
npm \
&& docker-php-ext-install -j$(nproc) iconv mysqli pdo_mysql zip \
&& docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
&& docker-php-ext-install -j$(nproc) gd

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install xsl
RUN docker-php-ext-install soap
RUN pecl install gnupg \
&& docker-php-ext-enable gnupg

RUN docker-php-ext-configure sockets
RUN docker-php-ext-install sockets

RUN pecl install xdebug-3.0.4 \
    && docker-php-ext-enable xdebug

RUN apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
	&& docker-php-ext-enable imagick

ADD ./php.ini /usr/local/etc/php/php.ini

RUN wget https://getcomposer.org/installer -O - -q \
    | php -- --install-dir=/bin --filename=composer --quiet

RUN mkdir -p /var/www/.gnupg
RUN chmod 777 /var/www/.gnupg
RUN mkdir -p /var/www/.config
RUN chmod 777 /var/www/.config

#Install Cron
RUN apt-get update && apt-get -y install cron

# Add crontab file in the cron directory
ADD crontab /etc/cron.d/app-cron
# Give execution rights on the cron job
RUN chmod 0644 /etc/cron.d/app-cron
# Create the log file to be able to run tail
RUN touch /var/log/cron.log

WORKDIR /code

#RUN cd "/tmp"; \
#    wget -c "https://xdebug.org/files/xdebug-2.6.0.tgz"; \
#    tar -xf xdebug-2.6.0.tgz; \
#    cd xdebug-2.6.0; \
#    phpize; \
#    ./configure; \
#    make && make install; \
#    echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/mod-xdebug.ini;

