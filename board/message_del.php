<?php

require_once "bootstrap.php";

$id = $_GET['id'];
$table = $_GET['table'];

if ($table == "message") {
    $query = $entityManager->find('Message', $id);
} elseif ($table == "reply" && isset($_GET['id'])) {
    $query = $entityManager->find('ReplyMessage', $id);
}

if ($query === null) {
    echo ("<script>window.alert('Deletion failed.')
                location.href='message_show.php';</script>");
    exit(1);
} else {
    $entityManager->remove($query);
    $entityManager->flush();

    echo ("<script>window.alert('Message has been deleted!')
                location.href='message_show.php';</script>");
    exit();
}