#!/bin/bash

FSPID=`ps -ef | grep viking_tracing_check.pl | grep -v grep | wc -l`

if [ $FSPID -gt 0 ]; then
        echo "Process is running..."
else
        echo "We should start the process..."
        nohup /home/freeswitch/viking_tracing_check.pl &
fi

exit

