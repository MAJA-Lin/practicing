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
        $request = Request::createFromGlobals();
        $request->query->set('page', $page);

        $pageLimit = 10;

        $em =$this->getDoctrine()->getManager('board');
        $total = $em->getRepository('ScottBoardBundle:Message')
            ->getTotalNumber();

        if ($page == 1){
            $offset = 0;
        } else {
            $offset = $pageLimit * ($page - 1);
        }

        $query = $em->getRepository('ScottBoardBundle:Message')
            ->getPages($offset, $pageLimit);

        if (empty($query)) {
            return $this->render(
                'ScottBoardBundle:message:error_page.html.twig',
                ['page' => $page]
            );
        } else {
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
                'ScottBoardBundle:message:show.html.twig',
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
    }

    /**
     * @Route("/message/add", name="add")
     * @Method("POST")
     */
    public function addAction(Request $request)
    {
        $name = $request->request->get('name');
        $msg = $request->request->get('msg');
        $id = $request->request->get('id');

        $entityManager = $this->getDoctrine()->getManager('board');

        $insertQuery = new Entity\Message();
        $insertQuery->setName($name);
        $insertQuery->setMsg($msg);
        $insertQuery->setTime();

        $entityManager->persist($insertQuery);
        $entityManager->flush();

        return $this->render(
            'ScottBoardBundle:message:add.html.twig',
            ['message' => $insertQuery]
        );
    }

    /**
     * @Route("/message/reply/add", name="reply_add")
     * @Method("POST")
     */
    public function replyAddAction(Request $request)
    {
        $name = $request->request->get('name');
        $msg = $request->request->get('msg');
        $id = $request->request->get('id');

        $entityManager = $this->getDoctrine()->getManager('board');

        $message = $entityManager->find(
            'ScottBoardBundle:Message',
            $id
        );

        if ( $message === null) {
            return $this->render(
                'ScottBoardBundle:message:error.html.twig',
                ['reason' => 'reply']
            );
        } else {
            $insertQuery = new Entity\ReplyMessage();
            $insertQuery->setMessage($message);
        }

        $insertQuery->setName($name);
        $insertQuery->setMsg($msg);
        $insertQuery->setTime();

        $entityManager->persist($insertQuery);
        $entityManager->flush();

        return $this->render(
            'ScottBoardBundle:message:add.html.twig',
            ['message' => $insertQuery]
        );
    }

    /**
     * @Route("/message/delete", name="delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request)
    {
        $id = $request->request->get('id');

        $entityManager = $this->getDoctrine()->getManager('board');
        $query = $entityManager->find('ScottBoardBundle:Message', $id);

        if ($query === null) {
            return $this->render(
                'ScottBoardBundle:message:error.html.twig',
                ['reason' => 'delete']
            );
        } else {
            $entityManager->remove($query);
            $entityManager->flush();
        }

        return $this->render(
            'ScottBoardBundle:message:delete.html.twig',
            ['message' => $query]
        );

    }

    /**
     * @Route("/message/reply/delete", name="reply_delete")
     * @Method("DELETE")
     */
    public function replyDeleteAction(Request $request)
    {
        $id = $request->request->get('id');

        $entityManager = $this->getDoctrine()->getManager('board');
        $query = $entityManager->find('ScottBoardBundle:ReplyMessage', $id);

        if ($query === null) {
            return $this->render(
                'ScottBoardBundle:message:error.html.twig',
                ['reason' => 'delete']
            );
        } else {
            $entityManager->remove($query);
            $entityManager->flush();
        }

        return $this->render(
            'ScottBoardBundle:message:delete.html.twig',
            ['message' => $query]
        );
    }

    /**
     * @Route("/message/update", name="update")
     * @Method("PUT")
     */
    public function updateAction(Request $request)
    {
        $id = $request->request->get('id');
        $msg = $request->request->get('msg');

        $entityManager = $this->getDoctrine()->getManager('board');
        $query = $entityManager->find('ScottBoardBundle:Message', $id);

        if ($query === null) {
            return $this->render(
                'ScottBoardBundle:message:error.html.twig',
                ['reason' => 'update']
            );
        } else {
            $query->setMsg($msg);
            $entityManager->persist($query);
            $entityManager->flush();
        }

        return $this->render(
            'ScottBoardBundle:message:update.html.twig',
            ['message' => $query]
        );
    }

    /**
     * @Route("/message/reply/update", name="reply_update")
     * @Method("PUT")
     */
    public function replyUpdateAction(Request $request)
    {
        $id = $request->request->get('id');
        $msg = $request->request->get('msg');

        $entityManager = $this->getDoctrine()->getManager('board');
        $query = $entityManager->find('ScottBoardBundle:ReplyMessage', $id);

        if ($query === null) {
            return $this->render(
                'ScottBoardBundle:message:error.html.twig',
                ['reason' => 'update']
            );
        } else {
            $query->setMsg($msg);
            $entityManager->persist($query);
            $entityManager->flush();
        }

        return $this->render(
            'ScottBoardBundle:message:update.html.twig',
            ['message' => $query]
        );
    }
}