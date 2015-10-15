<?php
    include_once "connection.php";

    $name = $_GET['name'];
    $msg = $_GET['msg'];
    $sql = "INSERT INTO message (name, msg, time) VALUES ('$name', '$msg', CURRENT_TIMESTAMP)";


    if(mysqli_query($link, $sql)){
                    echo ("<script>window.alert('Message has been added!')
                    location.href='index.php';</script>");
                    exit();
            }
    else {
            echo "Error ". mysqli_error($link);
    }


?>
