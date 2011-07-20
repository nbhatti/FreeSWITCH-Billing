<?php
  include_once('Parse_XML_CDR.php');

  $handler = fopen("cdr.txt","r");
  $contents = fread($handler, filesize("cdr.txt"));  

  $xml = new Parse_CDR_XML($contents);
  $cdr=$xml->ReturnArray();

  // log the entries
  // it would probably be better to log to a database but simple examples dont do that
#  echo $contents . "\n\nARRAYS\n\n";
  print_r($cdr);

?>