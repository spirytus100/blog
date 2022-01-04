<?php

    if (count(get_included_files) == 1) {
        header($_SERVER["SERVER_PROTOCOL"]." 403 Forbidden.");
    }

    function create_link($paragraph) {
        $words = explode(" ", $paragraph);
        $links = array();
        $i = 0;

        foreach ($words as $word) {
            $trimmed = trim($word);
            if (substr_compare($trimmed, "http", 0, 4, true) == 0) {
                $last_char = substr($trimmed, -1);
                if (in_array($last_char, array(".", ",", ";", ":"))) {
                    $trimmed = rtrim($trimmed, $last_char);
                    $link = "<a href='".$trimmed."'>".$trimmed."</a>";
                    $links[$i] = $link.$last_char;
                } else {
                    $link = "<a href='".$trimmed."'>".$trimmed."</a>";
                    $links[$i] = $link;
                }  
            }
            $i++;
        }

        if (count($links) > 0) {
            foreach($links as $key=>$val) {
                $words[$key] = $val;
            }
            $link_par = implode(" ", $words);
            return $link_par;

        } else {
            return $paragraph;
        }   
    }


    function format_paragraphs($text_content) {
        $html = false;
        if (substr_compare($text_content, "html", 0, 4, true) == 0) {
            echo substr($text_content, 4);
            $html = true;
        }
        if (!$html) {
            $exploded = explode("\n", $text_content);
            foreach ($exploded as $text_part) {
                echo "<p class='bg-light'>".create_link($text_part)."</p>";
            }
        }
    }


    function display_text_excerpt($id, $post_date, $title, $category, $text_content, $comment_count) {
        $html = false;
        if (substr_compare($text_content, "html", 0, 4, true) == 0) {
            $text_content = substr($text_content, 4);
            $html = true;
        } else {
            if (strlen($text_content) > 300) {
                $text_content = substr($text_content, 0, 300);
                $text_content = $text_content."... <a href='postdetails.php?id=".$id."'>Czytaj dalej</a>";
            }
        }

        echo "<div style='background-color: white; padding: 10px; padding-bottom: 35px; margin-bottom: 10px; border-style: solid; border-width: 2px; border-color: darkgrey'>
            <p class='post-date'>".$post_date."</p>
            <p class='h3'><a href='postdetails.php?id=".$id."' style='text-decoration: none; color: black'>".$title."</a></p>
            <div class='bg-light' style='margin-top: 10px; border-bottom-style: solid; border-width: 1px, border-color: darkred'>";
                if ($html) {
                    echo $text_content;
                } else {
                    echo "<p>".create_link($text_content)."</p>";
                }
            echo "</div>
            <a href='viewposts.php?category=".$category."' style='text-decoration: none'><span style='color: darkred; float: left'>".$category."</span></a>
            <span style='float: right'>komentarze ".$comment_count."</span>
        </div>";
    }


    function display_full_text_post($id, $post_date, $title, $category, $text_content, $comment_count) {
        echo "<div style='background-color: white; padding: 10px; padding-bottom: 35px; border-style: solid; border-width: 2px; border-color: darkgrey'>
            <p class='post-date'>".$post_date."</p>
            <h3>".$title."</h3>
            <div style='margin-top: 10px; border-bottom-style: solid; border-width: 1px, border-color: darkred'>";
                format_paragraphs($text_content);
        echo "</div>
        <a href='viewposts.php?category=".$category."' style='text-decoration: none'><span style='color: darkred; float: left'>".$category."</span></a>
        <span style='float: right'>komentarze ".$comment_count."</span>
        </div>";
    }


    function display_image_post($id, $post_date, $title, $category, $image_path, $image_desc, $comment_count) {
        echo "<div style='background-color: white; padding: 10px; padding-bottom: 35px; margin-bottom: 10px; border-style: solid; border-width: 2px; border-color: darkgrey'>
            <p class='post-date'>".$post_date."</p>
            <p class='h3'><a href='/post/postdetails.php?id=".$id."' style='text-decoration: none; color: black'>".$title."</a></p>
            <div style='margin-top: 10px'><img class='rounded' src='".$image_path."' alt='".$image_name."' style='width: 90%'></div>
            <p style='border-bottom-style: solid; border-width: 1px, border-color: darkred'>".$image_desc."</p>
            <a href='viewposts.php?category=".$category."' style='text-decoration: none'><span style='color: darkred; float: left'>".$category."</span></a>
            <span style='float: right'>komentarze ".$comment_count."</span>
        </div>";
    }


    function display_search_form() {
        echo "<form action='viewposts.php' method='get'>
                <input class='form-control' type='text' name='string'><br>
                <button class='btn' type='submit'>Wyszukaj</button>
            </form>";
    }


    function display_popular_tags($conn) {
        $sql = "SELECT title, category FROM posts WHERE published = 1";
        $cursor = $conn->query($sql);
        $category_dict = array();
        $cat_count = 0;
        while ($row = $cursor->fetch_assoc()) {
            $category = $row["category"];
            if (!array_key_exists($category, $category_dict)) {
                $category_dict[$category] = 1;
            } else {
                $category_dict[$category]++;
            }
            $cat_count++;
        }

        asort($category_dict);
        $arr_len = count($category_dict);
        $tag_avg = round($cat_count/$arr_len, 2);
        $counter = 0;
        foreach ($category_dict as $key=>$val) {
            $counter = $counter + ($val-$tag_avg);
        }
        $st_dev = sqrt(round($counter, 2)/$arr_len);
        if (is_nan($st_dev)) {
            $st_dev = 1;
        }

        $threshold = $tag_avg;
        $font_size = 13;
        $html_tags = array();
        foreach ($category_dict as $key=>$val) {
            $tag_share = round($val/$cat_count, 2);
            if ($tag_share > $threshold) {
                $font_size += 2;
                $html_p= "<p class='bg-info' style='font-size:".$font_size."px; display: inline; padding:2px'>".$key."</p>";
                array_push($html_tags, $html_span);
                $threshold += $st_dev;
            } else {
                $html_p= "<p class='bg-info' style='font-size:".$font_size."px; display: inline; padding:2px'>".$key."</p>";
                array_push($html_tags, $html_p);
            }
        }
                           
        shuffle($html_tags);
        foreach ($html_tags as $html_p) {
            echo $html_p." ";
        }
    }

    function display_comment_form($post_id, $author_ip) {
        echo "<form action='/post/addcomment.php' method='post'>
                <label for='author'>Autor</label>
                <input class='form-control' type='text' name='author'><br>
                <textarea rows=4 class='form-control' name='comment'></textarea><br>
                <input type='hidden' name='post_id' value='".$post_id."'>
                <input type='hidden' name='author_ip' value='".$author_ip."'>
                <button class='btn' type='submit'>Dodaj komentarz</button>
            </form>";
    }

    function display_comments($post_id, $conn, $author_ip) {
        $hidden_comm_form = "<form id='replyform%s'  class='reply-form' style='display: none' action='/post/addcomment.php' method='post'>
                                <label for='author'>Autor</label>
                                <input class='form-control' type='text' name='author'><br>
                                <textarea rows=4 class='form-control' name='comment'>%s</textarea><br>
                                <input type='hidden' name='post_id' value='%s'>
                                <input type='hidden' name='reply_to' value='%s'>
                                <input type='hidden' name='author_ip' value='%s'>
                                <button class='btn' type='submit'>Dodaj komentarz</button>
                            </form>";

        $sql = "SELECT id, author, content, date, reply_to FROM comments WHERE post_id = '$post_id' AND permitted = 1";
        $cursor = $conn->query($sql);

        $js = 'var x = document.getElementById("%s");
                var id = x.getAttribute("id");
                x.style.display = "block";
                var reply_forms = document.getElementsByClassName("reply-form");
                for (let i = 0; i <= reply_forms.length; i++) {
                    if (reply_forms[i].getAttribute("id") == id) {
                        continue;
                    } else {
                        reply_forms[i].style.display = "none";
                    }
                }';

        $comm_html = "<div style='%s' ><p class='date' style='font-size: 10px'>%s</p><p class='author' style='font-weight: bold'>%s</p><p class='content'>%s</p><a href='javascript:void(0);'
                     onclick='%s'>Odpowiedz</a></div><br>";

        $replies = array();
        $comm_list = array();
        $css_comm = "background-color: white; padding: 10px; border-style: solid; border-width: 2px";

        while ($row = $cursor->fetch_assoc()) {
            $comment = sprintf($comm_html, $css_comm, $row["date"], $row["author"], $row["content"], "%s");
            $comm_list[$row["id"]] = $comment;

            $i = 0;
            if ($row["reply_to"] != 0) {
                array_push($replies, array($row["reply_to"], $row["id"], $row["date"], $row["author"], $row["content"]));
            }
        }
        
        function check_replies($replies, $id) {
            $replies_to = array();
            foreach ($replies as $reply) {
                if ($reply[0] == $id) {
                    array_push($replies_to, $reply);
                }
            }
            if (count($replies_to) > 0) {
                return $replies_to;
            } else {
                return false;
            }
        }

        $css_reply = "background-color: white; padding: 10px; position: relative; left: 30px; border-style: solid; border-width: 2px";

        if (count($comm_list) == 0) {
            echo "<p class='h4'>Brak komentarzy</p>";
        } else {
            echo "<p class='h4'>Komentarze</p>";
        }

        $i = 0;
        $replied = array();
        foreach ($comm_list as $key=>$val) {
            if (in_array($key, $replied)) {
                continue;
            }
            $i++;
            
            $replied_to = strstr($val, "<p class='author'");
            $p_el = strpos($replied_to, "</p>") + 3;
            $replied_to = strip_tags(substr($replied_to, 0, $p_el));

            $hidden_form = sprintf($hidden_comm_form, $i, "@".$replied_to.": ", $post_id, $key, $author_ip);
            echo sprintf($val,  sprintf($js, "replyform".$i)) . $hidden_form;
            $replies_to = check_replies($replies, $key);
            
            if (!$replies_to) {
                continue;
            } else {
                foreach ($replies_to as $reply_data) {
                    $i++;
                    $hidden_form = sprintf($hidden_comm_form, $i, "@".$reply_data[3].": ", $post_id, $reply_data[0], $author_ip);
                    $reply_comment = sprintf($comm_html, $css_reply, $reply_data[2], $reply_data[3], $reply_data[4], "%s");

                    echo sprintf($reply_comment, sprintf($js, "replyform".$i)) . $hidden_form;
                    array_push($replied, $reply_data[1]);

                    $replies_to = check_replies($replies, $reply_data[1]);
                    if (!$replies_to) {
                        continue;
                    }
                    foreach ($replies_to as $reply_data) {
                        $i++;
                        $hidden_form = sprintf($hidden_comm_form, $i, "@".$reply_data[3].": ", $post_id, $reply_data[0], $author_ip);
                        $reply_comment = sprintf($comm_html, $css_reply, $reply_data[2], $reply_data[3], $reply_data[4], "%s");
    
                        echo sprintf($reply_comment, sprintf($js, "replyform".$i)) . $hidden_form;
                        array_push($replied, $reply_data[1]);
                    }
                }
            }
        }
    }
?>