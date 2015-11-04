<?php
require_once "bootstrap.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Index</title>
</head>
<body>
    <div>
        <form action="message_add.php" method="get">
            <br>
            <h2><strong>Leave new message<strong></h2>
            Message: <input type="text" name="msg" placeholder="commit here" size="50"/><br>
            Name: <input type="text" name="name" placeholder="User Name" /><br>
            <input type="hidden" name="table" value="message" />
            <input type="submit" name="button" value="submit" /><br>
        </form>
        <br>---------------------------------------------------------<br>
    </div>
    <div>
        <?php
        $pageLimit = 10;

        $total = $entityManager->getRepository('Message')->getTotalNumber();

        if (isset($_GET{'page'})) {
            $page = $_GET{'page'} + 1;
            $offset = $pageLimit * ($page - 1);
        } else {
            $page = 1;
            $offset = 0;
        }

        $left_data = $total - (($page - 1) * $pageLimit);
        $query = $entityManager->getRepository('Message')->getPages($offset, $pageLimit);

        foreach ($query as $value) {
            listMessage($value, $value['id'], 'message');
            listReplyMessage($entityManager, $value);
        }

        listPages($total, $pageLimit);
        ?>
    </div>
</body>
</html>

<?php

function listMessage($row, $id, $table)
{
    $name = $row['name'];
    $msg = $row['msg'];

    if ($table == 'message') {
        $time = $row['time'];
    } elseif ($table == 'reply') {
        $time = $row['time']->format('Y-m-d H:i:s');
    }

    print("<br>Name: ".$name);
    print("<br>Time: ".$time);
    print("<br>Message: ".$msg);

    printf("<form action=\"message_update.php\"><input type=\"hidden\" 
            name=\"id\" value=\"".$id."\">");
    printf("<input type=\"text\" name=\"new_msg\" placeholder=\"
            edit message here\" size=\"50\">");
    printf("<input type=\"hidden\" name=\"table\" value=\"".$table."\">");
    printf("<input type=\"submit\" name=\"button\" 
            value=\"Update\"></form>");

    printf("<form action=\"message_del.php\"><input type=\"hidden\" name=\"id\" 
            value=\"".$id."\">");
    printf("<input type=\"hidden\" name=\"table\" value=\"".$table."\">");
    printf("<input type=\"submit\" name=\"button\" value=\"Delete\"></form>");
    print("------------------------------------------------------------------<br>");
}

function listPages($total, $pageLimit)
{
    $page_count = 0;
    $left_data = 0;
    echo "<br>";
    while ($left_data < $total) {
        $display = $page_count + 1;
        echo "<a href=\"?page=$page_count\">Page ". $display ."</a>  ";
        $left_data = $left_data + $pageLimit;
        $page_count++;
    }
}

function listReplyMessage($entityManager, $parentQuery)
{
    $target = $parentQuery['id'];

    $reply = $entityManager->getRepository('ReplyMessage')
        ->findBy(array('message' => $target));

    if ($reply === null) {
        printf("<details><summary>Click to reply this message.</summary>");
        addForm($target, "reply");
        printf("</details>");
    } else {
        printf("<details><summary>Click to see reply</summary>");

        foreach ($reply as $value) {
            $id = $value->getId();
            $arr['name'] = $value->getName();
            $arr['msg'] = $value->getMsg();
            $arr['time'] = $value->getTime();
            listMessage($arr, $id, 'reply');
        }

        addForm($target, 'reply');
        printf("</details>");
    }
}

function addForm($id, $table)
{
    printf("<form action=\"message_add.php\" method=\"get\">");
    printf("<br><h3><strong>Reply this post<strong></h3>");
    printf("Message: <input type=\"text\" name=\"msg\" 
        placeholder=\"reply here\" size=\"50\"/><br>");
    printf("Name: <input type=\"text\" name=\"name\" 
        placeholder=\"User Name\" /><br>");
    printf("<input type=\"hidden\" name=\"table\" value=\"".$table."\">");
    printf("<input type=\"hidden\" name=\"id\" value=\"".$id."\">");
    printf("<input type=\"submit\" name=\"button\" 
        value=\"submit\" /><br></form>");
}

?>