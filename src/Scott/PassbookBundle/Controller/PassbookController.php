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

        $accountId = $customer[0]->getAccount();
	$entityManager = $this->getDoctrine()->getManager();
        $account = $entityManager
            ->find('ScottPassbookBundle:Account', $accountId);

        $record = $entityManager
            ->getRepository('ScottPassbookBundle:Record')
            ->findBy(['account' => $account->getId()]);

        $new_record = [];
        $form = $this->createFormBuilder($new_record)
            ->setMethod("POST")
            ->setAction($this->generateUrl('record_add'))
            ->add(
                'account_id',
                'hidden', [
                    'attr' => [
                        'value' => $account->getId()
                    ]
                ])
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
            'account' => $account,
            'record' => $record,
        ]);
    }

    /**
     * @Route("/reocrd/add", name="record_add")
     * @Method("POST")
     *
     */
    public function recordAddAction(Request $request)
    {

        $form = $request->request->get('form');
        $option =$form['option'];
        $amount = (float) $form['amount'];
        $memo = $form['memo'];
        $accountId = $form['account_id'];

        if (strlen($form['amount']) > 12) {
            return $this->render('ScottPassbookBundle:Passbook:passbook_error.html.twig', [
                'error' => "amount",
                'detail' => "length",
            ]);
        }

        if (!preg_match("/^\d+(\.\d+)?$/", $form['amount'])) {
            return $this->render('ScottPassbookBundle:Passbook:passbook_error.html.twig', [
                'error' => "amount",
                'detail' => "number",
            ]);
        }

        if (strlen($memo) > 50) {
            return $this->render('ScottPassbookBundle:Passbook:passbook_error.html.twig', [
                'error' => "memo",
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $record = new Entity\Record();
        $updateAccount = $entityManager
            ->find('ScottPassbookBundle:Account', $accountId);

        if (empty($updateAccount)) {
            return $this->render('ScottPassbookBundle:Passbook:passbook_error.html.twig', [
                'error' => "account",
            ]);
        }


        $balance = $updateAccount->getBalance();
        $record->setAccount($updateAccount);
        $record->setBalance($balance);
        $record->setCreateTime(new \DateTime());
        $record->setMemo($memo);

        if ($option == "Save") {
            $amount = abs($amount);
        } else {
            $amount = -1 * abs($amount);
        }
        $record->setAmount($amount);
        $updateAccount->setBalance($balance + $amount);

        $entityManager->persist($record);
        $entityManager->persist($updateAccount);
        $entityManager->flush();

        return $this->redirectToRoute('index');
    }

}
