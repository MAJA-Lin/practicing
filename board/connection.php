<?php

    $db_server = "localhost";
    //or ip address
    $db_name = "message_board";
    //$db_user = "tester";
    //$db_pw = "test5566";

    $db_user = "root";
    $db_pw = "working2015";

    $link = mysqli_connect($db_server, $db_user, $db_pw, $db_name) or die("Error, connection failed. " . mysqli_error($link));
    mysqli_set_charset($link,"utf8");



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
