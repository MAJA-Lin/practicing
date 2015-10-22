<?php

    namespace board\msg_management;

    include_once("connection.php");

    use board\connection as connect;

    class Message extends connect\SqlConnection
    {
        public function printout()
        {
            $mysqli = $this->dbConnection();
            $i = 0;
            $limit = 10;
            $sql= "SELECT count(sn) FROM message";
            $result = $mysqli->query($sql);
            if (!$result) {
                die('Failed to get data: ' . $mysqli->error);
            }
            $row = $result->fetch_array(MYSQL_NUM);
            $count = $row[0];

            if (isset($_GET{'page'})) {
                $page = $_GET{'page'} + 1;
                $offset = $limit * ($page - 1);
            } else {
                $page = 1;
                $offset = 0;
            }

            $left_data = $count - (($page - 1) * $limit);
            $sql = "SELECT * FROM message LIMIT $offset, $limit";

            if ($result = $mysqli->query($sql)) {
                while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                    $rows[] = $row;
                    print("<br>Name: ".$rows[$i]['name']);
                    print("<br>Time: ".$rows[$i]['time']);
                    print("<br>Message: ".$rows[$i]['msg']);

                    printf("<form action=\"msg_update.php\"><input type=\"hidden\" name=\"sn\" value=\"".$rows[$i]['sn']."\">");
                    printf("<input type=\"hidden\" name=\"name\" value=\"".$rows[$i]['name']."\">");
                    printf("<input type=\"text\" name=\"new_msg\" placeholder=\"edit message here\" size=\"50\">");
                    printf("<input type=\"submit\" name=\"button\" value=\"Update\"></form>");

                    printf("<form action=\"msg_del.php\"><input type=\"hidden\" name=\"sn\" value=\"".$rows[$i]['sn']."\">");
                    printf("<input type=\"submit\" name=\"button\" value=\"Delete\"></form>");
                    print("------------------------------------------------------------------<br>");
                    $i++;
                }
            } else {
                die("Failed to get data " . $mysqli->error);
            }

            $page_count = 0;
            $left_data = 0;
            while ($left_data < $count) {
                $display = $page_count + 1;
                echo "<a href=\"?page=$page_count\">Page ". $display ."</a> &#8195;";
                $left_data = $left_data + $limit;
                $page_count++;
            }
            $result->close();
            /*
            for ($i = 0; $i < count($rows); $i++) {
                print("<br>Name: ".$rows[$i]['name']);
                print("<br>Time: ".$rows[$i]['time']);
                print("<br>Message: ".$rows[$i]['msg']);

                printf("<form action=\"msg_update.php\"><input type=\"hidden\" name=\"sn\" value=\"".$rows[$i]['sn']."\">");
                printf("<input type=\"hidden\" name=\"name\" value=\"".$rows[$i]['name']."\">");
                printf("<input type=\"text\" name=\"new_msg\" placeholder=\"edit message here\" size=\"50\">");
                printf("<input type=\"submit\" name=\"button\" value=\"Update\"></form>");

                printf("<form action=\"msg_del.php\"><input type=\"hidden\" name=\"sn\" value=\"".$rows[$i]['sn']."\">");
                printf("<input type=\"submit\" name=\"button\" value=\"Delete\"></form>");
                print("------------------------------------------------------------------<br>");
            }
            */
        }

    }
?>