#!/usr/bin/perl

use strict;
use warnings;
use DBI;
$|=1;

my $dbh = DBI->connect('DBI:mysql:viking;host=viking_db', 'viking', 'V1k1ng') || die "Could not connect to database: $DBI::errstr";
# ip.src 
# ip.dst 
# udp.srcport 
# udp.dstport 
# sip.Method 
# sip.Status-Line 
# sip.from.host 
# sip.r-uri 
# sip.Call-ID 
# sdp.media.port 

sub StartTrace($$$$);

my $rtp_a_port;
my $rtp_b_port;

my $if="eth0";
my $ip_1="";
my $ip_2="";
my $port="";
my $dialed="";
my $running_trace = 0;
my $sth_trace = $dbh->prepare("Select running_trace, remote_host, service_ip, dialed_number from ws_settings;");
my $child=0;

$sth_trace->execute();
my $result = $sth_trace->fetchrow_hashref();

system('echo "truncate table sip_trace;"|mysql -u viking -pV1k1ng -h viking_db viking');

StartTrace($result->{remote_host},$result->{service_ip},"5060",$result->{dialed_number});

exit 0;


#----------------------------------------------------------------------------------------------------------------
     
sub CheckTrace(){
     $sth_trace->execute();
     my $result = $sth_trace->fetchrow_hashref();

     if($result->{running_trace} eq "NO" && $running_trace==1 ){
          print "I've been asked to stop\n";
          close(TRACE);
          $running_trace=0;
     }
}
sub StartTrace($$$$){

     $ip_1=shift;
     $ip_2=shift;
     $port=shift;
     $dialed=shift;
     
     if($ip_2 ne "0.0.0.0"){
          open(TRACE, "tshark -f \"port $port and host $ip_1 and host $ip_2\" -R \"sip.to.user contains $dialed\" -i $if  -E separator=\\; -T fields -e ip.src -e ip.dst -e udp.srcport -e udp.dstport -e sip.Method -e sip.Status-Line -e sip.from.host -e sip.ruri -e sip.Call-ID -e sdp.media.port -e sdp.media.format -l|");
     }else{
          open(TRACE, "tshark -f \"port $port and host $ip_1\" -R \"sip.to.user contains $dialed\" -i $if  -E separator=\\; -T fields -e ip.src -e ip.dst -e udp.srcport -e udp.dstport -e sip.Method -e sip.Status-Line -e sip.from.host -e sip.ruri -e sip.r-uri.host -e sip.r-uri.user -e sip.Call-ID -e sdp.media.port -e sdp.media.format -l|");
     }
     
     while(my $in=<TRACE>){
          CheckTrace();

          print $in;
          my($ipsrc, $ipdst, $srcport, $dstport, $method, $status, $from_host, $ruri,  $ruri_host, $ruri_user, $callid, $media_port) = split(/;/,$in);
     
          print "IPSRC.......: $ipsrc\n";
          print "IPDEST......: $ipdst\n";
          print "SRC PRT.....: $srcport\t";
          print "DST PRT.....: $dstport\t";
          print "METHOD......: $method\t";
          print "STATUS......: $status\t";
          print "HOST FRM....: $from_host\t";
          print "R-URI.......: $ruri\t";
          print "R-URI-HOST..: $ruri_host\t";
          print "R-URI-USER..: $ruri_user\t";
          print "CALL-ID.....: $callid\t";
          #print "MEDIA PRT...: $media_port\n";
     
     
     
#             if($method eq "INVITE"){ 
#                     $rtp_a_port = $media_port;
#                     print "\nINITIAL ($media_port)\n";
#             }
             
     $dbh->do("insert sip_trace values (null,now(),'$ipsrc',$srcport,'$ipdst',$dstport,'$method','$status','$from_host','$ruri_host','$ruri_user','$callid');");
     
          $status = uc($status); 
     #        if($status eq "SIP/2.0 200 OK" && $continue == 1){ 
     #            print "\nCONNECT ($media_port)\n";
     #                $rtp_b_port = $media_port;
     #                print "CALL CONNECTED, STARTING RTP TRACE WITH COMMAND: tshark -f \"udp and (port $rtp_a_port or $rtp_b_port)\"\n";
     #                open(RTP,"tshark -f \"udp and (port $rtp_a_port or $rtp_b_port)\" -n -i eth1  -E header=y -E separator=\\; -T fields -e ip.src -e udp.srcport -e ip.dst -e udp.dstport -l |");
     #                while(my $rtp=<RTP>){
     #                        chomp($rtp);
     #                        my ($ip_a,$port_a,$ip_b,$port_b) = split(/;/,$rtp);
     #
     ##                       print "\e[A";
     ##                       print "                                                \n";
     ##                       print "\e[A";
     #                        print "RTP: $ip_a:$port_a -> $ip_b:$port_b\n";
     #                }
     #        }
     }
}
