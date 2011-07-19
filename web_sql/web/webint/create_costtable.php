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
	<title>ws_providers</title>
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
<body>
<h3>Create new cost table</h3>
<form action="save_data.php" method="post">
<table width=400px>
     <tr>
          <td>
               Source table name:
          </td>
          <td>
               <select id=source name=source>
               <?php
                    // Check username and password agains the database.

                    $sqldatetime = "show tables like 'ws_cost_%';";
                    $resultado = mysql_query($sqldatetime) or die("La consulta ha fallado;: " . mysql_error());
                    
                    #	GET DATA SO THAT I CAN SHOW %/TOTAL FOR EACH CUSTOMER
                    while($linea=mysql_fetch_row($resultado)){                    	
                    	echo "<option>" . $linea[0] . "</option>\n";
                    }
               ?>
               </select>
          </td>
     </tr>
     <tr>
          <td>
               Destination (new) table name:
          </td>
          <td>
               <input type="text" id=newtable name=newtable>
          </td>
     </tr>
     <tr>
          <td>
               <input type="checkbox" onClick="setdata()"> Include data
          </td>
     </tr>
</table>
<input id=action name=action type="text" style="display: none" value="newcost"/>
<input id=includedata name=includedata style="display: none" type="text" value=""/>
<input type="submit"/>
</form>
</Body>
</html>