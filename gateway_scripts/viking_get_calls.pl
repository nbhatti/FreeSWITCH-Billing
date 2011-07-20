#!/usr/bin/perl

use strict;
use warnings;
use DBI;
$|=1;

my $dbh = DBI->connect('DBI:mysql:viking;host=viking_db', 'viking', 'V1k1ng') || die "Could not connect to database: $DBI::errstr";
my $result;
my $command="";
my @data;

my $uuid;
my $direction;
my $created;
my $created_epoch;
my $name;
my $state;
my $cid_name;
my $cid_num;
my $ip_addr;
my $dest;
my $application;
my $application_data;
my $dialplan;
my $context;
my $read_codec;
my $read_rate;
my $read_bit_rate;
my $write_codec;
my $write_rate;
my $write_bit_rate;
my $secure;
my $hostname;
my $presence_id;
my $presence_data;
my $callstate;
my $callee_name;
my $callee_num;
my $callee_direction;
my $call_uuid;


while(1){
     open IN,"/usr/local/freeswitch/bin/fs_cli --host=192.168.168.3 --port=8021 --password=YOURPASSWORD -x 'show channels' |";
     $dbh->do("truncate table channels"); 
     while(<IN>){
          if($_!~/^$/ && $_!~/total.$/ && $_!~/uuid,direction,created,created/){
               if($direction =~ /inbound/){
                    print "DATA INBOUND: $_\n";
                    ($uuid,$direction,$created,$created_epoch,$name,$state,$cid_name,$cid_num,$ip_addr,$dest,$application,$application_data,$dialplan,$context,$read_codec,$read_rate,$read_bit_rate,$write_codec,$write_rate,$write_bit_rate,$secure,$hostname,$presence_id,$presence_data,$callstate,$callee_name,$callee_num,$callee_direction,$call_uuid) = split(/,/,$_);
               
                    print "-------------------------------------------------------------------------------------\n";
                    print "UUID.........: $uuid\n";     
                    print "DIR..........: $direction\n";     
                    print "CID_NUM......: $cid_num\n";
                    print "DEST.........: $dest\n";
                    print "CONTEXT......: $context\n";     
                    print "READ CODEC...: $read_codec\n";     
                    print "WRITE CODEC..: $write_codec\n";     
                    print "CALL STATE...: $callstate\n";
                    print "PEER.........: $callee_num\n";
                    
                    my $sql = "insert channels values (null,'$uuid','$direction','$created','$created_epoch','$name','$state','$cid_name','$cid_num','$ip_addr','$dest','$application','$application_data','$dialplan','$context','$read_codec','$read_rate','$read_bit_rate','$write_codec','$write_rate','$write_bit_rate','$secure','$hostname','$presence_id','$presence_data','$callstate','$callee_name','$callee_num','$callee_direction','$call_uuid');";
                    print "SQL: $sql\n";
                    $dbh->do($sql);
               }else{
                    print "DATA OUTBOUND: $_\n";
                    ($uuid,$direction,$created,$created_epoch,$name,$state,$cid_name,$cid_num,$ip_addr,$dest,$application,$application_data,$dialplan,$context,$read_codec,$read_rate,$read_bit_rate,$write_codec,$write_rate,$write_bit_rate,$secure,$hostname,$presence_id,$presence_data,$callstate,$callee_name,$callee_num,$callee_direction,$call_uuid) = split(/,/,$_);
               
                    print "-------------------------------------------------------------------------------------\n";
                    print "UUID.........: $uuid\n";     
                    print "DIR..........: $direction\n";     
                    print "CID_NUM......: $cid_num\n";
                    print "DEST.........: $dest\n";
                    print "CONTEXT......: $context\n";     
                    print "READ CODEC...: $read_codec\n";     
                    print "WRITE CODEC..: $write_codec\n";     
                    print "CALL STATE...: $callstate\n";
                    print "PEER.........: $callee_num\n";
                    
                    my $sql = "update channels set callee_num = '$callee_num', callee_name = '$callee_name' where uuid = '$call_uuid';";
                    print "SQL: $sql\n";
                    $dbh->do($sql);
               
               }
          }else{ print "Empty line\n";}
     }
     sleep 10;
}


exit 0;

