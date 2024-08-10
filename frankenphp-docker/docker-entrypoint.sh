#!/bin/sh
set -e

# Start cron service
service cron start

if [ "$1" = 'frankenphp' ] || [ "$1" = 'php' ] || [ "$1" = 'artisan' ]; then
  # Install the project the first time PHP is started
  # After the installation, the following block can be deleted
  if [ ! -f composer.json ]; then
    rm -Rf tmp/
    composer create-project laravel/laravel tmp --prefer-dist --no-progress --no-interaction --no-install --no-scripts

    cd tmp
    cp -Rp . ..
    cd ..
    rm -Rf tmp/
  fi

  if [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
    composer install --prefer-dist --no-progress --no-interaction
  fi

  setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX storage
  setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX storage

  if [ ! -f ".env" ]; then
    cp .env.example .env
    sed -i "s/APP_ENV=local/APP_ENV=$APP_ENV/g" .env

    sed -i "s/DB_CONNECTION=sqlite/DB_CONNECTION=$DB_CONNECTION/g" .env
    sed -i "s/# DB_HOST=127.0.0.1/DB_HOST=$DB_HOST/g" .env
    sed -i "s/# DB_PORT=3306/DB_PORT=$DB_PORT/g" .env
    sed -i "s/# DB_DATABASE=laravel/DB_DATABASE=$DB_DATABASE/g" .env
    sed -i "s/# DB_USERNAME=root/DB_USERNAME=$DB_USERNAME/g" .env
    sed -i "s/# DB_PASSWORD=/DB_PASSWORD=$DB_PASSWORD/g" .env

    # Install octane
    composer require laravel/octane
    php artisan octane:install --server=frankenphp

    php artisan key:generate
  fi

  if grep -q ^DB_CONNECTION= .env; then
    if [ "$(find ./database/migrations -iname '*.php' -print -quit)" ]; then
      php artisan migrate --no-interaction
    fi
  fi

fi

exec docker-php-entrypoint "$@"
