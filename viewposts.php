<!DOCTYPE html>
<html>
<head>
	<title>xxx</title>
	<meta charset='utf-8'>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class='d-flex flex-column min-vh-100'>

<?php
    session_start();

    include $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";
    include $_SERVER["DOCUMENT_ROOT"]."/post/feed.php";

	echo "<div class='container'>";

	echo "<div class='row' style='margin-top: 15px; margin-bottom: 50px; background-color: black; text-align: center'>
				<a href='index.php' style='text-decoration: none'><p class='h1' class='text-center' style='color: white; padding: 15px'>j24</p></a>
		</div>";

	if (isset($_GET["string"])) {
		$phrase = trim($_GET["string"]);
    	$phrase = htmlspecialchars($phrase);
		$sql = "SELECT count(id) FROM posts WHERE published = 1 AND (title LIKE '%".$phrase."%' OR category LIKE '%".$phrase."%' OR tags LIKE '%".$phrase."%')";
	} elseif (isset($_GET["category"])) {
		$phrase = trim($_GET["category"]);
    	$phrase = htmlspecialchars($phrase);
		$sql = "SELECT count(id) FROM posts WHERE published = 1 AND category = '$phrase'";
	} else {
		$sql = "SELECT count(id) FROM posts WHERE published = 1";
	}
	
    $cursor = $conn->query($sql);
    $row = $cursor->fetch_row();
    $all_records = $row[0];
    $num_pages = ceil($all_records/10-1);

    if (isset($_GET["page"])) {
        $page = $_GET["page"];
        if ($page == 1) {
            $offset = 0;
        } else {
			if ($num_pages == 1) {
				$offset = 10;
			} else {
            	$offset = ($page-1)*10+1;
			}
        }
    } else {
        $page = 1;
        $offset = 0;
    }

	if (isset($_GET["string"])) {
		$sql = "SELECT posts.id, posts.category, posts.title, posts.text_content, posts.image, posts.image_path, posts.image_desc, posts.post_date, posts.tags, COUNT(comments.post_id) AS comments
				FROM posts LEFT OUTER JOIN comments ON posts.id=comments.post_id
				WHERE posts.published = 1 AND (posts.title LIKE '%".$phrase."%' OR posts.category LIKE '%".$phrase."%' OR posts.tags LIKE '%".$phrase."%')
				GROUP BY posts.id
				ORDER BY posts.id 
				DESC LIMIT ".$offset.", 10";
	} elseif (isset($_GET["category"])) {
		$sql = "SELECT posts.id, posts.category, posts.title, posts.text_content, posts.image, posts.image_path, posts.image_desc, posts.post_date, COUNT(comments.post_id) AS comments
				FROM posts LEFT OUTER JOIN comments ON posts.id=comments.post_id
				WHERE posts.published = 1 AND posts.category = '$phrase'
				GROUP BY posts.id
				ORDER BY posts.id 
				DESC LIMIT ".$offset.", 10";
	}

    $cursor = $conn->query($sql);

    echo "<div class='row'>";
		echo "<div class='col-sm-8'>";
		while ($row = $cursor->fetch_assoc()) {
			if ($row["image"] == false) {
				display_text_excerpt($row["id"], $row["post_date"], $row["title"], $row["category"], $row["text_content"], $row["comments"]);
			} else {
				display_image_post($row["id"], $row["post_date"], $row["title"], $row["category"], "/../".$row["image_path"], $row["image_desc"], $row["comments"]);
			}
		}
		echo "</div>";

		echo "<div class='col-sm-4'>";
			echo "<div style='background-color: white; padding: 15px; margin-bottom: 25px'>
				<h4 style='margin-left: 30px' >Wyszukaj na stronie</h4>";
				display_search_form();
			echo "</div>";
			echo "<div style='background-color: white; padding: 15px; margin-bottom: 25px'>
				<h4 style='margin-left: 30px'>O czym piszę</h4>";
				display_popular_tags($conn);
			echo "</div>";
		echo "</div>";
	echo "</div>";


	echo "<div class='row'>";
		echo "<div class='col-sm-12' style='padding:5px; background-color: white; margin-bottom: 10px; text-align: center'>";
		echo "<p>Strona</p>";
		$num_pages = ceil($all_records/10);
		if ($num_pages > 10) {
			$too_many_pages = true;
		}
		for ($i = 1; $i<=$num_pages; $i++) {
			if ($i == $page) {
				echo $i;
			} else {
				if ($too_many_pages) {
					if ($i <= 4 or $i >= $num_pages-3) {
						if (isset($_GET["category"])) {
							echo $cat_paging;
						} else {
							echo "<a href=?page=".$i.">".$i."</a>";
						}
						if ($i == 5) {
							echo "...";
						}
					}
				} else {
					if (isset($_GET["category"])) {
						echo "<a href=?category=".$phrase."&page=".$i.">".$i."</a>";
					} else {
						echo "<a href=?page=".$i.">".$i."</a>";
					}
				}
			}
		}
		echo "</div>";
	echo "</div>";

	echo "</div>";

	echo "<footer class='mt-auto'>
    		<div style='background-color: black; color: white; width: 100%; height: 100px; padding: 15px; text-align: center'>
      			<p>Autor: </p>
    		</div>
  		</footer>";

	$conn->close;
?>
</body>
</html>
