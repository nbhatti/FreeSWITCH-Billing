#!/bin/bash

FSPID=`ps -ef | grep viking_reload.pl | grep -v grep | wc -l`

if [ $FSPID -gt 0 ]; then
	echo "Process is running..."
else
	echo "We should start the process..."
	nohup /home/david/viking_reload.pl &
fi

exit

