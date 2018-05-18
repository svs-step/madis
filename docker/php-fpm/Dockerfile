FROM ubuntu:16.04

ENV DEBIAN_FRONTEND noninteractive
RUN apt-get update && apt-get -y upgrade && apt-get install -y software-properties-common python-software-properties apt-transport-https
RUN export LANG=C.UTF-8;add-apt-repository -y ppa:ondrej/php && apt-get update
RUN apt-get install -y libssl-dev php7.1-apcu php7.1-common php-fdomdocument php7.1-xml php7.1-cli \
    php7.1-common php7.1-curl php7.1-fpm php7.1-gd php7.1-intl php7.1-json \
    php7.1-mbstring php7.1-mysql php7.1-opcache php7.1-readline php7.1-soap \
    php7.1-xml php7.1-xdebug curl npm git zip sqlite3 php7.1-sqlite netcat

RUN usermod -s /bin/bash www-data

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    chmod +x /usr/local/bin/composer && \
    composer global require hirak/prestissimo

COPY fpm-pool.conf /etc/php/7.1/fpm/pool.d/www.conf
RUN touch /var/log/php7.1-fpm.log && chown www-data:www-data /var/log/php7.1-fpm.log
RUN mkdir -p /run/php/ /var/www/.composer/
RUN chown www-data:www-data -R /run/php/ /var/www
RUN su www-data -c "composer global require hirak/prestissimo"

RUN echo "xdebug.remote_enable=1" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.idekey=PHPSTORM" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.default_enable=0" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.remote_connect_back=0" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.remote_autostart=0" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.remote_enable=1" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.remote_host=docker.for.mac.host.internal" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.profiler_enable=0" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.profiler_enable_trigger=1" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini \
         && echo "xdebug.profiler_output_dir=/var/www/html/var/log" >> /etc/php/7.1/fpm/conf.d/20-xdebug.ini

RUN echo "export PATH=$PATH\":$HOME/bin\"" >> /root/.bashrc

USER www-data

CMD ["/usr/sbin/php-fpm7.1", "-F"]
