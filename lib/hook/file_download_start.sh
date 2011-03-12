#!/bin/sh
# Created @ 26.02.2011 by TheFox

export LOG DATE
BASH_SCRIPT_BASENAME=`basename $0`
LOG=./log/$BASH_SCRIPT_BASENAME.log
DATE=`date +"%Y/%m/%d %H:%M:%S"`
PACKET_ID=$1
PACKET_NAME=$2
FILE_ID=$3
FILE_PATH=$4


#echo "$DATE $USER $PACKET_ID '$PACKET_NAME' $FILE_ID" >> $LOG


# EOF
