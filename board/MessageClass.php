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

        echo "<br>";
        echo $total;

        if (isset($_GET{'page'})) {
            $page = $_GET{'page'} + 1;
            $offset = $pageLimit * ($page - 1);
        } else {
            $page = 1;
            $offset = 0;
        }

        $left_data = $total - (($page - 1) * $pageLimit);
        $query = $em->getRepository('Message')->getPages($offset, $pageLimit);

        echo "<br><br>";
        var_dump($query);
        echo "<br>";
        //echo get_class($query[0]->getAvatar());

        $this->listPages($total, $pageLimit);

    }

    public function listMessage($row)
    {
        print("<br>Name: ".$row['name']);
        print("<br>Time: ".$row['time']);
        print("<br>Message: ".$row['msg']);

        printf("<form action=\"msg_update.php\"><input type=\"hidden\" 
                name=\"sn\" value=\"".$row['sn']."\">");
        printf("<input type=\"hidden\" name=\"name\" value=\""
                .$row['name']."\">");
        printf("<input type=\"text\" name=\"new_msg\" placeholder=\"
                edit message here\" size=\"50\">");
        printf("<input type=\"submit\" name=\"button\" 
                value=\"Update\"></form>");

        printf("<form action=\"msg_del.php\"><input type=\"hidden\" name=\"sn\" 
                value=\"".$row['sn']."\">");
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
