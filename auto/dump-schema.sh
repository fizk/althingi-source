#!/usr/bin/env bash

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #
#
#   A script that can be run on a machine that has MySQL (mysqldump) installed.
#   It dumps the Althingi-DB schema into a file.
#
#   ENVIRONMENT VARIABLES:
#       DB_USER
#       DB_NAME
#
#
#   Dumps the file into ./assets/database/schema.sql
#
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #


if [ -z "${DB_USER}" ]; then
    DATABASE_USER='root'
else
    DATABASE_USER=${DB_USER}
fi

if [ -z "${DB_NAME}" ]; then
    DATABASE_NAME='althingi'
else
    DATABASE_NAME=${DB_NAME}
fi

SCRIPT_DIR="$( cd "$( dirname "$0" )" && pwd )"
DUMP_FILE="$SCRIPT_DIR/../assets/database/schema.sql"

echo -e "====== mysqldump [{$DATABASE_USER}, {$DATABASE_NAME}] ======"

mysqldump -u $DATABASE_USER -d $DATABASE_NAME > $DUMP_FILE

echo -e "====== mysqldump [{$DUMP_FILE}] ======"
