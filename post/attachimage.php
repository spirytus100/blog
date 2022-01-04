<!DOCTYPE html>
<html>
<head>
	<title>j24 załącz obraz</title>
	<meta charset="utf-8">
</head>
<body>
	<?php
		session_start();
		include $_SERVER["DOCUMENT_ROOT"]."/includes/redir_if_not_logged.php";
		require $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";

		$sql_select = "SELECT id, image_name FROM images";
		$result = $conn->query($sql_select);

		$title = "";
		$category = "";
		$image_desc = "";
		$tags = "";

		if (isset($_SESSION["draft_id"])) {
			$draft_id = $_SESSION["draft_id"];
			$sql = "SELECT title, category, image_desc, tags FROM posts WHERE id = '$draft_id'";
			$cursor = $conn->query($sql);
			$edit_row = $cursor->fetch_row();

			$title = $edit_row[0];
			$category = $edit_row[1];
			$image_desc = $edit_row[2];
			$tags = $edit_row[3];
		}

		echo "<div id='image-container'>
				<form action='validate.php' method='post'>
					<label for='title'>Tytuł posta</label>
					<textarea rows=1 cols=90 name='title'>".$title."</textarea><br><br>
					<label for='category'>Podaj kategorię</label>
					<input type='text' name='category' value='".$category."'><br><br>
					<label for='tags'>Podaj tagi oddzielone przecinkami</label>
					<input type='text' name='tags' value='".$tags."'><br><br>";
					foreach ($result as $row) {
						echo "<input type='radio' name='image_id' value='".$row['id']."' required><label for='image_name'>".$row['image_name']."</label><br>";
					}
					echo "<label for='image_desc'>Dodaj podpis</label><textarea rows=1 cols=90 name='image_desc'>".$image_desc."</textarea>
					<input type='submit'>
				</form>
			</div>";

	?>
</body>
</html>