#!/bin/sh

mysqldump --extended-insert --no-create-db dbname -hlocalhost -uwawision -p | gzip > /var/www/backup/mysql/mysql_complete_`date +%d`.gz
