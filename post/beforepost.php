<!DOCTYPE html>
<html>
<head>
	<title>j24 podgląd wpisu</title>
	<meta charset="utf-8">
</head>
<body>
	<?php
		session_start();
        include $_SERVER["DOCUMENT_ROOT"]."/includes/redir_if_not_logged.php";
        include $_SERVER["DOCUMENT_ROOT"]."/post/feed.php";
		require $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";

        function echo_post_details($title, $category, $tags, $post_type, $draft_id, $image_id) {
            if ($post_type == "text") {
                $back_path = "textpost.php";
            } elseif ($post_type == "image") {
                $back_path = "attachimage.php";
            }
            echo "<div id='post-details'>
                    <p>Tytuł posta: ".$title."</p>
                    <p>Kategoria: ".$category."</p>
                    <p>Tagi: ".$tags."</p><br>
                <a href='".$back_path."'>Edycja wpisu</a>
                <form action='addpost.php' method='post'>
                    <input type='hidden' name='draft_id' value='".$draft_id."'>
                    <input type='hidden' name='image_id' value='".$image_id."'>
                    <input type='submit' value='Opublikuj'>
                </form>";
        }

        $draft_id = $_SESSION["draft_id"];
        $post_type = $_SESSION["post_type"];

        if ( $post_type === "image") {
            $sql = "SELECT posts.category, posts.title, images.id, images.image_name, images.image_path, posts.image_desc, posts.tags
                        FROM posts
                        INNER JOIN images ON posts.image_id=images.id
                        WHERE posts.id = '$draft_id'";

            $cursor = $conn->query($sql);
            $row = $cursor->fetch_row();

            echo "<div id='img-container'>
                    <img src='".substr($row[4], 14)."' alt='".$row[3]."' style='width: 150px'>
                </div>
                <div id='desc-container'>
                    <p>".$row[5]."</p>
                </div>";
                echo_post_details($row[1], $row[0], $row[6], $post_type, $draft_id, $row[2]);
                
        } elseif ($post_type === "text") {
            $sql = "SELECT category, title, text_content, tags
                    FROM posts
                    WHERE id = '$draft_id'";
                
            $cursor = $conn->query($sql);
            $row = $cursor->fetch_row();

            echo "<div id='text-container'>";
                   format_paragraphs($row[2]);
            echo "</div>";
            echo_post_details($row[1], $row[0], $row[3], $post_type, $draft_id, null);        
        } else {
            header("Location: user/userpanel.php");
        }
	?>
</body>
</html>