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

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
	<title>Reporte de ventas y costes</title>
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
<h3>Reporte de ventas y costes</h3>
<form action="financial_report.php" method="post">
<?
     if(!isset($_POST['orderdate'])){
?>
<table width=400px>
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

<?

     }else{
#          echo "I will now execute the report with the following info: " . $_POST['customer'] . ", from " . $_POST['orderdate'] . " to: " . $_POST['orderdate2'] . "<br>";
          $resultado = mysql_query("select gw_symbol, cost_areacode, cost_description, cost_cost, sum(billsec/60) as minutes, sum(call_total_rate) as venta,
           sum(call_total_cost) as coste from cdr where datetime_start between '" . $_POST['orderdate'] . " 00:00:00' and '" . $_POST['orderdate2'] . " 23:59:59' group by gw_symbol, cost_areacode, cost_description, cost_cost order by gw_symbol, cost_areacode, cost_description, cost_cost ;") or die("La consulta ha fallado;: " . mysql_error());
#gw_symbol = '" . $_POST['provider'] . "' and 
          echo "<table cellspacing='0' cellpadding='0'>\n";
          echo "<tr bgcolor='green'>\n"; 
          echo "     <th width='200px' align='left' >Gateway</th>\n";
          echo "     <th width='100px' align='left' >Prefijo</th>\n";
          echo "     <th width='200px' align='left' >Descripción</th>\n";
          echo "     <th width='100px' align='right'>Coste</th>\n";
          echo "     <th width='100px' align='right'>Minutos</th>\n";
          echo "     <th width='200px' align='right'>Total Venta</th>\n";
          echo "     <th width='200px' align='right'>Total Coste</th>\n";
          echo "     <th width='200px' align='right'>Beneficio Bruto</th>\n";
          echo "     <th width='200px' align='right'>Margen</th>\n";
          echo "</tr>\n"; 
          while($linea=mysql_fetch_row($resultado)){
               echo "<tr bgcolor='white' style=\"color:black\">\n"; 
               echo "     <td align='left' > $linea[0] </td>\n";
               echo "     <td align='left' > $linea[1] </td>\n";
               echo "     <td align='left' > $linea[2] </td>\n";
               echo "     <td align='right'> ". number_format($linea[3],4,',','.') ."</td>\n";
               echo "     <td align='right'> ". number_format($linea[4],2,',','.') ."</td>\n";
               echo "     <td align='right'> ". number_format($linea[5],4,',','.') ."</td>\n";
               echo "     <td align='right'> ". number_format($linea[6],4,',','.') ."</td>\n";
               $beneficio = ($linea[5] - $linea[6]);
               echo "     <td align='right'> " . number_format($beneficio,4,',','.') . "</td>\n";
               $margen = $beneficio/$linea[5];
               echo "     <td align='right'> " . number_format($margen*100,2,',','.') . " %</td>\n";
               echo "</tr>\n"; 
          }
          echo "</table>\n";
     }
?>
</form>
</Body>
</html>
