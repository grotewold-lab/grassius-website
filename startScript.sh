#!/bin/bash

a2enmod ssl
mv /000-default.conf /etc/apache2/sites-enabled/000-default.conf
service apache2 reload

/usr/sbin/apache2ctl -D FOREGROUND
