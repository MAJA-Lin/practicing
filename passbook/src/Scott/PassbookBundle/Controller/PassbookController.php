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
     * @Route("/index", name="index")
     *
     *
     */
    public function indexAction(Request $request)
    {
        $response = new Response;
        $session = $request->getSession();
        $customer = $session->get('customer');

        if (empty($customer) || is_null($customer)) {
            return $this->redirectToRoute('login');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $account = $entityManager
            ->getRepository('ScottPassbookBundle:Account')
            ->findBy(['customer' => $customer[0]->getId()]);

        $record = $entityManager
            ->getRepository('ScottPassbookBundle:Record')
            ->findBy(['account' => $account[0]->getId()]);

        $session->set('Account', $account);
        $session->set('Record', $record);

        /*
        $request->cookies->set('Account', $account);
        $request->cookies->set('Record', $record);
        //
        $response->headers->setCookie(new Cookie('Account', $account));
        $response->headers->setCookie(new Cookie('Record', $record));
        */
        $new_record = [];
        $form = $this->createFormBuilder($new_record)
            ->setMethod("POST")
            ->setAction($this->generateUrl('record_add'))
            ->add(
                'option',
                'choice', [
                    'choices' => [
                        'Save' => 'Save',
                        'Withdraw' => 'Withdraw',
                    ]
                ])
            ->add(
                'amount',
                'number', [
                    'attr' => [
                        'maxlength' => 12,
                        'pattern' => '([0-9]*\.[0-9]+|[0-9]+)',
                        'title' => 'Must be an integer or float',
                    ]
                ])
            ->add(
                'memo',
                'text', [
                    'attr' => [
                        'maxlength' => 50
                    ]
                ])
            ->add(
                'signup',
                'submit', [
                    'label' => 'Add New Record'
                ])
            ->getForm();

        return $this->render('ScottPassbookBundle:Passbook:index.html.twig', [
            'form' => $form->createView(),
            'request' => $request,
            'response' => $response,
        ]);


    }

    /**
     * @Route("/reocrd/add", name="record_add")
     * @Method("POST")
     *
     */
    public function recordAddAction(Request $request)
    {
        //First: Add data into cookie
        //Second: Add verification
        //Update & retrive cookie
        //將資料塞進seesion後, 如果要再寫入database, 是否要重新find(retrive from database)?

        $form = $request->request->get('form');


        $option =$form['option'];
        $amount = (float) $form['amount'];
        $memo = $form['memo'];

        if (strlen($form['amount']) > 12) {

            return $this->render('ScottPassbookBundle:Passbook:error.html.twig', [
                'request' => $request,
                'error' => "amount",
            ]);
        }

        //Assume the value checker are done
        $session = $request->getSession();
        $account = $session->get('Account');

        if (is_array($account)) {
            $oldAccount = $account[0];
        } else {
            $oldAccount = $account;
        }

        $entityManager = $this->getDoctrine()->getManager();
        $record = new Entity\Record();
        //$account = $request->session->get('Account');
        $updateAccount = $entityManager
            ->find('ScottPassbookBundle:Account', $oldAccount->getId());
        $balance = $oldAccount->getBalance();

        $record->setAccount($oldAccount);
        $record->setBalance($balance);
        $record->setCreateTime(new \DateTime());
        $record->setMemo($memo);
        $record->setAmount($amount);

        if ($option == "Save") {
            $oldAccount->setBalance($balance + $amount);
        } else {
            $oldAccount->setBalance($balance - $amount);
        }

        $entityManager->persist($oldAccount, $record);
        $entityManager->flush();

        $session->replace([
            'Account' => $oldAccount,
            'Record' => $record,
        ]);

        return $this->render('ScottPassbookBundle:Passbook:error.html.twig', [
                'request' => $request,
                'error' => "amount",
                'value' => $record,
            ]);
        //return $this->redirectToRoute('index');
    }

}