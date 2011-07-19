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

<HTML>
	<TITLE>
		Viking Management Platform
	</TITLE>
	<head>

		<link rel="stylesheet" href="menu.css">
		<script language="JavaScript" src="menu.js"></script>
		<script language="JavaScript" src="menu_items.js"></script>
		<script language="JavaScript" src="menu_tpl.js"></script>

		<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE"><META HTTP-EQUIV="Expires" CONTENT="-1">
		<!-- some header data -->
		<link rel="stylesheet" href="menu.css">
		<link rel="stylesheet" href="style.css">

	</head>
	<FRAMESET ROWS=80px,100%>
		<FRAME SRC='top.php' SCROLLING=NO  NAME=Frame1  frameborder='0' framespacing='0' noresize>
		<FRAMESET COLS=190px,100%>
			<FRAME SRC='menu.php' SCROLLING=AUTO  NAME=Frame2  frameborder='0' framespacing='0' noresize>
			<FRAME SRC='blank.php' SCROLLING=AUTO  NAME=Frame3  frameborder='0' framespacing='0' noresize>

		</FRAMESET>
	</FRAMESET>
</HTML>
