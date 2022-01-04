<?php

    if (count(get_included_files) == 1) {
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden.");
    }

    class Post {
        public $conn;
        public $title;
        public $category;
        public $tags;
        
        function __construct($conn, $title, $category, $tags) {
            $this->conn = $conn;
            $this->title = $this->check_input($title);
            $this->category = $this->check_input($category);
            $this->tags = $this->check_input($tags);
        }

        function check_input($data) {
            $vetted_data = trim($data);
            if (substr_compare($vetted_data, "html", 0, 4, true) != 0) {
                $vetted_data = htmlspecialchars($vetted_data);
            }
            $vetted_data = stripslashes($vetted_data);
			return $vetted_data;
		}

        function save_to_db($text_content, $image, $image_id, $image_path, $image_desc, $published) {
            $stmt = $this->conn->prepare("INSERT INTO posts (category, title, text_content, image, image_id, image_path, image_desc, tags, post_date, published)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
            $stmt->bind_param("sssiisssi", $this->category, $this->title, $text_content, $image, $image_id, $image_path, $image_desc, $this->tags, $published);
            $stmt->execute();
        }

        function update_post($text_content, $image, $image_id, $image_path, $image_desc, $post_id) {
            $stmt = $this->conn->prepare("UPDATE posts SET category=?, title=?, text_content=?, image=?, image_id=?, image_path=?, image_desc=?, tags=? WHERE id=?");
            $stmt->bind_param("sssiisssi", $this->category, $this->title, $text_content, $image, $image_id, $image_path, $image_desc, $this->tags, $post_id);
            $result = $stmt->execute();
            if ($result === false) {
                $result->trigger_error($stmt->error, E_USER_ERROR);
            }         
        }
    }


    class TextPost extends Post {
        public $text_content;

        function __construct($conn, $title, $category, $tags, $text_content) {
            parent::__construct($conn, $title, $category, $tags);
            $this->text_content = $this->check_input($text_content);
        }
    }


    class ImagePost extends Post {
        public $image_id;
        public $image_path;
        public $image_name;
        public $image_desc;

        function __construct($conn, $title, $category, $tags, $image_id, $image_path, $image_name, $image_desc) {
            parent::__construct($conn, $title, $category, $tags);
            $this->image_id = $image_id;
            $this->image_path = $image_path;
            $this->image_name = $image_name;
            $this->image_desc = $this->check_input($image_desc);
        }
    }
?>