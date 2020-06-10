FROM php:7.3.8-fpm
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/
COPY sources.list .
RUN mv /etc/apt/sources.list /etc/apt/sources.list.bak && mv sources.list /etc/apt/
RUN apt-get update
RUN apt-get install unzip libpng-dev -y
#RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
#RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'c5b9b6d368201a9db6f74e2611495f369991b72d9c8cbd3ffbc63edff210eb73d46ffbfce88669ad33695ef77dc76976') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
#RUN php composer-setup.php
#RUN php -r "unlink('composer-setup.php');"
#RUN mv  composer.phar /usr/bin/composer
#RUN composer config -g repos.packagist composer https://mirrors.cloud.tencent.com/composer/
RUN docker-php-ext-install pdo_mysql gd bcmath
RUN pecl install redis -y
RUN docker-php-ext-enable redis
