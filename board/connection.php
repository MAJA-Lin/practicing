<?php


	/*
	*	Defination of server, name, user and pw;
	*		Change the user of database in the future.
	*/
    $db_server = "localhost";
    //or ip address
    $db_name = "message_board";
    //$db_user = "tester";
    //$db_pw = "test5566";

    $db_user = "root";
    $db_pw = "working2015";


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
	*	Original version of connection to the database
	*		Due to the difficult preventation of sql injection,
	*			leave it.
	*/
	/*
    $link = mysqli_connect($db_server, $db_user, $db_pw, $db_name) or die("Error, connection failed. " . mysqli_error($link));
    mysqli_set_charset($link,"utf8");
	*/


    /*
    $sql= "SELECT * FROM message";

    $result = mysqli_query($link, $sql);

    while(($row = @mysqli_fetch_array($result))) {
            $rows[] = $row;
    }
    var_dump($rows);
    */


    /*

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
