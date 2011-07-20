#!/usr/bin/perl

$result = system("kill -KILL `ps -eo pid -o cmd | grep '/home/freeswitch/viking_sip_trace.pl\\|contains\\|dumpcap' | grep -v grep | awk '{print \$1}'`");

while ( <IN>  ) {
     print "KILL $_\n";
}
