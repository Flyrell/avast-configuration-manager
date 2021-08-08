#!/bin/sh

cd /var/www/symfony && php "bin/console" "config:load" "$@"
