#!/bin/sh
# Created @ 29.10.2010 by TheFox@fox21.at

export LOG DATE
#LOG=/var/log/scripts/.log
DATE=`date +"%Y/%m/%d %H:%M:%S"`


#echo "$DATE $USER" >> $LOG
echo install
pwd

mkdir cache tmp
chmod 777 cache tmp

echo done

# EOF
