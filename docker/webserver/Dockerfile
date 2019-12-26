FROM ubuntu:18.04

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get update && \
    # apt-get dist-upgrade -y && \
    apt-get install -y \
      apache2 \
      php7.2 \
      php7.2-cli \
      libapache2-mod-php7.2 \
      php-apcu \
      php-xdebug \
      php7.2-gd \
      php7.2-json \
      php7.2-ldap \
      php7.2-mbstring \
      php7.2-mysql \
      php7.2-pgsql \
      php7.2-sqlite3 \
      php7.2-xml \
      php7.2-xsl \
      php7.2-zip \
      php7.2-soap \
      php7.2-opcache \
      nano \
      sendmail \
      composer \
      php-curl

COPY apache-host.conf /etc/apache2/sites-available/000-default.conf
COPY run.sh /usr/local/bin/run
COPY configmail.sh /usr/local/bin/configmail
RUN chmod +x /usr/local/bin/run /usr/local/bin/configmail

EXPOSE 80
CMD ["/usr/local/bin/run"]
