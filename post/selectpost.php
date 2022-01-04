<?php
    session_start();
    include $_SERVER["DOCUMENT_ROOT"]."/includes/redir_if_not_logged.php";
    
    if (isset($_SERVER["REQUEST_METHOD"])) {
        $_SESSION["post_id"] = null;
        
		if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if ($_POST["select_type"] == "text") {
                header("Location: textpost.php");
            } else {
                header("Location: attachimage.php");
            }
        }
    }
?>