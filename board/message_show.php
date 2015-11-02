<?php
require_once "bootstrap.php";
require_once "message_show.php";
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
            Name: <input type="varchar" name="name" placeholder="User Name" /><br>
            <input type="submit" name="button" value="submit" /><br>
        </form>
        <br>---------------------------------------------------------<br>
    </div>
    <div>
        <?php
        $i = 0;
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
            listMessage($value, $value['sn'], 'message');
            //listReplyMessage($entityManager, $value);
        }

        listPages($total, $pageLimit);
        ?>
    </div>
</body>
</html>

<?php

function listMessage($row, $sn, $table)
{
    print("<br>Name: ".$row['name']);
    print("<br>Time: ".$row['time']);
    print("<br>Message: ".$row['msg']);

    printf("<form action=\"msg_update.php\"><input type=\"hidden\" 
            name=\"sn\" value=\"".$sn."\">");
    printf("<input type=\"text\" name=\"new_msg\" placeholder=\"
            edit message here\" size=\"50\">");
    printf("<input type=\"hidden\" name=\"table\" value=\"".$table."\">");
    printf("<input type=\"submit\" name=\"button\" 
            value=\"Update\"></form>");

    printf("<form action=\"msg_del.php\"><input type=\"hidden\" name=\"sn\" 
            value=\"".$sn."\">");
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
        echo "<a href=\"?page=$page_count\">Page ". $display ."</a> &#8195;";
        $left_data = $left_data + $pageLimit;
        $page_count++;
    }
}

function listReplyMessage($entityManager, $parentQuery)
{
    #do sql/dql query to find key in reply_message, then show those related messages.
    $target = $parentQuery['sn'];
    //SELECT reply_message.reply_sn, reply_message.name, reply_message.time, 
        //reply_message.msg FROM reply_message INNER JOIN message 
        //ON message.sn = reply_message.target;
    /*$dql = "SELECT r.name, r.time, r.msg, r.reply FROM ReplyMessage r JOIN r.message m " .
    "WHERE m.sn = '21'";
    $query = $entityManager->createQuery($dql)->setParameter(1, $target)->getScalarResult();
    */

    $message = $entityManager->find('Message', $target);

    //$reply = $entityManager->find('ReplyMessage',);
    //$reply->getMessageTable()->add($message);
    /*
    if ($query === null) {
        addForm();
    } else {
        printf("<details><summary>Click to see reply</summary>");

        foreach ($query as $value) {
            listMessage($value, $value['sn'], 'reply');
        }

        $this->addForm('reply');
        printf("</details>");
    }
    */
}
/*
#Leave this to msg_update.php
public function updateMessage($entityManager, $sn, $newMsg)
{
    $message = $entityManager->find('Message', $sn);

    if ($message === null) {
        echo "Can't find message.\n";
        exit(1);
    } else {
        $message->setMsg($newMsg);
        $entityManager->flush();
        echo ("<script>window.alert('Message has been updated!')
        location.href='index.php';</script>");
    }
}
*/

function addForm($table)
{
    printf("<form action=\"msg_add.php\" method=\"get\">");
    printf("<br><h3><strong>Reply this post<strong></h3>");
    printf("Message: <input type=\"text\" name=\"msg\" 
        placeholder=\"reply here\" size=\"50\"/><br>");
    printf("Name: <input type=\"varchar\" name=\"name\" 
        placeholder=\"User Name\" /><br>");
    printf("<input type=\"hidden\" name=\"table\" value=\"".$table."\">");
    printf("<input type=\"submit\" name=\"button\" 
        value=\"submit\" /><br></form>");
}


?>
