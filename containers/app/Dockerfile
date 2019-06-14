FROM php:7.1-cli

RUN set -e \
    && apt-get update -y -qq \
    && apt-get install -y -qq \
       build-essential \
       git \
       unzip \
    && apt-get autoremove -y \
    && apt-get autoclean -y \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -O https://nodejs.org/dist/v10.16.0/node-v10.16.0-linux-x64.tar.xz \
    && tar xvf node-v10.16.0-linux-x64.tar.xz \
    && mv node-v10.16.0-linux-x64 /usr/local \
    && ln -s /usr/local/node-v10.16.0-linux-x64/bin/* /usr/local/bin/

RUN docker-php-ext-install pdo pdo_mysql

RUN mkdir -p /webapp
WORKDIR /webapp

COPY composer.* /webapp/
RUN composer install --no-autoloader

COPY package.* /webapp/
RUN npm install

COPY . /webapp/
RUN composer dump-autoload

VOLUME \
    "/webapp/bootstrap/cache" \
    "/webapp/node_modules" \
    "/webapp/storage" \
    "/webapp/vendor"

EXPOSE 3306
