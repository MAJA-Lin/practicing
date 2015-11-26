<?php

namespace Scott\PassbookBundle\Service;

//use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Scott\PassbookBundle\Entity as Entity;

class SecurityListener
{

   public function __construct(TokenStorage $security, Session $session)
   {
      $this->security = $security;
      $this->session = $session;
   }

   public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
   {
        /*
        $timezone = $this->security->getToken()->getUser()->getTimezone();
        if (empty($timezone)) {
            $timezone = 'UTC';
        }
        */

        $customer = $this->session->get('_security_main');
        $customer = unserialize($customer);

        $entityManager = $this->getDoctrine()->getManager();
        //$account


        $this->session->set('mew', "mew");
   }

}