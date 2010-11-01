#!/bin/sh
# Created @ 29.10.2010 by TheFox@fox21.at

export LOG DATE
#LOG=/var/log/scripts/.log
DATE=`date +"%Y/%m/%d %H:%M:%S"`
CONFIGPATH=lib/config.php

#echo "$DATE $USER" >> $LOG
echo install

mkdir tmp cache cache/tpl_c cache/html downloads downloads/finished downloads/loading log
chmod 777 tmp cache cache/tpl_c cache/html downloads downloads/finished downloads/loading
chmod 755 install

if [ ! -e $CONFIGPATH ]
then
	touch $CONFIGPATH
	chmod 646 $CONFIGPATH
fi

echo done
echo now run install/install.php in your browser.


# EOF
