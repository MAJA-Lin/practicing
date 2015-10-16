<?php
    include_once "connection.php";

    //$name = $_GET['name'];
    $time = $_GET['time'];
    $sql = "DELETE FROM message WHERE time = '$time'";

    if ($mysqli->query($sql)){
        echo ("<script>window.alert('Message has been deleted!')
                    location.href='index.php';</script>");
        /* free result set */
        $mysqli->close();
        exit();
    }
    else {
            echo "Error ". mysqli_error($link);
    }


?>
