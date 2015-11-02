<?php

require_once "bootstrap.php";

$name = $_GET['name'];
$msg = $_GET['msg'];
$table = $_GET['table'];

if ($table == "message") {
    $insertQuery = new Message();
} elseif ($table == "reply" && isset($_GET['id'])) {
    $message = $entityManager->find('Message', $_GET['id']);
    $insertQuery = new ReplyMessage();
    $insertQuery->setMessage($message);
}

$insertQuery->setName($name);
$insertQuery->setMsg($msg);
$insertQuery->setTime();

$entityManager->persist($insertQuery);
$entityManager->flush();

echo ("<script>window.alert('Message has been added!')
            location.href='message_show.php';</script>");
exit();