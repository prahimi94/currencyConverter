#!/bin/sh

set -e  #stop script if error happened

composer install --no-interaction --prefer-dist --optimize-autoloader
npm install
npm run build

# copy .env.example to .env if .env does not exist
if [ ! -f ".env" ]; then
  cp .env.example .env
fi


# run laravel initialization commands
php artisan key:generate
php artisan config:clear
php artisan cache:clear

# run server (php artisan serve + npm run dev) (defined in package.json using concurrently)
npm run start