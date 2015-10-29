<?php

require_once "bootstrap.php";

$name = $_GET['name'];
$msg = $_GET['msg'];

$insertQuery = new Message();
$insertQuery->setName($name);
$insertQuery->setMsg($msg);
$insertQuery->setTime();

$entityManager->persist($insertQuery);
$entityManager->flush();

echo ("<script>window.alert('Message has been added!')
            location.href='index.php';</script>");
exit();

?>
