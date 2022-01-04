<!DOCTYPE html>
<html>
<head>
	<title>j24 wylogowanie</title>
	<meta charset="utf-8">
</head>
<body>
<?php
	session_start();

	session_unset();
	session_destroy();
	
	echo "Zostałeś wylogowany.<br>";
	echo "<a href='login.php'>Zaloguj się ponownie</a><br>
		<a href='/../index.php";
?>
</body>
</html>