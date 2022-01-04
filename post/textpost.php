<!DOCTYPE html>
<html>
<head>
	<title>j24 dodaj post</title>
	<meta charset="utf-8">
	<style>
		input.form-text-content {
			height: 400px;
			width: 50%;
		}
	</style>
</head>
<body>
	<?php
		session_start();
        include $_SERVER["DOCUMENT_ROOT"]."/includes/redir_if_not_logged.php";
		include $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";

		if (isset($_SESSION["draft_id"])) {
			$draft_id = $_SESSION["draft_id"];
			$sql = "SELECT title, text_content, category, tags FROM posts WHERE id = '$draft_id'";
			$cursor = $conn->query($sql);
			$row = $cursor->fetch_row();

			echo "<form action='validate.php' method='post'>
				<label for='title'>Tytuł posta</label>
				<textarea rows=1 cols=90 name='title'>".$row[0]."</textarea><br><br>
				<label for='text_content'>Treść posta:</label>
				<textarea rows=20 cols=90 name='text_content'>".$row[1]."</textarea><br><br>
				<label for='category'>Podaj kategorię</label>
				<input type='text' name='category' value='".$row[2]."'><br><br>
				<label for='tags'>Podaj tagi oddzielone przecinkami</label>
				<input type='text' name='tags' value='".$row[3]."'><br><br>
				<button type='submit'>Dodaj post</button>
			</form>";
		} else {
			echo "<form action='validate.php' method='post'>
					<label for='title'>Tytuł posta</label>
					<textarea rows=1 cols=90 name='title'></textarea><br><br>
					<label for='text_content'>Treść posta:</label>
					<textarea rows=20 cols=90 name='text_content'></textarea><br><br>
					<label for='category'>Podaj kategorię</label><br>";

					$sql_select = "SELECT DISTINCT category FROM posts";
					$cursor = $conn->query($sql_select);
					while ($row = $cursor->fetch_assoc()) {
						echo "<input type='radio' id='".$row["category"]."' name='category' value='".$row["category"]."'>
								<label for='".$row["category"]."'>".$row["category"]."</label><br>";
					}
					
				echo "<label for='new_category'>Nowa kategoria</label>
					<input type='text' name='new_category'><br><br>
					<label for='tags'>Podaj tagi oddzielone przecinkami</label>
					<input type='text' name='tags'><br><br>
					<button type='submit'>Dodaj post</button>
				</form>";
		}
	?>
</body>
</html>