<!DOCTYPE html>
<html>
<head>
	<title>j24 dodaj post</title>
	<meta charset="utf-8">
</head>
<body>
	<?php
		session_start();
		include $_SERVER["DOCUMENT_ROOT"]."/includes/redir_if_not_logged.php";
		require $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";

		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$post_id = $_POST["draft_id"];
			$sql = "UPDATE posts SET published = 1 WHERE id = '$post_id'";
			$conn->query($sql);

			if (isset($_POST["image_id"])) {
				$image_id = $_POST["image_id"];
				$sql = "UPDATE images SET posted = 1 WHERE id = '$image_id'";
				$conn->query($sql);
			}
			
			$_SESSION["post_type"] = null;
			$_SESSION["draft_id"] = null;
			
			echo "Post został dodany.<br>";
			echo "<a href='../index.php'>Strona główna</a><br>
				<a href='/../user/userpanel.php'>Panel użytkownika</a>";
		}
	?>
</body>
</html>