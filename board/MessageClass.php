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
            $this->listMessage($value, $value['sn']);
            //$this->listReplyMessage($value);
        }

        $this->listPages($total, $pageLimit);

    }

    public function listMessage($row, $sn)
    {
        print("<br>Name: ".$row['name']);
        print("<br>Time: ".$row['time']);
        print("<br>Message: ".$row['msg']);

        printf("<form action=\"msg_update.php\"><input type=\"hidden\" 
                name=\"sn\" value=\"".$sn."\">");
        printf("<input type=\"hidden\" name=\"name\" value=\""
                .$row['name']."\">");
        printf("<input type=\"text\" name=\"new_msg\" placeholder=\"
                edit message here\" size=\"50\">");
        printf("<input type=\"submit\" name=\"button\" 
                value=\"Update\"></form>");

        printf("<form action=\"msg_del.php\"><input type=\"hidden\" name=\"sn\" 
                value=\"".$sn."\">");
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
    /*
    public function listReplyMessage($parentQuery)
    {
        #do sql/dql query to find key in reply_message, then show those related messages.
        $target = $parent['sn'];
        //SELECT reply_message.reply_sn, reply_message.name, reply_message.time, 
            //reply_message.msg FROM reply_message INNER JOIN message 
            //ON message.sn = reply_message.target;
        $dql = "SELECT r FROM ReplyMessage r JOIN Message m ON m.sn = r.target";
        $query = $em->createQuery($dql)->getScalarResult();

        if ($query === null) {

        }

        if (#if there is no repl, just show user the adding reply block) {
            # code...
        } else {
            printf("<details><summary>Click to see reply</summary>");
            foreach () {
                #maybe use listMessage again?
            }
        }


    }*/

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

}

?>
