<?php
    include_once "connection.php";

    $name = $_GET['name'];
    $new_msg = $_GET['new_msg'];
    $sn = $_GET['sn'];

    $sql = "UPDATE message SET msg = ? WHERE sn = ?";
    $result = $mysqli->prepare($sql);
    $result->bind_param("ss", $new_msg, $sn);

    if ($result->execute()) {
    	echo ("<script>window.alert('Message has been updated!')
                    location.href='index.php';</script>");
    	$mysqli->close();
    	exit();
    } else {
    	echo "Error ". $result->error();
    }

?>
