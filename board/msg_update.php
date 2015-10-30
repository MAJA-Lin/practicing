<?php
require_once "bootstrap.php";

$new_msg = $_GET['new_msg'];
$sn = $_GET['sn'];
$table = $_GET['table'];

if ($table == "message") {
    $query = $entityManager->find('Message', $sn);
} elseif ($table == "reply" && isset($_GET['sn'])) {
    $query = $entityManager->find('ReplyMessage', $sn)
}

if ($query === null) {
    echo ("<script>window.alert('Update failed.')
                location.href='index.php';</script>");
    exit(1);
} else {
    $query->setMsg($new_msg);

    $entityManager->persist($query);
    $entityManager->flush();

    echo ("<script>window.alert('Message has been updated!')
                location.href='index.php';</script>");
    exit();
}

?>
