#!/bin/bash

if [ "$1" == "prod" ]; then
	rm -R -f var/cache/* var/log/*
	php bin/console cache:clear --env=prod --no-debug
	php bin/console assets:install
	php bin/console assetic:dump --env=prod --no-debug
	chmod -R 777 var/cache var/log
elif [ "$1" == "dev" ]; then
    php bin/console cache:clear
    php bin/console assets:install --symlink
    php bin/console assetic:dump
    chmod -R 777 var/cache var/log
else
	echo "parameter expected: dev | prod"
fi
