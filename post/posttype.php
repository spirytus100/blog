<!DOCTYPE html>
<html>
<head>
	<title>j24 rodzaj wpisu</title>
	<meta charset="utf-8">
</head>
<body>
	<?php
		session_start();
		include $_SERVER["DOCUMENT_ROOT"]."/includes/redir_if_not_logged.php";

        echo "<h3>Wybierz rodzaj wpisu</h3>";
		echo "<div id='posttype-container'>";
		echo "<form action='selectpost.php' method='post'>";
        echo "<input type='radio' name='select_type' value='text'<label for='select_post'>Tekst</label><br>";
		echo "<input type='radio' name='select_type' value='image'<label for='select_post'>Obraz</label><br>";
		echo "<input type='submit'>";
		echo "</form>";
		echo "</div>";

	?>
</body>
</html>