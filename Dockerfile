FROM php:7.4-apache

LABEL Name=lumen Version=1.0.1

#hacer que la consola no sea interactiva
ENV DEBIAN_FRONTEND=noninteractive
#--------------------------------

#instalar apache y php
RUN apt-get update

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        git-core \
        openssl \
        libssl-dev \
        python3 \
        libonig-dev \
        libzip-dev \
        libxml2-dev \
        libpq-dev

RUN docker-php-ext-install gd mbstring pdo pdo_mysql zip xml

#herramientas utiles
RUN apt-get install -y wget && \
    apt-get install -y curl
#-------------------------

# memory limit php
RUN echo "memory_limit=-1" > /usr/local/etc/php/conf.d/memory-limit.ini
#---------------------------------------------------------------------

# limite de archivos
RUN echo "file_uploads = On \n memory_limit = 64M \n upload_max_filesize = 64M \n post_max_size = 64M \n max_execution_time = 600" > /usr/local/etc/php/conf.d/uploads.ini
#---------------------------------------------------------------------------

#composer, git y node
RUN curl -s https://getcomposer.org/installer | php

RUN mv composer.phar /usr/local/bin/composer

RUN apt-get install -y git-core openssl libssl-dev python3
#----------------------

# Cleanup
RUN apt-get autoremove -y
#---------------------

#configurar proyecto
EXPOSE 80

ENV APP_HOME /var/www/html

RUN mkdir -p /opt/data/public && \
    rm -r /var/www/html && \
    ln -s /opt/data/public $APP_HOME

#--------traer config de apache--------
RUN rm /etc/apache2/sites-enabled/000-default.conf
ADD 000-default.conf /etc/apache2/sites-enabled
#===============================================

RUN a2enmod rewrite

WORKDIR /opt/data