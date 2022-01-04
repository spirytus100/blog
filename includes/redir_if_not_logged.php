<?php
    if (count(get_included_files) == 1) {
		header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden.");
	}
    
    if (empty($_SESSION)) {
        header("Location: /user/login.php");
    }
?>