# Hoard server image

# Base Docker File
FROM ubuntu:14.04

# Maintainer
MAINTAINER outrunthewolf/marcqualie

# Environment variables
ENV SITEPATH "/home/hoard"
RUN mkdir -p /home/downloads

# Create hoard user
RUN useradd -d /home/hoard hoard

# Update the box
RUN apt-get update

# Install various packages, including composer
RUN apt-get install -y git-core php5 php5-fpm php5-cgi php5-cli spawn-fcgi curl php5-curl php5-mcrypt nano htop openssh-server gcc libpcre3 libpcre3-dev libssl-dev make php5-dev php-pear php5-mysql re2c
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/bin/composer

# Install Phalcon
RUN git clone git://github.com/phalcon/cphalcon.git /home/downloads/phalcon && \
    cd /home/downloads/phalcon/build && \
    ./install

# Create the ini file
RUN echo "extension=phalcon.so" > /etc/php5/mods-available/phalcon.ini
RUN ln -s /etc/php5/mods-available/phalcon.ini /etc/php5/fpm/conf.d/20-phalcon.ini
RUN ln -s /etc/php5/mods-available/phalcon.ini /etc/php5/cli/conf.d/20-phalcon.ini
RUN ln -s /etc/php5/mods-available/phalcon.ini /etc/php5/cgi/conf.d/20-phalcon.ini

# Install the hoard extension
RUN git clone https://github.com/json-c/json-c.git /home/downloads/json-c && \
    cd /home/downloads/json-c && \
    sh autogen.sh && \
    ./configure && \
    make && make install

RUN git clone https://github.com/phalcon/zephir /home/downloads/zephir && \
    cd /home/downloads/zephir && \
    ./install -c

RUN git clone https://github.com/marcqualie/hoard-utils.git /home/downloads/hoard-utils && \
    cd /home/downloads/hoard-utils && \
    make && make install
RUN echo "extension=hoardutils.so" > /etc/php5/mods-available/hoardutils.ini
RUN ln -s /etc/php5/mods-available/hoardutils.ini /etc/php5/fpm/conf.d/20-hoardutils.ini
RUN ln -s /etc/php5/mods-available/hoardutils.ini /etc/php5/cli/conf.d/20-hoardutils.ini
RUN ln -s /etc/php5/mods-available/hoardutils.ini /etc/php5/cgi/conf.d/20-hoardutils.ini

# Add all the files - possibly move to a git pull
#ADD app /home/hoard/app
#ADD bin /home/hoard/bin
#ADD public /home/hoard/public
#ADD composer.json /home/hoard/composer.json

# Compose the shit out of stuff
#RUN cd /home/hoard && \
#    composer update

# Install an nginx server
# Download and install nginx
RUN mkdir -p /home/downloads/nginx && \
    cd /home/downloads/nginx && \
	wget http://nginx.org/download/nginx-1.7.3.tar.gz && \
	tar -zxvf nginx-1.7.3.tar.gz && \
	cd nginx-1.7.3 && \
	./configure --with-http_ssl_module && \
	make && \
	make install

# Set up nginx configurations
RUN mkdir /usr/local/nginx/conf/sites-available && \
	mkdir /usr/local/nginx/conf/sites-enabled

# Add base nginx conf
ADD ./docker/nginx/default_nginx_config /usr/local/nginx/conf/nginx.conf

# Add a default vhost, activate host file
ADD ./docker/nginx/default_nginx_vhost /usr/local/nginx/conf/sites-available/default.conf
RUN ln -s /usr/local/nginx/conf/sites-available/default.conf /usr/local/nginx/conf/sites-enabled/default.conf

# Set up php fpm, restart php
ADD ./docker/nginx/default_php_pool /etc/php5/fpm/pool.d/default.conf
RUN touch /var/log/php-slowlog.log

ENTRYPOINT service php5-fpm restart && \
           /usr/local/nginx/sbin/nginx
