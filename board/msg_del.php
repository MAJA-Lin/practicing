<?php
include_once "classes/SqlConnection.php";

$conneting = new SqlConnection;
$mysqli = $conneting->dbConnection();
$sn = $_GET['sn'];
$sql = "DELETE FROM message WHERE sn = '$sn'";

if (($result = $mysqli->query($sql))) {
    echo ("<script>window.alert('Message has been deleted!')
                location.href='index.php';</script>");
    $mysqli->close();
    exit();
} else {
    echo "Error ". $mysqli->error;
}
?>
