<?php
    include_once "connection.php";

    $name = $_GET['name'];
    $new_msg = $_GET['new_msg'];
    $time = $_GET['time'];


    //Prevent SQL injection
    $sql = "UPDATE message SET msg = ? WHERE time = ?";
    $result = $mysqli->prepare($sql);

    $result->bind_param("ss", $var1, $var2);

    $var1 = $new_msg;
    $var2 = $time;

    //$result->execute();
    if ($result->execute()) {
    	echo ("<script>window.alert('Message has been updated!')
                    location.href='index.php';</script>");
    	$result->close();
    	exit();
    }
    else {
    	echo "Error ". $result->error();
    }

?>
