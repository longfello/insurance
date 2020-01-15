#!/usr/bin/env bash

if [[ $# -lt 4 ]] ; then
    echo 'Usage: import_db.sh <DB_USER> <DB_PASSWORD> <DB_HOST> <DB_NAME> [<DOMAIN>]'
    exit 0
fi

### DIRECTORIES ###
SCRIPT=$(readlink -f $0)
SCRIPT_DIR=`dirname ${SCRIPT}`
ROOT_DIR=`dirname ${SCRIPT_DIR}`
TEMP_DIR=${SCRIPT_DIR}/runtime
DUMP_FILE=${ROOT_DIR}/bullosafe.sql

# DOMAINS
DEV_DOMAIN="bullosafe.kvk-dev.pp.ua"

# DB
DB_USERNAME=${1}
DB_PASSWORD=${2}
DB_HOST=${3}
DB_NAME=${4}

echo "Copying dump file into temp dir"
cp ${DUMP_FILE} ${TEMP_DIR}/dump.sql

if ! [ -z "$5" ]; then
    echo "Changing ${DEV_DOMAIN} to ${5}"
    sed -i "s/${DEV_DOMAIN}/${5}/g" ${TEMP_DIR}/dump.sql
fi

echo "Importing dump"
mysql --default-character-set=utf8 -u ${DB_USERNAME} -p${DB_PASSWORD} -h ${DB_HOST} -D ${DB_NAME} < ${TEMP_DIR}/dump.sql

echo "Removing temp file"
rm ${TEMP_DIR}/dump.sql