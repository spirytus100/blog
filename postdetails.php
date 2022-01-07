<!DOCTYPE html>
<html>
<head>
	<title>xxx</title>
	<meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php
        include $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";
        include $_SERVER["DOCUMENT_ROOT"]."/post/feed.php";

        echo "<div class='container'>";

	    echo "<div class='row' style='margin-top: 15px; margin-bottom: 50px; background-color: black; text-align: center'>
				<a href='index.php' style='text-decoration: none'><p class='h1' class='text-center' style='color: white; padding: 15px'>j24</p></a>
		    </div>";

        $post_id = $_GET["id"];
        $sql = "SELECT posts.id,
					posts.category,
					posts.title, 
					posts.text_content, 
					posts.image, 
					posts.image_path, 
					posts.image_desc, 
					posts.post_date, 
					COUNT(comments.post_id) as comment_count
			FROM posts LEFT OUTER JOIN comments ON posts.id=comments.post_id 
            WHERE posts.id = '$post_id'";

        $cursor = $conn->query($sql);
	    $row = $cursor->fetch_row();

        echo "<div class='row'>";
            echo "<div class='col-sm-8'>";
            if ($row[4] == false) {
                display_full_text_post($row[0], $row[7], $row[2], $row[1], $row[3], $row[8]);
            } else {
                display_image_post($row[0], $row[7], $row[2], $row[1], "/../".$row[5], $row[6], $row[8]);
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

        echo "<div class='row' style='margin-top: 4%'>
                <div class='col-sm-8'>
                    <p class='h4'>Dodaj komentarz</p>";
                    display_comment_form($post_id, $_SERVER["REMOTE_ADDR"]);
            echo "</div>
                <div class='col-sm-4'>
                </div>
            </div>";

        echo "<div class='row' style='margin-top: 2%'>
                <div class='col-sm-8'>";
                display_comments($post_id, $conn, $_SERVER["REMOTE_ADDR"]);
            echo "</div>
                <div class='col-sm-4'>
                </div>
            </div>";

        echo "</div>";

        echo "<footer class='mt-auto'>
    		<div style='background-color: black; color: white; width: 100%; height: 100px; padding: 15px; text-align: center'>
      			<p>Autor: </p>
    		</div>
  		</footer>";
    ?>
</body>
</html>
