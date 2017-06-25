FROM nimmis/apache-php7
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
