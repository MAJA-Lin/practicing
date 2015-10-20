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
            $limit = 5;
            /* Get total number of records */
            $sql= "SELECT count(time) FROM message";
            $result = $mysqli->query($sql);
            if (! $result){
                die('Failed to get data: ' . $mysqli->error());
            }
            $row = $result->fetch_array(MYSQL_NUM);
            $count = $row[0];

            /*
            *   gage => current pages, use get to pass
            *   limit => numbers of data that one page displayed
            *   offset => kind of index, mainly record the index of current data;
            *       e.g. SELECT * FROM tbl LIMIT 5,10;  # Retrieve rows 6-15
            *       offset is just like 5
            */
            if( isset($_GET{'page'})){
                $page = $_GET{'page'} + 1;
                $offset = $limit * $page ;
            }
             else{
                $page = 0;
                $offset = 0;
            }

            /*  left_data => count how many data left */
            $left_data = $count - ($page * $limit);
            $sql = "SELECT name, time, msg FROM message LIMIT $offset, $limit ";

            if ($result = $mysqli->query($sql)){
                while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                    $rows[] = $row;
                    print("<br>Name: ".$rows[$i]['name']);
                    print("<br>Time: ".$rows[$i]['time']);
                    print("<br>Message: ".$rows[$i]['msg']);
                    //Use hidden form again to perform updating
                    printf("<form action=\"msg_update.php\"><input type=\"hidden\" name=\"time\" value=\"".$rows[$i]['time']."\">");
                    printf("<input type=\"hidden\" name=\"name\" value=\"".$rows[$i]['name']."\">");
                    printf("<input type=\"text\" name=\"new_msg\" placeholder=\"edit message here\" size=\"50\">");
                    printf("<input type=\"submit\" name=\"button\" value=\"Update\"></form>");

                    //Use hidden form to perform deleting
                    printf("<form action=\"msg_del.php\"><input type=\"hidden\" name=\"time\" value=\"".$rows[$i]['time']."\">");
                    printf("<input type=\"submit\" name=\"button\" value=\"Delete\"></form>");
                    print("------------------------------------------------------------------<br>");
                    $i++;
                }
            }
            else {
                die("Failed to get data " . $mysqli->error);
            }

            if($page > 0){
                $last = $page - 2;
                echo "<a href=\"?page=$last\">Last 5 Records</a> |";
                echo "<a href=\"?page=$page\">Next 5 Records</a>";
            }

            else if( $page == 0 ){
                echo "<a href=\"?page=$page\">Next 5 Records</a>";
            }

             else if( $left_data < $limit ){
                $last = $page - 2;
                echo "<a href=\"?page=$last\">Last 5 Records</a>";
            }

            $mysqli->close();
        ?>
    </div>

</body>
</html>
