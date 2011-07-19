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
     <script> 

          function disable_table_insert(){ 
               document.getElementById('insert_table').disabled = true;
               document.getElementById('zaprecs').disabled = true;    
               document.getElementById('table').disabled = false;    
          } 

          function disable_table_create(){ 
               document.getElementById('insert_table').disabled = false;
               document.getElementById('zaprecs').disabled = false;    
               document.getElementById('table').disabled = true;    
          } 

     </script>
</head>
<body>
<h3>Crear tabla de Precios/Rutas desde archivo</h3>
     <?php
     
          if( isset($_FILES['uploaded']['name']) ){
          
               #    CREATE AND INSERT INTO A TABLE
               if($_POST['operation']=="create_table"){ 
                    if(!isset($_POST['table'])||$_POST['table']==""){ 
                         echo "<script>alert('No ha especificado una tabla a CREAR!');</script>";
                    }else{
                         $table = "ws_rate_" . $_POST['table'];
                         //print "I will create the table first!<br>\n";
                         //print "The new table name is " . $table . "<br>\n";
                         $resultado = mysql_query("create table " . $table . " like ws_def_rates;") or die("No he podido CREAR la tabla! " . mysql_error());
                         MoveAndInsert($table);
                    }
               }
               
               #    INSERT INTO A TABLE
               if($_POST['operation']=="insert_table"){ 
                    if(!isset($_POST['insert_table'])||$_POST['insert_table']==""){ 
                         echo "<script>alert('No ha especificado una tabla a insertar!');</script>"; 
                    }else{
                         $table = $_POST['insert_table'];
                         //print "INSERTING INTO: $table<br>\n";
                         if( $_POST['zaprecs'] ){ 
                              //print "But first i will remove all existing records<br>\n";
                              $resultado = mysql_query("truncate table " . $table . ";") or die("No he podido vaciar la tabla! " . mysql_error());
                         }
                         MoveAndInsert($table);
                    }
               }

          }
     ?> 
     <form enctype="multipart/form-data" action="costupload.php" method="POST">
          <table>
               <tr>
                    <td>
                         <input type="radio" id="operation" name="operation" value="create_table" onclick="disable_table_insert()" checked>Crear una tabla nueva
                    </td>
                    <td>
                         <input type="text" name="table" id="table">(El sistema pondrá el prefijo "ws_rate_" automáticamente)
                    </td>
               </tr>
               <tr>
                    <td>
                         <input type="radio" id="operation" name="operation" value="insert_table" onclick="disable_table_create()">Insertar en la tabla
                    </td>
                    <td>
                         <select name="insert_table" id="insert_table">
                              <option value="" SELECTED></option>
                         <?
                              $sqldatetime = "show tables like 'ws_rate_%';";
                              $resultado = mysql_query($sqldatetime) or die("La consulta ha fallado;: " . mysql_error());
                              while($linea=mysql_fetch_row($resultado)){                    	
                              	echo "<option value=\"" . $linea[0] . "\">" . $linea[0] . "</option>\n";
                              }
                         ?>
                         </select>
                         <input type="checkbox" name="zaprecs" id="zaprecs">delete all records before inserting
                         
                    </td>
               </tr>
               <tr>
                    <td>
                         Separador de campos
                    </td>
                    <td>
                         <select name="separator" id="separator">
                              <option>;</option>
                         </select>
                    </td>
               </tr>
               <tr>
                    <td>
                         Seleccione el archivo
                    </td>
                    <td>
                          <input name="uploaded" type="file" />
                    </td>
               </tr>
                    <td colspan="2">
                         <input type="submit" value="Upload" />
                    </td>
               </tr>
          </table>
     </form> 
</body>
</html>

<?

function MoveAndInsert($table){
     $target = "/tmp/"; 
     $target = $target . basename( $_FILES['uploaded']['name']) ; 
     $ok=1; 
     if(move_uploaded_file($_FILES['uploaded']['tmp_name'], $target)) 
     {

          $separator = $_POST['separator'];
          $sqldatetime = "LOAD DATA LOCAL INFILE '" . $target . "' INTO TABLE $table FIELDS TERMINATED BY '$separator' (areacode, description, @var1, route) SET rate = replace(@var1, ',', '.');";
          //print "<br>$sqldatetime<br>\n";
          system("chown www-data $target");
          $resultado = mysql_query($sqldatetime) or die("No he podido insertar los datos, por favor verifique el archivo! " . mysql_error());
          
     } else {
          echo "Sorry, there was a problem uploading your file.";
     }

}
?>