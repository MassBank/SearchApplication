#!/bin/sh
# filename: manager.sh

#ps aux | grep '[r]un_mq_worker.php.php'
#if [ $? -ne 0 ]; then
#	DIR="$( cd "$( dirname "$0" )" && pwd )"
#	/usr/bin/php $DIR/run_mq_worker.php > /dev/null &
#fi
 
#####################
PROCESSORS=1;
x=0
 
while [ "$x" -lt "$PROCESSORS" ];
do
        PROCESS_COUNT=`pgrep -f mq_worker.php | wc -l`
        if [ $PROCESS_COUNT -ge $PROCESSORS ]; then
                exit 0
        fi
        x=`expr $x + 1`
        
        DIR="$( cd "$( dirname "$0" )" && pwd )"
        php -f $DIR/run_mq_worker.php > /dev/null &
done
exit 0
#####################