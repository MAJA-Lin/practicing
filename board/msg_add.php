<?php
include_once "classes/SqlConnection.php";

$conneting = new SqlConnection;
$mysqli = $conneting->dbConnection();
$name = $_GET['name'];
$msg = $_GET['msg'];
$sql = "INSERT INTO message (name, msg, time) VALUES (?,?,?)";

$result = $mysqli->prepare($sql);
$time = date("Y-m-d H:i:s");
$result->bind_param("sss", $name, $msg, $time);

if ($result->execute()) {
    echo ("<script>window.alert('Message has been updated!')
                location.href='index.php';</script>");
    $mysqli->close();
    exit();
} else {
    echo "Error ". $result->error;
}

?>
