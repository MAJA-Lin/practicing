<?php
    namespace board;

    include_once __DIR__ . "/autoload.php";

    use board\SqlConnection as connect;
    use board\MessageClass as msg;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Index</title>
</head>
<body>
    <div>
        <form action="msg_add.php" method="get">
            <br>
            <h2><strong>Leave new message<strong></h2>
            Message: <input type="text" name="msg" placeholder="commit here" size="50"/><br>
            Name: <input type="varchar" name="name" placeholder="Name" /><br>
            <input type="submit" name="button" value="submit" /><br>
        </form>
        <br>---------------------------------------------------------<br>
    </div>
    <div>
        <?php
            $index = new msg\Message();
            $index->printout();
        ?>
    </div>
</body>
</html>