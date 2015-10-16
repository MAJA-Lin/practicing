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
            	$i = 0;
            	$sql= "SELECT * FROM message";
            	/*
            	*	OO
            	*/
            	if ($result = $mysqli->query($sql)) {
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

				    /* free result set */
				    $result->close();
				}

            	/*
            	*Original version of showing the message
            	*
            	*/
            	/*
                $result = mysqli_query($link, $sql);
                while(($row = @mysqli_fetch_array($result, MYSQL_ASSOC))) {
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
                */


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
        </div>
</body>
</html>
