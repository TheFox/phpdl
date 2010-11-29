#!/bin/sh
# Created @ 29.10.2010 by TheFox@fox21.at

export LOG DATE
DATE=`date +"%Y/%m/%d %H:%M:%S"`
CONFIGPATH=lib/config.php


echo install start

if [ "$0" == "./install.sh" ] || [ "$0" == "install.sh" ]
then
	echo cd ..
	cd ..
fi 

mkdir tmp cache cache/tpl_c cache/html downloads downloads/finished downloads/loading log
chmod 777 tmp cache cache/tpl_c cache/html downloads downloads/finished downloads/loading install

if [ ! -e $CONFIGPATH ]
then
	touch $CONFIGPATH
	chmod 666 $CONFIGPATH
fi

echo done
echo now run install/install.php in your browser.


# EOF
