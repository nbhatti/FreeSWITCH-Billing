#!/bin/bash

FSPID=`ps -ef | grep viking_get_calls.pl | grep -v grep | wc -l`

if [ $FSPID -gt 0 ]; then
        echo "Process is running..."
else
        echo "We should start the process..."
        nohup /home/freeswitch/viking_get_calls.pl &
fi

exit

