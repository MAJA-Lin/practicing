<?php
    include_once "connection.php";

    $search = $_GET['search'];
    $i = 0;
    $sql = "SELECT * FROM message WHERE msg LIKE N'%$search%'";
    if (($result = $mysqli->query($sql))) {
        while ($row = $result->fetch_array(MYSQL_ASSOC)) {
            $rows[] = $row;
                printf("<br>Name: ".$rows[$i]['name']);
                printf("<br>Time: ".$rows[$i]['time']);
                printf("<br>Message: ".$rows[$i]['msg']);

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
        die('Failed to get data: ' . $mysqli->error);
    }
    print("<a href=\"index.php\"><h3>Back to index<h3></a>");
?>
