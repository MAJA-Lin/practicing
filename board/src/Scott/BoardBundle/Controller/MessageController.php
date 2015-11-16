<?php

namespace Scott\BoardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Scott\BoardBundle\Entity as Entity;

class MessageController extends Controller
{
    /**
     * @Route("/message/show/{page}",
     *      name="show",
     *      defaults={"page": 1},
     *      requirements={"page": "\d+"})
     * @Method("GET")
     */
    public function showAction($page)
    {
        $pageLimit = 10;
        $em =$this->getDoctrine()->getManager();

        $total = $em->getRepository('ScottBoardBundle:Message')
            ->getTotalNumber();

        if ($page == 1){
            $offset = 0;
        } else {
            $offset = $pageLimit * ($page - 1);
        }

        $query = $em->getRepository('ScottBoardBundle:Message')
            ->getPages($offset, $pageLimit);

        foreach ($query as $key=>$value) {
            $reply[$key] = $em
                ->getRepository('ScottBoardBundle:ReplyMessage')
                ->findBy(['message' => $value['id']]);

            $replyExists[$key] = empty($reply[$key]);
        }

        $totalPage = floor($total / $pageLimit);
        if (($total % $pageLimit) > 0) {
            $totalPage++;
        }

        return $this->render(
            'message/show.html.twig',
            [
                'message' => $query,
                'reply' => $reply,
                'replyExists' => $replyExists,
                'table' => 'message',
                'page' => $page,
                'totalPage' => $totalPage
            ]
        );
    }

    /**
     * @Route("/message/add")
     * @Method("GET")
     */
    public function addAction()
    {
        $name = $_GET['name'];
        $msg = $_GET['msg'];
        $table = $_GET['table'];
        $entityManager = $this->getDoctrine()->getManager();
        if ($table == "message") {
            $insertQuery = new Entity\Message();
        } elseif ($table == "reply") {
            $message = $entityManager->find(
                'ScottBoardBundle:Message',
                $_GET['id']
            );

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
            ['message' => $insertQuery]
        );
    }

    /**
     * @Route("/message/delete")
     * @Method("GET")
     */
    public function deleteAction()
    {
        $id = $_GET['id'];
        $table = $_GET['table'];
        $entityManager = $this->getDoctrine()->getManager();

        $query = $entityManager->find('ScottBoardBundle:Message', $id);

        if ($table == "message") {
            $query = $entityManager->find('ScottBoardBundle:Message', $id);
        } elseif ($table == "reply" && isset($_GET['id'])) {
            $query = $entityManager->find('ScottBoardBundle:ReplyMessage', $id);
        }

        $entityManager->remove($query);
        $entityManager->flush();

        return $this->render(
            'message/delete.html.twig',
            ['message' => $query]
        );

    }

    /**
     * @Route("/message/update")
     * @Method("GET")
     */
    public function updateAction()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $id = $_GET['id'];
        $msg = $_GET['msg'];
        $table = $_GET['table'];

        if ($table == "message") {
            $query = $entityManager->find('ScottBoardBundle:Message', $id);
        } elseif ($table == "reply" && isset($_GET['id'])) {
            $query = $entityManager->find('ScottBoardBundle:ReplyMessage', $id);
        }

        if ($query === null) {
            exit(1);
        } else {
            $query->setMsg($msg);
            $entityManager->persist($query);
            $entityManager->flush();
        }

        return $this->render(
            'message/update.html.twig',
            ['message' => $query]
        );
    }
}