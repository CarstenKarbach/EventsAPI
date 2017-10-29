FROM ubuntu:16.04
MAINTAINER Carsten Karbach (c.karbach@fz-juelich.de)

# install dependencies, certificates, apache
RUN apt-get update \
        && apt-get install -y --no-install-recommends ca-certificates \
        && apt-get -y dist-upgrade \
        && apt-get autoremove -y \
        && apt-get clean all \
        && apt-get install -y apache2 \
        && rm -r /var/lib/apt/lists/*

# Set environment variables. 
ENV HOME /root 
# Define working directory. 
WORKDIR /root

# disable interactive functions 
ENV DEBIAN_FRONTEND noninteractive 
# Install php
RUN apt-get update && \
 apt-get install -y curl zip unzip php libapache2-mod-php \
  php-fpm php-cli php-mysqlnd php-pgsql php-sqlite3 php-redis \
  php-apcu php-intl php-imagick php-mcrypt php-json php-gd php-curl php-mbstring && \
 phpenmod mcrypt && \
 rm -rf /var/lib/apt/lists/*

# Install composer
RUN cd /tmp && curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Setup for EventsAPI application
RUN rm /var/www/html/index.html
# Add EventsApi source
ADD . /var/www/html/EventsAPI
# Activate Rewrite and SSL module
RUN a2enmod rewrite && a2enmod ssl
# Configure Rewrites for silex
RUN cp /var/www/html/EventsAPI/configs/000-default.conf /etc/apache2/sites-available/000-default.conf && \
  cp /var/www/html/EventsAPI/configs/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
# Activate SSL connections
RUN a2ensite default-ssl.conf
# Set access rights for www-data, run composer
RUN php /var/www/html/EventsAPI/utils/install.php

# DO not allow access to root doc
RUN echo "deny from all" > /var/www/html/.htaccess

# Default command       
CMD ["apachectl", "-D", "FOREGROUND"]


