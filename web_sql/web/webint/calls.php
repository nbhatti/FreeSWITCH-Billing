<html>
<body>

<?
require "../webint/conexion.inc";

     echo "Loading configuration...<br>\n";
     $fp = fsockopen("192.168.168.3", 8021, $errno, $errstr, 30);
     
     if (!$fp) {
          echo "$errstr ($errno)<br />\n";
     } else {
          $out = "auth M3ll4m0d4v1d\n\n";
          fwrite($fp, $out);


#          $resultado_context = mysql_query("select service_type from ws_settings") or die("La consulta ha fallado;: " . mysql_error());
#          while($linea_context=mysql_fetch_row($resultado_context)){
#               $resultado = mysql_query("select symbol, sip_ip, out_prefix, sip_username, sip_pwd from ws_providers order by symbol") or die("La consulta ha fallado;: " . mysql_error());
#               while($linea=mysql_fetch_row($resultado)){
#                    $out = "api sofia profile " . $linea_context[0] . " killgw $linea[0]\n\n";
#                    echo $out . "<br>";
#                    fwrite($fp, $out);
#               }
#          }

#          $out = "api reloadxml\n\n";
#          echo $out . "<BR>";
#          fwrite($fp, $out);
          
#          $out = "api distributor_ctl reload\n\n";
#          echo $out . "<BR>";
#          fwrite($fp, $out);
          
#          $out = "api sofia profile restart all\n\n";
#          echo $out . "<BR>";
#          fwrite($fp, $out);
          
          $out = "bgapi show channels\n\n";
          echo $out . "<BR>";
          fwrite($fp, $out);
          
          while (!feof($fp)) {
               $in = fgets($fp, 128);
               echo "$in<br>\n";
          }
          fclose($fp);
     }
     echo "Configuration loaded.<br>\n";

?>
</body>
</html>
