<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Scott\PassbookBundle\Entity as Entity;

class PassbookController extends Controller
{
    /**
     * @Route("/index", name="index")
     *
     *
     */
    public function indexAction(Request $request)
    {
        // The second parameter is used to specify on what object the role is tested.
        /*
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY',
            null,
            'Unable to access this page!'
        );
        */

        $session = $request->getSession();
        $loginInfo = $session->get('_security_main');
        $loginInfo = unserialize($loginInfo);
        var_dump($loginInfo);

        return $this->render('ScottPassbookBundle:Passbook:index.html.twig', [
                'request' => $request,
            ]);
    }

}
