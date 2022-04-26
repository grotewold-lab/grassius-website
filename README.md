# grassius-website
Source code for grassius.org

This is a cleaned-up copy of the repository that was called "new-grassius-no-tripal". This contains only what is necessary to host the website. The logic for building the database is contained in a separate repository called grassius-db-builder.


### avoiding bloat

This repository contains a compressed database snapshot, output from grassius-db-builder. 

```
database/build_db.sql.tar.gz
```

This file cannot be efficiently version-controlled. This may lead to a gradual increase in the size of the repository. To mitigate this, the main branch of this repository should only contain a minimal number of revisions to the snapshot. 

A new branch should always be created when testing revisions to the database. As long as the new branch is never directly merged into main, any number of revisions can be committed for convenience during testing. On the rare occassion that a new version of the database is finalized, its branch should be deleted and only the final snapshot should be commited to the main branch.


### deploy with docker

Deploy both the database and the website in docker containers. 

Requires only [docker](https://docs.docker.com/get-docker/) and [docker-compose](https://docs.docker.com/compose/install/)

```
git clone https://github.com/grotewold-lab/grassius-website.git
cd grassius-website
docker-compose up --build
```

It takes a few minutes to build the database. Then the website is visible at https://localhost:8080/

The two running containers (website and database) are sufficient together. They can also work independently.

### deploy with php built-in server

Deploy the website quickly.

The new-grassius database must be running somewhere on the network. Edit ```app/Config/Database.php``` accordingly

Requires [composer](https://getcomposer.org/). Additional dependencies are handled in ```composer install```.

```
git clone https://github.com/grotewold-lab/grassius-website.git
cd grassius-website
composer install
php spark serve
```

then the local website is visible at http://localhost:8000/
