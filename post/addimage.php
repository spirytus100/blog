<?php
	session_start();

	require $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";

	$target_dir = $_SERVER['DOCUMENT_ROOT'] . "/images/";
	$file = $target_dir . basename($_FILES["image"]["name"]);
	$img_name = $_FILES["image"]["name"];

	if (isset($_FILES['image'])) {
		$check = getimagesize($_FILES["image"]["tmp_name"]);

		if ($check === false) {
			die("Plik nie jest obrazem.");
		}

		if ($_FILES['image']["size"] > 5000000) {
			die("Plik jest zbyt duży.");
		}

		$filetype = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		if ($filetype != "jpg" and $filetype != "jpeg" and $filetype != "png") {
			die("Nieprawidłowy format pliku");
		}

		if (move_uploaded_file($_FILES["image"]["tmp_name"], $file)) {
			$sql_insert = "INSERT INTO images (image_name, image_path) VALUES ('$img_name', '$file')";
			if ($conn->query($sql_insert) == false) {
				echo "Coś poszło nie tak. Obraz nie został dodany do bazy danych.<br>";
				echo "<a href='/../user/userpanel.php'>Spróbuj ponownie</a>";
			} else {
				echo "Obraz został dodany.<br>";
			}
			
		} else {
			echo "Coś poszło nie tak. Obraz nie został dodany.<br>";
			echo "<a href='/../user/userpanel.php'>Spróbuj ponownie</a>";
		}
	}
	
?>