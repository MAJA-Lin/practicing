<?php
// src/AppBundle/Controller/LuckyController.php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity as Entity;

class MessageController extends Controller
{
    /**
     * @Route("/message/show")
     */
    public function showAction()
    {
        $pageLimit = 10;
        $em =$this->getDoctrine()->getManager();
        $total = $em->getRepository('AppBundle:Message')->getTotalNumber();

        if (isset($_GET{'page'})) {
            $page = $_GET{'page'} + 1;
            $offset = $pageLimit * ($page - 1);
        } else {
            $page = 1;
            $offset = 0;
        }

        $left_data = $total - (($page - 1) * $pageLimit);
        $query = $em->getRepository('AppBundle:Message')->getPages($offset, $pageLimit);
        /*
        foreach ($query as $value) {
            $reply = $em->getRepository('AppBundle:ReplyMessage')
            ->findBy(array('message' => $value['id']));
        }

        //listPages($total, $pageLimit);
        $replyExists = isset($reply);
        */

        return $this->render(
            'message/show.html.twig',
            array('message' => $query, 'reply' => $pageLimit)//, 'replyExists' => $replyExists)
        );
    }

    /**
     * @Route("/message/add")
     */
    public function addAction()
    {
        $name = $_GET['name'];
        $msg = $_GET['msg'];
        $table = $_GET['table'];
        $entityManager = $this->getDoctrine()->getManager();
        if ($table == "message") {
            $insertQuery = new Entity\Message();
        } elseif ($table == "reply" && isset($_GET['id'])) {
            $message = $entityManager->find('AppBundle:Message', $_GET['id']);
            $insertQuery = new Entity\ReplyMessage();
            $insertQuery->setMessage($message);
        }

        $insertQuery->setName($name);
        $insertQuery->setMsg($msg);
        $insertQuery->setTime();

        $entityManager->persist($insertQuery);
        $entityManager->flush();

        return $this->render(
            'message/add.html.twig',
            array('message' => $insertQuery)
        );
    }

    /**
     * @Route("/message/delete")
     */
    public function deleteAction()
    {
        $id = $_GET['id'];
        //$table = $_GET['table'];
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->find('AppBundle:Message', $id);
        /*
        if ($table == "message") {
            $query = $entityManager->find('Message', $id);
        } elseif ($table == "reply" && isset($_GET['id'])) {
            $query = $entityManager->find('ReplyMessage', $id);
        }
        */

        $entityManager->remove($query);
        $entityManager->flush();

        return $this->render(
            'message/delete.html.twig',
            array('message' => $query)
        );

    }

    /**
     * @Route("/message/update")
     */
    public function updateAction()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $id = $_GET['id'];
        $msg = $_GET['new_msg'];
        //$table = $_GET['table'];

        /*
        if ($table == "message") {
            $query = $entityManager->find('Message', $id);
        } elseif ($table == "reply" && isset($_GET['id'])) {
            $query = $entityManager->find('ReplyMessage', $id);
        }

        if ($query === null) {
            echo ("<script>window.alert('Update failed.')
                        location.href='message_show.php';</script>");
            exit(1);
        } else {
            $query->setMsg($new_msg);
            $entityManager->persist($query);
            $entityManager->flush();
        */

        $query = $entityManager->find('AppBundle:Message', $id);
        $query->setMsg($msg);

        $entityManager->persist($query);
        $entityManager->flush();

        return $this->render(
            'message/update.html.twig',
            array('message' => $query)
        );
    }

    private function listMessage($row)
    {
        $em =$this->getDoctrine()->getManager();
        $reply = $em->getRepository('AppBundle:ReplyMessage')
            ->findBy(array('message' => $row['id']));

        $replyExists = isset($reply);

        return $this->render(
            'lucky/number.html.twig',
            array('luckyNumberList' => '100')
        );
    }

    private function listPages($total, $pageLimit)
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

    private function listReply($parentQuery)
    {
        $target = $parentQuery['id'];
        $em =$this->getDoctrine()->getManager();

        $reply = $em->getRepository('ReplyMessage')
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
}