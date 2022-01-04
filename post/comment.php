<?php

    if (count(get_included_files) == 1) {
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden.");
    }
    
    class Comment {
        public $conn;
        public $post_id;
        public $author;
        public $content;
        public $author_ip;
        public $reply_to;
        public $error_msg;
        const vulg = array("jeba", "chuj", "kurw");

        function __construct($conn, $post_id, $author, $content, $author_ip, $reply_to) {
            $this->conn = $conn;
            $this->post_id = $post_id;
            $this->author = $this->check_wrapper($author, "username");
            $this->content = $this->check_wrapper($content, "content");
            $this->author_ip = $this->prevent_flood($author_ip);
            $this->reply_to = $reply_to;
            $this->error_msg = false;
        }

        function check_input($data) {
            $vetted_data = trim($data);
            $vetted_data = htmlspecialchars($vetted_data);
            $vetted_data = stripslashes($vetted_data);
			return $vetted_data;
		}

        function check_length($data, $data_type) {
            if ($data_type == "username") {
                if (strlen($data) > 100 or strlen($data) == 0) {
                    return false;
                } else {
                    return true;
                }
            } elseif ($data_type == "content") {
                if (strlen($data) > 3000 or strlen($data) == 0) {
                    return false;
                } else {
                    return true;
                }
            }
        }

        function check_content($content) {
            $content_check = $content;
            foreach(explode(" ", $content) as $el) {
                if (substr_compare($el, "http", 0, 4) == 0 and strlen($el) > 7) {
                    return false;
                }
                if (in_array(substr($el, 0, 4), self::vulg)) {
                    $content = str_ireplace($el, str_repeat("*", strlen($el)), $content_check);
                }
            }
            return $content;    
        }

        function prevent_flood($author_ip) {
            $sql = "SELECT MAX(date) FROM comments WHERE ip = '$author_ip'";
            $cursor = $this->conn->query($sql);
            if ($cursor) {
                $row = $cursor->fetch_row();
                $last_comment_date = strtotime($row[0]);

                if ((time() - $last_comment_date) < 15) {
                        return false;
                } else {
                    return $author_ip;
                }
            } else {
                return $author_ip;
            }
        }

        function check_wrapper($content, $data_type) {
            $content = $this->check_input($content);
            if ($data_type == "content") {
                if (!$this->check_length($content, "content")) {
                    return false;
                }
                $content = $this->check_content($content);
                return $content;
            } else {
                if (!$this->check_length($content, "username")) {
                    return false;
                }
                return $content;
            }
        }

        function save_or_error() {
            if (!$this->author) {
                $this->error_msg = "Nieprawidłowa nazwa użytkownika";
            } elseif (!$this->content) {
                $this->error_msg = "Niedozwolone znaki lub niedozwolona długość komentarza";
            } elseif (!$this->author_ip) {
                $this->error_msg = "Komentujesz zbyt szybko.";
            } else {
                if ($this->reply_to == 0) {
                    $stmt = $this->conn->prepare("INSERT INTO comments (post_id, author, ip, content) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("isss", $this->post_id, $this->author, $this->author_ip, $this->content);
                    $result = $stmt->execute();
                } else {
                    $stmt = $this->conn->prepare("INSERT INTO comments (post_id, author, ip, content, reply_to) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("isssi", $this->post_id, $this->author, $this->author_ip, $this->content, $this->reply_to);
                    $result = $stmt->execute();
                }

                if ($result === false) {
                    $this->error_msg = $result->trigger_error($stmt->error, E_USER_ERROR);
                } else {
                    return true;
                }
            }
            return false;
        }
    }
?>