<?php
	if (count(get_included_files) == 1) {
		header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden.");
	}

	$server = "xxx";
	$user = "xxx";
	$passw = "xxx";
	$db = "xxx";

	$conn = new mysqli($server, $user, $passw, $db);

	if ($conn->connect_error) {
		die("Nie udało się połączyć z bazą danych.");
	}
?>