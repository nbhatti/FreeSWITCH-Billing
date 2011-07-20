#!/usr/bin/perl -w

use strict;

open(IN,'ps -ef | grep viking_get_calls.pl | grep -v grep |');
while (<IN>){
     if($_ =~ /\/viking_get_calls/){ 
          print "Process is running\n$_\n";
     }else{
          exec('nohup /home/freeswitch/viking_get_calls.pl &');
     }
}
close IN;
exit 0;
