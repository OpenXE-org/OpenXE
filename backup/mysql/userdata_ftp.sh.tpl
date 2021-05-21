#!/bin/sh
tar cfz /tmp/userdata.tar.gz ../../userdata


ftp -inv <ipftpserver> << EOF
user <userftp> <passwordftp>
put /tmp/userdata.tar.gz userdata_complete_`date +%d`.tar.gz
bye
EOF
rm -f /tmp/userdata.tar.gz
