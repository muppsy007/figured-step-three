#!/usr/bin/env bash

## Copy functional .env
cp .env.example .env

## Run composer and yarn to obtain dependencies
docker-compose exec app composer install
docker-compose exec app yarn

## Run migrations to set up models to support functional test app
docker-compose exec app php artisan migrate

## Just for this test, migrate from scratch and seed
docker-compose exec app php artisan migrate:fresh --seed

## Just do a migrate status so we can visually confirm all has gone well
docker-compose exec app php artisan migrate:status

