#!/usr/bin/perl
use DBI;
use strict;

sub Reload();
sub CheckSum($);

my $table_checksum = 0;
my $old_table_checksum = 0;

while(1){
     $table_checksum = CheckSum("ws_customers");
     $table_checksum += CheckSum("ws_providers");
     $table_checksum += CheckSum("ws_routes");

     print "Tables Checksum: $table_checksum \n";

     if($old_table_checksum != $table_checksum){
          Reload();
     }

     $old_table_checksum = $table_checksum;
     $table_checksum=0;

     sleep 10;
}

exit 0;




sub Reload(){
        system("/home/freeswitch/freeswitch_reload_config.pl");
}


sub CheckSum($){

     my $table_in = shift;

     my $dbh = DBI->connect('DBI:mysql:viking;host=viking_db', 'viking', 'V1k1ng') || die "Could not connect to database: $DBI::errstr";
     my $sth = $dbh->prepare('checksum table ' . $table_in . ';') or die "Couldn't prepare statement: " . $dbh->errstr;
     $sth->execute();

     while (my @data = $sth->fetchrow_array()) {
          my $table = $data[0];
          my $checksum = $data[1];
          return $checksum;
     }
}

