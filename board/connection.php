<?php
    $db_server = "localhost";
    $db_name = "message_board";
    $db_user = "tester";
    $db_pw = "test5566";

    $mysqli = new mysqli($db_server, $db_user, $db_pw, $db_name);
    /* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}



?>
