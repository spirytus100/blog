<!DOCTYPE html>
<html>
<head>
	<title>j24</title>
</head>
<body>
<?php

	if (count(get_included_files) == 1) {
		header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden.");
	}
	
	include $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";

	if (isset($_SERVER["REQUEST_METHOD"])) {
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
			$username = $_POST["username"];
			$password = $_POST["password"];

			$passwd_hash = password_hash($password, PASSWORD_DEFAULT);

			$stmt = $conn->prepare("INSERT INTO users (username, passwd_hash) VALUES (?, ?)");
			$stmt->bind_param("ss", $username, $passwd_hash);
			$stmt->execute();
			if ($result === false) {
                $result->trigger_error($stmt->error, E_USER_ERROR);
            } else {
				echo "Użytkownik ".$username." został zarejestrowany.";

			}
		}
	}

?>
</body>
</html>