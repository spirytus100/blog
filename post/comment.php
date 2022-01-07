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

        function anti_spam($author_ip) {
            $sql = "SELECT content FROM comments WHERE DATE(date) = CURDATE() AND ip = '$author_ip'";
            $cursor = $this->conn->query($sql);

            $i = 0;
            $spam_count = 0;
            $spam_list = array();

            while ($row = $cursor->fetch_assoc()) {
                $pot_spam = trim(strpbrk($row["content"], ":"), ": ");
                if (!$pot_spam) {
                    $pot_spam = $row["content"];
                }
                if ($i > 1) {
                    foreach ($spam_list as $key=>$val) {
                        similar_text($val, $pot_spam, $percent);
                        if ($percent > 70) {
                            $spam_count += 1;
                            unset($spam_list[$key]);
                            if ($spam_count > 5) {
                                return false;
                            }
                        }
                    }
                }
                array_push($spam_list, $pot_spam);
                $i += 1;
            }
            return true;
        }

        function prevent_flood($author_ip) {
            if (!$this->anti_spam($author_ip)) {
                return false;
            }

            $sql = "SELECT MAX(date) FROM comments WHERE ip = '$author_ip'";
            $cursor = $this->conn->query($sql);
            if ($cursor) {
                $row = $cursor->fetch_row();
                $last_comment_date = strtotime($row[0]);

                if ((time() - $last_comment_date) < rand(15, 90)) {
                        return false;
                } else {
                    return $author_ip;
                }
            } else {
                return $author_ip;
            }
        }

        function prevent_db_overflow() {
            $sql = "SELECT COUNT(id) FROM comments WHERE DATE(date) = CURDATE()";
            $cursor = $this->conn->query($sql);

            $last_hour = new DateInterval("PT1H");
            $date_start = date_sub(date_create(), $last_hour);
            $date_compare = date_sub($date_start, $last_hour);
    
            $compare_count = 0;
            $curr_count = 0;
    
            while ($row = $cursor->fetch_assoc()) {
                if ($date_compare < date_create($row["date"]) and date_create($row["date"]) < $date_start) {
                    $compare_count += 1;
                }
                if (date_create($row["date"]) > $date_start) {
                    $curr_count += 1;
                }
            }
            
            if ($compare_count != 0) {
                if ($curr_count > pow($compare_count, 4)) {
                    return false;
                }
            } else {
                if ($curr_count > 40) {
                    return false;
                }
            }
            return true;
        }

        function check_wrapper($content, $data_type) {
            if (!$this->prevent_db_overflow()) {
                return false;
            }

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
                $this->error_msg = "Komentujesz zbyt szybko lub zbyt często się powtarzasz.";
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