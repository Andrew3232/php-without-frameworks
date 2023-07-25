# php-without-frameworks

## Technologies

* PHP 8.1
* [Apache](https://www.apache.org/)
* [Docker](https://www.docker.com/)
* [Docker-compose](https://docs.docker.com/compose/)
* [PhpUnit](https://phpunit.de/)
* MySQL 8

## Install

The following sections describe dockerized environment.

Just keep versions of installed software to be consistent with the team and production environment (see [Pre-requisites](#pre-requisites) section).

Start application docker containers:
``` bash
docker-compose up -d
```

Install composer dependencies and generate app key:
```bash
docker exec -it php-app composer install
```

Database migrations install 
```bash
docker exec -it php-app php migration.php
```

Application server should be ready on `http://localhost:8080`

phpMyAdmin should be ready on `http://localhost:8081`


## Tests

## Debugging 

To debug the application we highly recommend you to use xDebug, it is already pre-installed in dockerized environment, but you should setup your IDE.