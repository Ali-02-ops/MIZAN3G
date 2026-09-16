#!/usr/bin/env sh
set -eu

php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
php artisan route:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
