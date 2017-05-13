#!/usr/bin/env bash

SCRIPT_DIR="$( cd "$( dirname "$0" )" && pwd )"
DUMP_FILE="$SCRIPT_DIR/../assets/schema.sql"
echo $DUMP_FILE

mysqldump -u root -d althingi > $DUMP_FILE
