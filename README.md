# Figured Test Project

This is a test app built with Laravel. It runs in Docker containers
 
## Running the test application

This repo utilizes a dev environment partially based on [FiguredLimited/figured-starter](https://github.com/FiguredLimited/figured-starter), but upgraded to PHP 8 and Laravel 8.

To run this, you need docker and docker compose. After cloning this project, set it up with the following steps:

1. cd to the working project directory 
2. `./start-app.sh`
3. `./init-app.sh`
4. Wait for composer, yarn and database migrations to complete. 
5. Visit http://localhost:1515 in a browser to view the application. 
   
NOTE: If port 1515 clashes with something else on your system, change it in docker-compose.yml and try the above again

## Running laravel commands

Because we are running under a docker image called app, we have to use docker-compose exec. For example:

    docker-compose exec app php artisan migrate:status 

## Running yarn

    docker-compose exec app yarn

## Running composer

    docker-compose exec app composer install

## Running migrations

    docker-compose exec app php artisan migrate:fresh
    
## Running tests

    docker-compose exec app ./vendor/bin/phpunit

OR

    docker-compose exec app php artisan test

## Connecting to Database from host machine

You can also use a DB viewer like the one built into PHPStorm to interact and monitor the Database activity

* Database Type: MySQL
* Host: localhost
* Port: 33061
* User: figured_user
* Password: password
* Database: figured_test

P.S. I'm aware Figured uses Mongo, but I am not familiar with working with it in Docker and time is a bit short
