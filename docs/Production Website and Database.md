# Deploying website + database for production


## Overview

The production system may be any linux system with apache2/httpd. SSL should be enabled using certbot ([general instructions](https://certbot.eff.org/)) ([amazon instructions](https://docs.aws.amazon.com/AWSEC2/latest/UserGuide/SSL-on-amazon-linux-2022.html#letsencrypt-2022)). This repo should be cloned somewhere on the production system. The configuration files in the clone should be adjusted. The configuration should NOT be commited to the main branch. Composer should be used to ensure that the required php modules are enabled. The database should be deployed. Finally, the contents of the local clone should be copied to folders served by apache.

## Configuration

The configuration should be adjusted by modifying files in the production system's clone of this repository. Use `git diff` to keep track of configuration changes. Below is an example of the expected output after configuring for production. See configuration details at the bottom of this file for a complete list of config files.

```
$ git diff --name-status
M       app/Config/App.php
M       app/Config/Database.php
M       app/Controllers/BaseController.php
M       docker-compose.yml
M       public/index.php
```

Use composer install to verify that the necessary php modules are enabled.
```
$ composer install
...
Nothing to install, update or remove
...
```



## Database Deployment

The website may be configured to connect to a databse anywhere on the internet (see `app/Config/Database.php`). However, the simplest solution is to use `docker-compose` to host the database locally on the production system. In that case the website would connect using `'hostname' => 'localhost'`.

It is recommended to first delete the `grassius` service (a copy of the website) from `docker-compose.yml`. Only the `db` service is necessary. Then run the `docker-compose` command to start the database. During this process a database snapshot will be loaded from `database/build_db.sql.tar.gz`

```
$ docker-compose up --build -d
```

`docker-compose` may also be used to destroy and re-create the database container. This is necessary to apply changes to docker-compose.yml. This could be used to apply updates to the database, but should be avoided because it causes some downtime. See "Applying Updates" below.

```
$ docker-compose down
$ docker-compose up --build -d
```

## Website Deployment

The contents of the repo clone on the production system should be copied to the folder served by apache. For example, if the apache DocumentRoot is set as `/var/www/html/codeigniter4/public`, the following commands could be used.

```
$ sudo cp -r app/Config/. /var/www/html/codeigniter4/app/Config/
$ sudo cp -r app/Views/. /var/www/html/codeigniter4/app/Views/
$ sudo cp -r app/Controllers/. /var/www/html/codeigniter4/app/Controllers/
$ sudo cp -r public/. /var/www/html/codeigniter4/public/
$ sudo cp -r writable/. /var/www/html/codeigniter4/writable/
```

The "writable" folder may need to be given relaxed permissions

```
$ sudo chmod 777 -R /var/www/html/codeigniter4/writable/
```





## Configuration details and examples

Below is a complete list of relevant configuration files that should be considered.

### public/index.php
switch environment from "development" to "production"

this prevents the codeigniter debugging toolbar from appearing on the website
```
define("ENVIRONMENT","production");
```
    
### app/Config/App.php
set baseURL to match the production domain name
```
public $baseURL = 'https://grassius.org/';
```

### app/Config/Database.php
configure connection to the production database
```
public $default = [
    ...
    'hostname' => 'localhost',
    ...
    'port'     => 8642,
];
```
    
    
### app/Controllers/BaseController.php
insert `force_https();` to redirect all http requests to https
```
public function initController(...)
{
    // Do Not Edit This Line
    parent::initController($request, $response, $logger);

    force_https();
```


### docker-compose.yml
remove "grassius" service

configure local docker container of production database
```
services:
  db:
    build: database
    ...
    ports:
      - '8642:5432'
```
          
  The first port number, to the left of the colon, should be set to an unoccupied port on the production system. The second port number should not be changed.
        
        
        









