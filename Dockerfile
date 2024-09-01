FROM php:8.1-apache

# Instala extensiones de PHP necesarias
RUN docker-php-ext-install mysqli

# Habilita OPcache
RUN docker-php-ext-enable opcache
RUN echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini
RUN echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Exponer el puerto 8888
EXPOSE 8888

# Copia los archivos del proyecto al contenedor
COPY . /var/www/html/

# Configuración para asegurar que Apache pueda escribir archivos
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/
