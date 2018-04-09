#!/bin/bash

chromedriver --url-base=/wd/hub &
CDPID="$!"
trap "kill ${CDPID}" exit
bin/console server:start

vendor/bin/codecept -vvv run acceptance --env pgsql,chrome "$@"