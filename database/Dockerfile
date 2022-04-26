FROM postgres:9

COPY build_db.sql.tar.gz /docker-entrypoint-initdb.d/

WORKDIR /docker-entrypoint-initdb.d
RUN tar -xzvf build_db.sql.tar.gz
RUN rm build_db.sql.tar.gz
WORKDIR /