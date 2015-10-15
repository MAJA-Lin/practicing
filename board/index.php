<?php
        include_once("connection.php");
        include_once("msg_func.php");	//Big trouble! Can't use sub function to do SQL query.

?>
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <title>Index</title>
</head>
<body>
        <div>
                <?php

                    $sql= "SELECT * FROM message";

                    $result = mysqli_query($link, $sql);
                    $i = 0;
                    while(($row = @mysqli_fetch_array($result, MYSQL_ASSOC))) {
                        $rows[] = $row;

                        print("Message: ".$rows[$i]['msg']);
                        print("<br>Name: ".$rows[$i]['name']);
                        print("<br>Time: ".$rows[$i]['time']);
                        print("<br>------------------------------------------------------------------<br>");
                        $i++;
                    }
                    //var_dump($rows);

                ?>
        </div>
        <div>
                <form action="msg_add.php" method="get">
                    <br>
                    <h2><strong>Leave new message<strong></h2>
                    Message: <input type="text" name="msg" placeholder="commit here" size="50"/><br>
                    Name: <input type="varchar" name="name" placeholder="Name" /><br>
                    <input type="submit" name="button" value="submit" /><br>
                </form>
                <br>---------------------------------------------------------<br>
                <form action="msg_del.php" method="get">
                	<br>
                	<h2><strong>Delete the message</strong></h2>
                	Name: <input type="varchar" name="name" placeholder="Name" /><br>
                	<input type="submit" name="button" value="submit" /><br>
                </form>
        </div>
</body>
</html>
