<?PHP

function displayLogin() {
header("WWW-Authenticate: Basic realm=\"Viking Management Platform\"");
header("HTTP/1.0 401 Unauthorized");
echo "<h2>Authentication Failure</h2>";
echo "La contraseña que ha introducido no es válida. Refresque la página e inténtelo de nuevo.";
exit;
}

require "conexion.inc";
require "checklogin.inc";

?>

<?                                   
     if(!isset($_POST['provider'])){ 
?>                                   
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
	<title>Reporte por proveedor</title>
	<link rel="stylesheet" href="pages_style.css">
</head>
<script language="javascript">
     function setdata(){
          tbl = document.getElementById('includedata').value;
          if(document.getElementById('includedata').value=="include"){
               document.getElementById('includedata').value="";
          }else{
               document.getElementById('includedata').value="include";
          }
     }


</script>

<script type="text/javascript" src="calendarDateInput.js">

/***********************************************
* Jason's Date Input Calendar- By Jason Moon http://calendar.moonscript.com/dateinput.cfm
* Script featured on and available at http://www.dynamicdrive.com
* Keep this notice intact for use.
***********************************************/
</script>
<body>
<h3>Reporte por proveedor</h3>
<form action="cdrexport.php" method="post">

<table width=400px>
     <tr bgcolor="#FFFF66">
          <td colspan="2">
               <font color="black">Esta página sólo exporta CDRs de llamadas conectadas.</font>
          </td>
     </tr>
     <tr>
          <td>
               Proveedor:
          </td>
          <td>
               <select id=provider name=provider>
               <?php
                    // Check username and password agains the database.

                    $sqldatetime = "select symbol, name from ws_providers union select ws_customer_symbol, ws_customer_company from ws_customers order by name;";
                    $resultado = mysql_query($sqldatetime) or die("La consulta ha fallado;: " . mysql_error());
                    
                    #	GET DATA SO THAT I CAN SHOW %/TOTAL FOR EACH CUSTOMER
                    while($linea=mysql_fetch_row($resultado)){                    	
                    	echo "<option value='" . $linea[0] . "'>" . $linea[1] . "</option>\n";
                    }
               ?>
               </select>
          </td>
     </tr>
     <tr>
          <td>
               Fechas Desde:
          </td>
          <td>
               <script>
                    DateInput('orderdate', true, 'YYYY-MM-DD')
               </script>
          </td>
     </tr>
     <tr>
          <td>
               Fechas Hasta:
          </td>
          <td>
               <script>
                    DateInput('orderdate2', true, 'YYYY-MM-DD')
               </script>
          </td>
     </tr>
     <tr>
          <td>
               <input type="submit" value="Ejecutar">
          </td>
          <td>
          </td>
     </tr>
</table>

</form>
</Body>
</html>

<?

     }else{
#          echo "I will now execute the report with the following info: " . $_POST['customer'] . ", from " . $_POST['orderdate'] . " to: " . $_POST['orderdate2'] . "<br>";
          $sqlquery = "
                                        select 
                                             datetime_start,
                                             customer_company,
                                             customer_symbol,
                                             received_ip, 
                                             clgnum,
                                             cldnum,
                                             billsec,
                                             customer_ratetable,
                                             if(customer_prepaid=1,'PREPAID','POSTPAID') as customer_type,
                                             customer_balance,
                                             rate_areacode,
                                             rate_description,
                                             rate_rate,
                                             call_total_rate,
                                             gw_symbol,
                                             gw_sip_ip,
                                             gw_strip_digits,
                                             gw_out_prefix,
                                             gw_cost_table,
                                             gw_sip_username,
                                             gw_sip_pwd,
                                             cost_areacode,
                                             cost_description,
                                             cost_cost,
                                             call_total_cost,
                                             datetime_answer,
                                             read_codec,
                                             write_codec,
                                             datetime_end,
                                             hangup_cause,
                                             hangup_cause_q850, 
                                             call_result 
                                        from viking.cdr 
                                        where billsec > 0 and
                                        (
                                             customer_symbol = '" . $_POST['provider'] . "' or 
                                             gw_symbol = '" . $_POST['provider'] . "'
                                        ) 
                                        and 
                                        datetime_start between '" . $_POST['orderdate'] . " 00:00:00' and '" . $_POST['orderdate2'] . " 23:59:59' 
                                        order by gw_symbol, cost_areacode, cost_description, cost_cost;
          ";
          $resultado = mysql_query($sqlquery) or die("La consulta ha fallado;: " . mysql_error());

          $filename = "cdr_" . $_POST['provider'] . "_" . $_POST['orderdate'] . "_" . $_POST['orderdate2'] . ".csv";
          $contents = "";
          header('Content-type: application/ms-excel');
          header('Content-Disposition: attachment; filename='.$filename);
                    while($linea=mysql_fetch_row($resultado)){
                         $contents .= "" . $linea[0];
                         $contents .= ";" . $linea[1];
                         $contents .= ";" . $linea[2];
                         $contents .= ";" . $linea[3];
                         $contents .= ";" . $linea[4];
                         $contents .= ";" . $linea[5];
                         $contents .= ";" . $linea[6];
                         $contents .= ";" . $linea[7];
                         $contents .= ";" . $linea[8];
                         $contents .= ";" . $linea[9];
                         $contents .= ";" . $linea[10];
                         $contents .= ";" . $linea[11];
                         $contents .= ";" . $linea[12];
                         $contents .= ";" . $linea[13];
                         $contents .= ";" . $linea[14];
                         $contents .= ";" . $linea[15];
                         $contents .= ";" . $linea[16];
                         $contents .= ";" . $linea[17];
                         $contents .= ";" . $linea[18];
                         $contents .= ";" . $linea[19];
                         $contents .= ";" . $linea[20];
                         $contents .= ";" . $linea[21];
                         $contents .= ";" . $linea[22];
                         $contents .= ";" . $linea[23];
                         $contents .= ";" . $linea[24];
                         $contents .= ";" . $linea[25];
                         $contents .= ";" . $linea[26];
                         $contents .= ";" . $linea[27];
                         $contents .= ";" . $linea[28] . "\n";
                    }
                    echo $contents;
     }
?>
