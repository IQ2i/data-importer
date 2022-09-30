FROM php:8.0-cli-alpine

RUN apk add --no-cache git

RUN curl --location --output /usr/local/bin/install-php-extensions https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
	&& chmod +x /usr/local/bin/install-php-extensions \
	&& sync \
	&& install-php-extensions \
       @composer \
       intl \
       pcntl \
	   pcov
