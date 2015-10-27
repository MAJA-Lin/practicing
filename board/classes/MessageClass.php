<?php

require_once "../bootstrap.php";

use scott\board\classes as board;

class MessageClass implements board\FrontOutput
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
                $this->listMessage($row);
            }
        } else {
            die("Failed to get data " . $mysqli->error);
        }

        $this->listPages($count, $limit);
        $mysqli->close();

    }

    public function listMessage($row)
    {
        print("<br>Name: ".$row['name']);
        print("<br>Time: ".$row['time']);
        print("<br>Message: ".$row['msg']);

        printf("<form action=\"msg_update.php\"><input type=\"hidden\" 
                name=\"sn\" value=\"".$row['sn']."\">");
        printf("<input type=\"hidden\" name=\"name\" value=\""
                .$row['name']."\">");
        printf("<input type=\"text\" name=\"new_msg\" placeholder=\"
                edit message here\" size=\"50\">");
        printf("<input type=\"submit\" name=\"button\" 
                value=\"Update\"></form>");

        printf("<form action=\"msg_del.php\"><input type=\"hidden\" name=\"sn\" 
                value=\"".$row['sn']."\">");
        printf("<input type=\"submit\" name=\"button\" value=\"Delete\"></form>");
        print("------------------------------------------------------------------<br>");
    }

    public function listPages($count, $limit)
    {
        $page_count = 0;
        $left_data = 0;
        while ($left_data < $count) {
            $display = $page_count + 1;
            echo "<a href=\"?page=$page_count\">Page ". $display ."</a> &#8195;";
            $left_data = $left_data + $limit;
            $page_count++;
        }
    }
}

?>
