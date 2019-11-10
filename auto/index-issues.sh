#!/usr/bin/env bash

SCRIPT=$( cd "$( dirname "$0" )" && pwd )/../public/index.php

php ${SCRIPT} index:issue --assembly=$1 --issue=$2 --category=$3
