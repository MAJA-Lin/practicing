<?php

require_once "bootstrap.php";

$sn = $_GET['sn'];
$query = $entityManager->find('Message', $sn);

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
