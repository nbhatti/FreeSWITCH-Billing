<?php
  include_once('Parse_XML_CDR.php');

  require "../webint/conexion.inc";

  Header("Content-type: text/plain");

  $xml = new Parse_CDR_XML($_POST['cdr']);
  $cdr=$xml->ReturnArray();

  // log the entries
  // it would probably be better to log to a database but simple examples dont do that
  
  
  
$fh = fopen("/tmp/cdr.xml.".uniqid(), 'wb');
fwrite($fh, $_POST['cdr'] . "\n\nprint_r\n\n");
fwrite($fh,print_r($cdr,true)."\n\n");

/*  
fwrite($fh,"start_stamp.............: " . urldecode($cdr["variables"]["start_stamp"] . "\n"));
fwrite($fh,"sip_received_ip.........: " . urldecode( $cdr["variables"]["sip_received_ip"]) . "\n");
fwrite($fh,"caller_id...............: " . urldecode( $cdr["variables"]["caller_id"]) . "\n");
fwrite($fh,"called_number...........: " . urldecode( $cdr["variables"]["called_number"]) . "\n");
fwrite($fh,"sip_local_network_addr..: " . urldecode( $cdr["variables"]["sip_local_network_addr"]) . "\n");
fwrite($fh,"ws_customer_company.....: " . urldecode( $cdr["variables"]["ws_customer_company"]) . "\n");
fwrite($fh,"ws_customer_symbol......: " . urldecode( $cdr["variables"]["ws_customer_symbol"]) . "\n");
fwrite($fh,"ws_customer_sig_ip......: " . urldecode( $cdr["variables"]["ws_customer_sig_ip"]) . "\n");
fwrite($fh,"ws_customer_ratetable...: " . urldecode( $cdr["variables"]["ws_customer_ratetable"]) . "\n");
fwrite($fh,"ws_customer_prepaid.....: " . urldecode( $cdr["variables"]["ws_customer_prepaid"]) . "\n");
fwrite($fh,"ws_customer_balance.....: " . urldecode( $cdr["variables"]["ws_customer_balance"]) . "\n");
fwrite($fh,"ws_customer_enabled.....: " . urldecode( $cdr["variables"]["ws_customer_enabled"]) . "\n");
fwrite($fh,"rate_areacode...........: " . urldecode( $cdr["variables"]["rate_areacode"]) . "\n");
fwrite($fh,"rate_description........: " . urldecode( $cdr["variables"]["rate_description"]) . "\n");
fwrite($fh,"rate_rate...............: " . urldecode( $cdr["variables"]["rate_rate"]) . "\n");
fwrite($fh,"rate_gateway............: " . urldecode( $cdr["variables"]["rate_gateway"]) . "\n");
fwrite($fh,"gw_symbol...............: " . urldecode( $cdr["variables"]["gw_symbol"]) . "\n");
fwrite($fh,"gw_sip_ip...............: " . urldecode( $cdr["variables"]["gw_sip_ip"]) . "\n");
fwrite($fh,"gw_strip_digits.........: " . urldecode( $cdr["variables"]["gw_strip_digits"]) . "\n");
fwrite($fh,"gw_out_prefix...........: " . urldecode( $cdr["variables"]["gw_out_prefix"]) . "\n");
fwrite($fh,"gw_cost_table...........: " . urldecode( $cdr["variables"]["gw_cost_table"]) . "\n");
fwrite($fh,"gw_sip_username.........: " . urldecode( $cdr["variables"]["gw_sip_username"]) . "\n");
fwrite($fh,"gw_sip_pwd..............: " . urldecode( $cdr["variables"]["gw_sip_pwd"]) . "\n");
fwrite($fh,"cost_areacode...........: " . urldecode( $cdr["variables"]["cost_areacode"]) . "\n");
fwrite($fh,"cost_description........: " . urldecode( $cdr["variables"]["cost_description"]) . "\n");
fwrite($fh,"cost_rate...............: " . urldecode( $cdr["variables"]["cost_rate"]) . "\n");
fwrite($fh,"max_call_dura...........: " . urldecode( $cdr["variables"]["max_call_dura"]) . "\n");
fwrite($fh,"answer_stamp............: " . urldecode( $cdr["variables"]["answer_stamp"]) . "\n");
fwrite($fh,"endpoint_disposition....: " . urldecode( $cdr["variables"]["endpoint_disposition"]) . "\n");
fwrite($fh,"syssec..................: " . urldecode( $cdr["variables"]["duration"]) . "\n");
fwrite($fh,"billsec.................: " . urldecode( $cdr["variables"]["billsec"]) . "\n");
fwrite($fh,"read_codec..............: " . urldecode( $cdr["variables"]["read_codec"]) . "\n");
fwrite($fh,"write_codec.............: " . urldecode( $cdr["variables"]["write_codec"]) . "\n");
fwrite($fh,"sip_term_cause..........: " . urldecode( $cdr["variables"]["sip_term_cause"]) . "\n");
fwrite($fh,"bridge_hangup_cause.....: " . urldecode( $cdr["variables"]["bridge_hangup_cause"]) . "\n");
fwrite($fh,"hangup_cause............: " . urldecode( $cdr["variables"]["hangup_cause"]) . "\n");
fwrite($fh,"hangup_cause_q850.......: " . urldecode( $cdr["variables"]["hangup_cause_q850"]) . "\n");
fwrite($fh,"end_stamp...............: " . urldecode( $cdr["variables"]["end_stamp"]) . "\n");

*/


###
#
#   Get INFO for gateway
#
###

