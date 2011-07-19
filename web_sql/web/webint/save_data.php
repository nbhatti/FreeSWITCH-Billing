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

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15">
	<title>Saving data...</title>
	<link rel="stylesheet" href="pages_style.css">
</head>
<body>

<?php


if($_POST['action']=="newrate"){
     $resultado = mysql_query("show tables like ws_rate_'" . $_POST['newtable'] . "';");
     if(mysql_num_rows($resultado)>0){
          echo "<font size=4>Ya existe una tabla con ese nombre (" . $_POST['newtable'] . "), por favor retroceda y cambie el nombre.";
          exit; 
     }

     $resultado = mysql_query("create table ws_rate_" . $_POST['newtable'] . " like " . $_POST['source'] . ";");

     if(!$resultado){ echo "<font size=6 color='red'>Ha habido un error creando la nueva tabla, por favor dirígase al administrador del sistema"; exit; }
     
     if( $_POST['includedata'] == "include" ){
          executeSQL("insert ws_rate_" . $_POST['newtable'] . " select * from " . $_POST['source'] . " order by areacode;");
     }
}


if($_POST['action']=="newcost"){
     $resultado = mysql_query("show tables like ws_cost_'" . $_POST['newtable'] . "';");
     if(mysql_num_rows($resultado)>0){
          echo "<font size=4>Ya existe una tabla con ese nombre (" . $_POST['newtable'] . "), por favor retroceda y cambie el nombre.";
          exit; 
     }

     $resultado = mysql_query("create table ws_cost_" . $_POST['newtable'] . " like " . $_POST['source'] . ";");

     if(!$resultado){ echo "<font size=6 color='red'>Ha habido un error creando la nueva tabla, por favor dirígase al administrador del sistema"; exit; }
     
     if( $_POST['includedata'] == "include" ){
          executeSQL("insert ws_cost_" . $_POST['newtable'] . " select * from " . $_POST['source'] . " order by areacode;");
     }
}




function executeSQL($sql){

     $resultado = mysql_query($sql) or die("
     <html>
     <head>
          <meta http-equiv=\"refresh\" content=\"5; url = create_ratetable.php\" />
     </head>
     <body  bgcolor=\"#1C2D67\">
          <br>
               <font face=\"Verdana\" color=\"#FF0000\" size=\"12\">
                    <center>
                         There was an error executing your request. <br>
                         Please contact the system administrator
                    </center>
               </font>
          <br>
     " . mysql_error() . "</body>");
     
     
     if($resultado){
          print "
               <html>
               <head>
                    <meta http-equiv=\"refresh\" content=\"5; url = create_ratetable.php\" />
               </head>
               <body  bgcolor=\"#1C2D67\">
                    <br>
                         <font face=\"Verdana\" color=\"White\" size=\"4\">
                              <center>
                                   Your request has been executed successfully. <br>
                                   You are being redirected to the main page.
                              </center>
                         </font>
                    <br>
               </body>";
     }
}






?>
</body>
</html>