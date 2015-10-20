<?php
    include_once "connection.php";

    $time = $_GET['time'];
    $sql = "DELETE FROM message WHERE time = '$time'";

    if (($result = $mysqli->query($sql))) {
        echo ("<script>window.alert('Message has been deleted!')
                    location.href='index.php';</script>");
        $result->close();
        exit();
    } else {
        echo "Error ". mysqli_error($link);
    }
?>
