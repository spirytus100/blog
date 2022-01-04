<!DOCTYPE html>
<html>
<head>
	<title>j24 logowanie</title>
</head>
<body>
	<?php
		session_start();
		include $_SERVER["DOCUMENT_ROOT"]."/includes/redir_if_not_logged.php";
		require $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";

		function print_menu($username) {
			echo "Witaj, ".$username."!<br>";
			echo "<a href='logout.php'>Wyloguj się</a><br>";
			echo "<a href='/../index.php'>Strona główna</a><br>";
			echo "<a href='/post/posttype.php'>Dodaj post</a><br>";
			echo "<form action='/../post/addimage.php' method='post' enctype='multipart/form-data'>
					<label for='image'>Dodaj obraz (max 5MB)</label>
					<input type='file' name='image'/><input type='submit'/>
				</form>";
		}


		if (isset($_SERVER["REQUEST_METHOD"])) {
			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				$username = $_POST["username"];
				$password = $_POST["password"];

				$sql_select = "SELECT passwd_hash FROM users WHERE username = '$username'";
				$result = $conn->query($sql_select);
				if ($result->num_rows > 0) {
					$row = $result->fetch_row();
					$passwd_hash = $row[0];
				}

				if (password_verify($password, $passwd_hash)) {
					session_start();
					$_SESSION['username'] = $_POST["username"];
					print_menu($_SESSION['username']);	
				} else {
					echo "Błędna nazwa użytkownika lub hasło.";
				}

			} else {

				if (isset($_SESSION["draft_id"])) {
					unset($_SESSION["draft_id"]);
				}

				if (isset($_SESSION['username'])) {
					print_menu($_SESSION['username']);
				}
			}
		}
	?>
</body>
</html>
