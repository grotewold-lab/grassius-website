# Grassius Website Regular Maintenence


## Zero-Downtime Maintenence using DNS

The website and its loosely-integrated components (like BLAST) should be hosted on different subdomains. DNS entries may be used as the top-level configuration which cannot be effected by a failure in our systems. By changing DNS entries, the website or any of its components may be swapped to a temporary copy at a different IP address. 


## Website backup

A copy of the website configuration should exist on the production system, in a local clone of this repo. As long as this repo is private, that configuration should be committed and pushed to a dedicated branch (not the main branch). 

Other than the files in this repository, it may be worth backing up the configuration in the system's httpd/apache2 directories. For example, below is the location of the relevant httpd config file on an amazon linux server (similar to CentOS).

```
/etc/httpd/conf/httpd.conf
```

This file is modified during the certbot SSL certification process. In that file we can see references to the files involved in SSL. 

```
SSLCertificateFile /etc/letsencrypt/live/www.eglab-dev.com/fullchain.pem
SSLCertificateKeyFile /etc/letsencrypt/live/www.eglab-dev.com/privkey.pem
Include /etc/letsencrypt/options-ssl-apache.conf
```

## Database backup

As of May 2022, the database is intended to be static apart from occasional updates. If this is the case, this repository should always have an up-to-date database snapshot under `database/build_db.sql.tar.gz`.

In case additional database backups become necessary, the `pg_dump` and `pgsql` commands may be used. These commands should always work on the production system regardless of how or where the database is deployed. The options in the commands below should match the production configuration in `app/Config/Database.php`.

Save a database snapshot to `snapshot.sql`

```
$ pg_dump -h localhost -p 8642 -U postgres --clean --if-exists -f snapshot.sql
```

Restore the snapshot `snapshot.sql`

```
$ psql -h localhost -p 8642 -U postgres -f snapshot.sql
```

Restore the snapshot from this repository

```
$ tar -xzvf database/build_db.sql.tar.gz
$ psql -h localhost -p 8642 -U postgres -f build_db.sql
```


## SSL certificates

## Power Outages

## OS Updates

## Links to External URLs
