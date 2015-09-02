#!/bin/sh

ps aux | grep '[r]un_sync.php'
if [ $? -ne 0 ]
then
	DIR="$( cd "$( dirname "$0" )" && pwd )"
	/usr/bin/php $DIR/run_sync.php > /dev/null &
fi

########## Create a Cron Job (Scheduled Task) ##########
#
# The basic format of a crontab schedule consists of 6 fields, placed on a single line and separated by spaces, formatted as follows:
# minute hour day month day-of-week command-line-to-execute
#
# 0 * * * * /bin/sh /home/axio/workspace/mbmspsync/public/run.sh
#
##########
