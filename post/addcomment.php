<?php
    session_start();
    include $_SERVER["DOCUMENT_ROOT"]."/post/comment.php";
    include $_SERVER["DOCUMENT_ROOT"]."/includes/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $author = $_POST["author"];
        $content = $_POST["comment"];
        $post_id = $_POST["post_id"];
        $ip = $_POST["author_ip"];

        if (isset($_POST["reply_to"])) {
            $reply_to = $_POST["reply_to"];
        } else {
            $reply_to = 0;
        }

        $comment = new Comment($conn, $post_id, $author, $content, $ip, $reply_to);

        if ($comment->save_or_error()) {
            header("Location: /../postdetails.php?id=".$post_id);
        } else {
            echo $comment->error_msg;
        }
    }
?>