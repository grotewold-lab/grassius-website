# new-grassius-no-tripal
A non-tripal website using a chado database.

This is based on the old grassius website with different queries.

### deploy with docker

Deploy both the database and the website in docker containers. 

Requires only [docker](https://docs.docker.com/get-docker/) and [docker-compose](https://docs.docker.com/compose/install/)

```
git clone https://github.com/grotewold-lab/new-grassius-no-tripal.git
cd new-grassius-no-tripal
docker-compose up --build
```

It takes a few minutes to build the database. Then the website is visible at https://localhost:8080/

The two running containers (website and database) are sufficient together. They can also work independently.

### deploy with php built-in server

Deploy the website quickly.

The new-grassius database must be running somewhere on the network. Edit ```app/Config/Database.php``` accordingly

Requires [composer](https://getcomposer.org/). Additional dependencies are handled in ```composer install```.

```
git clone https://github.com/grotewold-lab/new-grassius-no-tripal.git
cd new-grassius-no-tripal
composer install
php spark serve
```

then the local website is visible at http://localhost:8000/


### update database snapshot

This repository includes a snapshot of the database. Use the following commands to update that snapshot based on a running local database.

```
cd database
pg_dump -h localhost -p 5432 -U postgres --clean --if-exists -f build_db.sql
sed -i '0,/^/s//CREATE ROLE DRUPAL;/' build_db.sql
tar -zcvf build_db.sql.tar.gz build_db.sql
rm build_db.sql
```
