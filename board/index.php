<?php
    include_once("connection.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Index</title>
</head>
<body>
    <div>
        <form action="msg_search.php" method="get">
            <br>
            <h2><strong>Search the specific message<strong></h2>
            keyword: <input type="text" name="search" placeholder="search" size="30"/><br>
            <input type="submit" name="button" value="submit" /><br>
        </form>
        <br>---------------------------------------------------------<br>
        <form action="msg_add.php" method="get">
            <br>
            <h2><strong>Leave new message<strong></h2>
            Message: <input type="text" name="msg" placeholder="commit here" size="50"/><br>
            Name: <input type="varchar" name="name" placeholder="Name" /><br>
            <input type="submit" name="button" value="submit" /><br>
        </form>
        <br>---------------------------------------------------------<br>
    </div>
    <div>
        <?php
            $i = 0;
            $limit = 10;
            /* Get total number of records */
            $sql= "SELECT count(sn) FROM message";
            $result = $mysqli->query($sql);
            if (!$result) {
                die('Failed to get data: ' . $mysqli->error;
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

            if ($page > 1 && !($left_data < $limit)) {
                $last = $page - 2;
                echo "<a href=\"?page=$last\">Last page</a> |";
                echo "<a href=\"?page=$page\">Next page</a>";
            } elseif ($page == 1) {
                echo "<a href=\"?page=$page\">Next page</a>";
            } elseif ($left_data < $limit) {
                $last = $page - 2;
                echo "<a href=\"?page=$last\">Last page</a>";
            }
            $result->close();
        ?>
    </div>
</body>
</html>