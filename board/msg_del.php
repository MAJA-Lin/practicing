<?php
    include_once "connection.php";

    $sn = $_GET['sn'];
    $sql = "DELETE FROM message WHERE sn = '$sn'";

    if (($result = $mysqli->query($sql))) {
        echo ("<script>window.alert('Message has been deleted!')
                    location.href='index.php';</script>");
        $result->close();
        exit();
    } else {
        echo "Error ". mysqli_error($link);
    }
?>
