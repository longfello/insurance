#!/usr/bin/env bash
CDIR=$(realpath $(dirname "$0"))

# RSYNC
DIR_FROM=`pwd`/../
DIR_TO="webuser@bullosafe.ru:/home/webuser/bullosafe.ru"
RSYNC_PASSWORD="ghjtrn9"

### DIRECTORIES ###
CCDIR=$(realpath $(dirname ${BASH_SOURCE[0]}))
ROOTDIR=$(realpath ${CCDIR}/../)
TEMPDIR=$(realpath ${ROOTDIR}/console/runtime)

### EXECUTABLES ###
PHP=`which php`
MYSQLDUMP=`which mysqldump`

# DOMAINS
DEV_DOMAIN="bullosafe.kvk-dev.pp.ua"
PROD_DOMAIN="bullosafe.ru"

# DB
DB_USERNAME="admin"
DB_PASSWORD="ghjtrn9"
DB_HOST="192.168.1.67"
DB_NAME="bullosafe"
PROD_DB_USER="bullosafe"
PROD_DB_PASSWORD="ghjtrn9"
PROD_DB_NAME="bullosafe"
PROD_DB_HOST="185.87.48.72"

echo "Copy $DIR_FROM -> $DIR_TO"
sshpass -p $RSYNC_PASSWORD rsync -avc --stats --delete --exclude '.env' --exclude 'frontend/web/orders' $DIR_FROM $DIR_TO

# echo "Apply database"

#$MYSQLDUMP -u $DB_USERNAME -p$DB_PASSWORD $DB_NAME -h $DB_HOST > $TEMPDIR/dump.sql
#sed -i "s/${DEV_DOMAIN}/${PROD_DOMAIN}/g" $TEMPDIR/dump.sql

#$MYSQLDUMP -u ${PROD_DB_USER} -p${PROD_DB_PASSWORD} -h ${PROD_DB_HOST} --add-drop-table --no-data ${PROD_DB_NAME} | grep ^DROP | mysql -u ${PROD_DB_USER} -p${PROD_DB_PASSWORD} -h ${PROD_DB_HOST} -D ${PROD_DB_NAME}
#mysql --default-character-set=utf8 -u ${PROD_DB_USER} -p${PROD_DB_PASSWORD} -h $PROD_DB_HOST -D ${PROD_DB_NAME} < "${TEMPDIR}/dump.sql"
 
