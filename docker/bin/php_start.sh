#!/usr/bin/env bash
#composer install
#php artisan key:generate
#php artisan storage:link
#db container maybe is not start yet
#php artisan migrate --no-interaction
#php artisan db:seed --no-interaction
#php artisan passport:keys
#php artisan passport:client --password --provider=admins --name='Admins'
#php artisan passport:client --password --provider=users --name='Users'
#php artisan passport:client --personal --name='Users'
sleep 1
#ram volume not mounted yet
#php artisan key:generate --env=testing
#php artisan migrate --no-interaction --env=testing
#php artisan migrate:fresh --env=testing
#php artisan db:seed --env=testing
exec php-fpm --nodaemonize
