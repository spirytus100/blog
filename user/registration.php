<!DOCTYPE html>
<html>
<head>
	<title>j24 rejestracja</title>
	<meta charset="utf-8">
</head>
<body>
	<?php
		session_start();

		if (!empty($_SESSION)) {
			echo "Witaj, ".$_SESSION['username']."!<br>";
			echo "<a href='userpanel.php'>Panel użytkownika</a><br>";
			echo "<a href='logout.php'>Wyloguj się</a>";
		} else {
			echo '<form action="register.php" method="post">
			Nazwa użytkownika: <input type="text" name="username" required><br>
			Hasło: <input type="password" name="password" required><br>
			<button type="submit">Zarejestruj się</button>
			</form>';
		}
	?>
</body>
</html>