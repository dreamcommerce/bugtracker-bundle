#!/bin/bash

cd `dirname $0`

if [ ! -f .env ]; then
    echo No .env file, copy file .env.default and adjust values
    exit 1
fi

. ./.env

if ! which docker &> /dev/null; then
    echo Unable to find docker command
    exit 2
fi

V=`docker -v | awk '{ print $3 }' | awk -F. '{ print $1 }'`
if [ "$V" -lt 19 ]; then
    echo Docker is too old, version 19.0 is required
    exit 3
fi

if ! which docker-compose &> /dev/null; then
    echo Unable to find docker-compose command
    exit 4
fi

if ! docker info &> /dev/null; then
    echo 'Unable to communicate with docker, is it running?'
    exit 5
fi

docker-compose build -m ${BUILD_MEMORY}M
docker-compose run bugtracker-php ${@}
exit $?
