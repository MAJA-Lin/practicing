<?php

require_once "bootstrap.php";

class MessageClass
{

    public function printout($em)
    {
        $i = 0;
        $pageLimit = 10;

        //$dql = "SELECT count(m.sn) FROM Message m";

        //$query = $em->createQuery($dql)->getResult();
        $total = $em->getRepository('Message')->getTotalNumber();

        if (isset($_GET{'page'})) {
            $page = $_GET{'page'} + 1;
            $offset = $pageLimit * ($page - 1);
        } else {
            $page = 1;
            $offset = 0;
        }

        $left_data = $total - (($page - 1) * $pageLimit);
        $query = $em->getRepository('Message')->getPages($offset, $pageLimit);

        foreach ($query as $value) {
            $this->listMessage($value, $value['sn'], 'message');
            $this->listReplyMessage($em, $value);
        }

        $this->listPages($total, $pageLimit);

    }

    public function listMessage($row, $sn, $table)
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

    public function listPages($total, $pageLimit)
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

    public function listReplyMessage($em, $parentQuery)
    {
        #do sql/dql query to find key in reply_message, then show those related messages.
        $target = $parentQuery['sn'];
        //SELECT reply_message.reply_sn, reply_message.name, reply_message.time, 
            //reply_message.msg FROM reply_message INNER JOIN message 
            //ON message.sn = reply_message.target;
        $dql = "SELECT r.name, r.time, r.msg, r.reply FROM ReplyMessage r JOIN r.message m " .
        "WHERE m.sn = '21'";
        $query = $em->createQuery($dql)->setParameter(1, $target)->getScalarResult();


        if ($query === null) {
            $this->addForm();
        } else {
            printf("<details><summary>Click to see reply</summary>");

            foreach ($query as $value) {
                $this->listMessage($value, $value['sn'], 'reply');
            }

            $this->addForm('reply');
            printf("</details>");
        }
    }
    /*
    #Leave this to msg_update.php
    public function updateMessage($em, $sn, $newMsg)
    {
        $message = $em->find('Message', $sn);

        if ($message === null) {
            echo "Can't find message.\n";
            exit(1);
        } else {
            $message->setMsg($newMsg);
            $em->flush();
            echo ("<script>window.alert('Message has been updated!')
            location.href='index.php';</script>");
        }
    }
    */

    public function addForm($table)
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
}

?>
