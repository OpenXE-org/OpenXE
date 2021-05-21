#!/bin/sh
mysqldump dbname -h127.0.0.1 -u<username> -p<password> | gzip > /tmp/mysql_complete_daily.gz


ftp -inv <ipftpserver> << EOF
user <userftp> <passwordftp>
put /tmp/mysql_complete_daily.gz mysql_complete_`date +%y%m%d_0000`.gz
bye
EOF
rm -f /tmp/mysql_complete_daily.gz
