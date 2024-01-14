#syntax=docker/dockerfile:1.4

FROM php:8.3-cli-alpine

COPY --from=mlocati/php-extension-installer --link /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer/composer:2-bin --link /composer /usr/bin/composer

RUN apk add --no-cache  \
      git \
    ;

RUN set -eux; \
    install-php-extensions \
    	intl \
        pcov \
        pcntl \
    	zip \
    ;

RUN set -eux; \
    curl -OL https://github.com/composer-unused/composer-unused/releases/latest/download/composer-unused.phar; \
    chmod a+x composer-unused.phar; \
    mv composer-unused.phar /usr/local/bin/composer-unused

RUN set -eux; \
    curl -OL https://github.com/maglnet/ComposerRequireChecker/releases/latest/download/composer-require-checker.phar; \
    chmod a+x composer-require-checker.phar; \
    mv composer-require-checker.phar /usr/local/bin/composer-require-checker