if ( $cdr["callflow"][0]["caller_profile"]["network_addr"] != $cdr["variables"]["sip_received_ip"]){
     $costs_info = "select symbol, sip_ip, strip_digits, out_prefix, cost_table from ws_providers where sip_ip like '%" . $cdr["callflow"][0]["caller_profile"]["network_addr"] . "%';\n";
     fwrite($fh,$costs_info);
     $resultado = mysql_query($costs_info) or die(fwrite($fh,"La consulta ha fallado;: " . mysql_error()));
                             
     while($linea=mysql_fetch_row($resultado)){
          $symbol             = $linea[0];
          $sip_ip             = $linea[1];
          $strip_digits       = $linea[2];
          $out_prefix         = $linea[3];
          $cost_table         = $linea[4];
          $gw_sip_username    = "";
          $gw_sip_pwd         = "";
          $cost_areacode      = "";
          $cost_description   = "";
          $cost_rate          = "";
     }
     
     
     ###
     #
     #   Get Cost for gateway
     #
     ###
     
     $costs = "select areacode, description, cost from " . $cost_table . " where '" . $cdr["variables"]["called_number"] . "' like concat(areacode,'%');\n";
     fwrite($fh,$costs);
     $resultado = mysql_query($costs) or die(fwrite($fh,"La consulta ha fallado;: " . mysql_error()));
     
     while($linea=mysql_fetch_row($resultado)){
          $cost_areacode      = $linea[0];
          $cost_description   = $linea[1];
          $cost_rate          = $linea[2];
     }
}


$sql="insert cdr values (null,'" .
     urldecode( $cdr["variables"]["start_stamp"]) . "','" . 
     urldecode( $cdr["variables"]["sip_received_ip"]) . "','" . 
     urldecode( $cdr["variables"]["caller_id"]) . "','" . 
     urldecode( $cdr["variables"]["called_number"]) . "','" . 
     urldecode( $cdr["variables"]["sip_local_network_addr"]) . "','" . 
     urldecode( $cdr["variables"]["ws_customer_company"]) . "','" . 
     urldecode( $cdr["variables"]["ws_customer_symbol"]) . "','" . 
     urldecode( $cdr["variables"]["ws_customer_sig_ip"]) . "','" . 
     urldecode( $cdr["variables"]["ws_customer_ratetable"]) . "','" . 
     urldecode( $cdr["variables"]["ws_customer_prepaid"]) . "','" . 
     urldecode( $cdr["variables"]["ws_customer_balance"]) . "','" . 
     urldecode( $cdr["variables"]["ws_customer_enabled"]) . "','" . 
     urldecode( $cdr["variables"]["rate_areacode"]) . "','" . 
     urldecode( $cdr["variables"]["rate_description"]) . "','" . 
     urldecode( $cdr["variables"]["rate_rate"]) . "','" . 
     urldecode( $cdr["variables"]["rate_gateway"]) . "','" . 
     urldecode( $symbol            ) . "','" . 
     urldecode( $cdr["callflow"][0]["caller_profile"]["network_addr"]) . "','" . 
     urldecode( $strip_digits      ) . "','" . 
     urldecode( $out_prefix        ) . "','" . 
     urldecode( $cost_table        ) . "','" . 
     urldecode( $gw_sip_username   ) . "','" . 
     urldecode( $gw_sip_pwd        ) . "','" . 
     urldecode( $cost_areacode     ) . "','" . 
     urldecode( $cost_description  ) . "','" . 
     urldecode( $cost_rate         ) . "','" . 
     urldecode( $cdr["variables"]["max_call_dura"]) . "','" . 
     urldecode( $answer_stamp      ) . "','" . 
     urldecode( $cdr["variables"]["endpoint_disposition"]) . "'," . 
     urldecode( $cdr["variables"]["duration"]) . "," . 
     urldecode( $cdr["variables"]["billsec"]) . "," .
     ($cdr["variables"]["billsec"] * ($cdr["variables"]["rate_rate"]/60)) . "," . 
     ($cdr["variables"]["billsec"] * ($cost_rate/60)) . "," . 
     ($cdr["variables"]["ws_customer_balance"] - ($cdr["variables"]["billsec"] * ($cdr["variables"]["rate_rate"]/60))) . ",'" .   
     urldecode( $cdr["variables"]["read_codec"]) . "','" . 
     urldecode( $cdr["variables"]["write_codec"]) . "','" . 
     urldecode( $cdr["variables"]["sip_term_cause"]) . "','" . 
     urldecode( $cdr["variables"]["bridge_hangup_cause"]) . "','" . 
     urldecode( $cdr["variables"]["hangup_cause"]) . "','" . 
     urldecode( $cdr["variables"]["hangup_cause_q850"]) . "','" . 
     urldecode( $cdr["variables"]["end_stamp"]) . "');";

fwrite($fh,$sql."\n");
  
$resultado = mysql_query($sql) or die(fwrite($fh,"La consulta ha fallado;: " . mysql_error()));
$sql_customer_deduct_call = "update ws_customers set ws_customer_balance=ws_customer_balance-" . ($cdr["variables"]["billsec"] * ($cdr["variables"]["rate_rate"]/60)) . " where ws_customer_symbol = '" . urldecode( $cdr["variables"]["ws_customer_symbol"]) . "' and ws_customer_context = '" . $cdr["callflow"][0]["caller_profile"]["context"] . "';"; 
$resultado = mysql_query($sql_customer_deduct_call) or die(fwrite($fh,"La consulta ha fallado;: " . mysql_error()));

fwrite($fh,$sql_customer_deduct_call."\n");
fclose($fh);

/*



call price: 



*/
?>