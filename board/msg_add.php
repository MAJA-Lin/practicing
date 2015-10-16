<?php
    include_once "connection.php";

    $name = $_GET['name'];
    $msg = $_GET['msg'];
    $sql = "INSERT INTO message (name, msg, time) VALUES (?,?,?)";

    $result = $mysqli->prepare($sql);
    $time = date("Y-m-d H:i:s");
    $result->bind_param("sss", $name, $msg, $time);

    $result->execute();
    $result->close();

    /*
    if(mysqli_query($link, $sql)){
                    echo ("<script>window.alert('Message has been added!')
                    location.href='index.php';</script>");
                    exit();
            }
    else {
            echo "Error ". mysqli_error($link);
    }
    */

    echo ("<script>window.alert('Message has been added!')
                    location.href='index.php';</script>");
                    exit();


?>
