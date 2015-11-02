<?php

require_once "bootstrap.php";

$sn = $_GET['sn'];
$table = $_GET['table'];

if ($table == "message") {
    $query = $entityManager->find('Message', $sn);
} elseif ($table == "reply" && isset($_GET['sn'])) {
    $query = $entityManager->find('ReplyMessage', $sn)
}


if ($query === null) {
    echo ("<script>window.alert('Deletion failed.')
                location.href='index.php';</script>");
    exit(1);
} else {
    $entityManager->remove($query);
    $entityManager->flush();

    echo ("<script>window.alert('Message has been deleted!')
                location.href='index.php';</script>");
    exit();
}

?>
