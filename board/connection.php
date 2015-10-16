<?php

	/*
	*	Defination of server, name, user and pw;
	*/
    $db_server = "localhost";
    $db_name = "message_board";
    $db_user = "tester";
    $db_pw = "test5566";
    /*
    *   Don't use root account to access MySQL
    */
    /*
    $db_user = "root";
    $db_pw = "working2015";
    */

    /*
    *	OO style - To prevent SQL injection
    *		Sample on the php.net
    *			http://php.net/manual/en/mysqli-stmt.execute.php
    */
    $mysqli = new mysqli($db_server, $db_user, $db_pw, $db_name);
    /* check connection */
	if (mysqli_connect_errno()) {
	    printf("Connect failed: %s\n", mysqli_connect_error());
	    exit();
	}

    /*
        *   Use PDO to access db
        *
        *

        try {
            $dbh = new PDO('mysql:host=localhost;dbname=message_board', 'root', 'working2015');
            foreach($dbh->query('SELECT * from message') as $row) {
                print_r($row);
            }
            //$dbh = null;
        }
        catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
        */


?>
