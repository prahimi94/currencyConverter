#!/bin/sh

set -e  #stop script if error happened

# copy .env.example to .env if .env does not exist
if [ ! -f ".env" ]; then
  cp .env.example .env
fi

# Create SQLite DB file if it doesn't exist
if [ ! -f "database/database.sqlite" ]; then
  touch database/database.sqlite
fi


# run laravel initialization commands
php artisan key:generate
php artisan config:clear
php artisan cache:clear

# wait for services to be up
/wait-for-it.sh redis:6379 --timeout=30 --strict -- echo "Redis is up"
/wait-for-it.sh influxdb:8086 --timeout=30 --strict -- echo "InfluxDB is up"

# run server (php artisan serve + npm run dev) (defined in package.json using concurrently)
npm run start