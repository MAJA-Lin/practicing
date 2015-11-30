<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Cookie;
use Scott\PassbookBundle\Entity as Entity;

class PassbookController extends Controller
{
    /**
     * @Route("/index/{page}",
     *      name="index",
     *      defaults={"page": 1},
     *      requirements={"page": "\d+"})
     *
     */
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $customer = $session->get('customer');

        if (empty($customer) || is_null($customer)) {
            return $this->redirectToRoute('login');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $account = $entityManager
            ->find('ScottPassbookBundle:Account', $customer[0]->getAccount());

        $record = $entityManager
            ->getRepository('ScottPassbookBundle:Record')
            ->findBy(['account' => $account->getId()]);

        return $this->render('ScottPassbookBundle:Passbook:index.html.twig', [
            'request' => $request,
            'account' => $account,
            'record' => $record,
        ]);
    }

}