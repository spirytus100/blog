<!DOCTYPE html>
<html>
<head>
	<title>j24 logowanie</title>
</head>
<body>
	<?php
		session_start();

		if (!empty($_SESSION)) {
			header("Location: userpanel.php");
		} else {
			echo '<form action="userpanel.php" method="post">
			<label for="username"><b>Nazwa użytkownika</b></label>
			<input type="text" name="username" required><br>

			<label for="password"><b>Hasło</b></label>
			<input type="password" name="password" required><br>

			<button type="submit">Zaloguj się</button></form>';
		}
	?>
</body>
</html>
