<?php
    include_once "connection.php";

    $name = $_GET['name'];
    $sql = "DELETE FROM message WHERE `name` = '$name'";


    if(mysqli_query($link, $sql)){
                    echo ("<script>window.alert('Message has been deleted!')
                    location.href='index.php';</script>");
                    exit();
            }
    else {
            echo "Error ". mysqli_error($link);
    }


?>
