FROM atsanna/codeigniter4:v4.1.5-php7.4

RUN apt-get update
RUN apt-get -y upgrade
RUN apt-get -y install libpng-dev libzip-dev

RUN docker-php-ext-install zip gd pgsql pdo_pgsql
RUN docker-php-ext-enable zip gd pgsql pdo_pgsql

COPY app/. /var/www/html/codeigniter4/app/
COPY public/. /var/www/html/codeigniter4/public/
COPY writable/. /var/www/html/codeigniter4/writable/
COPY vendor/. /var/www/html/codeigniter4/vendor/
COPY composer.json /var/www/html/codeigniter4/
COPY composer.lock /var/www/html/codeigniter4/

RUN chmod 777 -R /var/www/html/codeigniter4/writable

WORKDIR /var/www/html/codeigniter4
RUN composer install

RUN mkdir -p /etc/ssl/localcerts
COPY ./ssl-certs/apache.pem /etc/ssl/localcerts/apache.pem
COPY ./ssl-certs/apache.key /etc/ssl/localcerts/apache.key
COPY ./ssl-certs/000-default.conf /000-default.conf

COPY startScript.sh /startScript.sh
RUN chmod +x /startScript.sh
