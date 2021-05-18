#!/usr/bin/env bash

## Copy functional .env
cp .env.example .env

## Prime the sqlite DB file for use by unit tests
docker-compose exec app touch database/figured_test.sqlite

## Run composer and yarn to obtain dependencies
docker-compose exec app composer install
docker-compose exec app yarn

## Just for this test, migrate from scratch and seed
docker-compose exec app php artisan migrate:fresh --seed

## Just do a migrate status so we can visually confirm all has gone well
docker-compose exec app php artisan migrate:status

