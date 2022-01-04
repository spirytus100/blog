<?php
    session_start();

    if (count(get_included_files) == 1) {
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden.");
    }

    include $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";
    include $_SERVER["DOCUMENT_ROOT"]."/post/post_class.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $title = $_POST["title"];

        if ($_POST["new_category"] == "") {
            $category = $_POST["category"];
        } else {
            $category = $_POST["new_category"];
        }

        $tags = $_POST["tags"];


        if (isset($_POST["text_content"])) {
            $text_content = $_POST["text_content"];
            $text_post = new TextPost($conn, $title, $category, $tags, $text_content);

            if (isset($_SESSION["draft_id"])) {
                $text_post->update_post($text_post->text_content, false, null, null, null, $_SESSION["draft_id"]);
            } else {
                $text_post->save_to_db($text_post->text_content, false, false, null, null, false);
            }

            $_SESSION["post_type"] = "text";

        } elseif (isset($_POST["image_id"])) {
            $image_id = $_POST["image_id"];
            $image_desc = $_POST["image_desc"];
            $sql = "SELECT image_name, image_path FROM images WHERE id = '$image_id'";
            $cursor = $conn->query($sql);
            $row = $cursor->fetch_row();
            $image_name = $row[0];
            $image_path = substr($row[1], 14);
            $image_post = new ImagePost($conn, $title, $category, $tags, $image_id, $image_path, $image_name, $image_desc);

            if (isset($_SESSION["draft_id"])) {
                $image_post->update_post(null, true, $image_id, $image_path, $image_post->image_desc, $_SESSION["draft_id"]);
            } else {
                $image_post->save_to_db(null, true, $image_id, $image_path, $image_post->image_desc, false);
            }

            $_SESSION["post_type"] = "image";
        }
        
        if (!isset($_SESSION["draft_id"])) {
            $_SESSION["draft_id"] = $conn->insert_id;
        }
        header("Location: beforepost.php");
    }
?>