<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $customerId = $request->query->get('customerId');
        $page = $request->attributes->get('page');
        $request->attributes->set('customerId', $customerId);

        if (empty($customerId) || is_null($customerId)) {
            return $this->redirectToRoute('login');
        }

        $customerId = base64_decode($customerId);
        $entityManager = $this->getDoctrine()->getManager();
        $account = $entityManager
            ->getRepository('ScottPassbookBundle:Account')
            ->findBy(["customer" => $customerId]);

        if (empty($account)) {
            return $this->render('ScottPassbookBundle:Passbook:passbook_error.html.twig', [
                'error' => "account",
            ]);
        }
        $accountId = $account[0]->getId();
        $result =  $this->pagination($page, $accountId);

        if ($page > $result['total']) {
            return $this->render('ScottPassbookBundle:Passbook:passbook_error.html.twig', [
                'error' => "page",
            ]);
        }

        return $this->render('ScottPassbookBundle:Passbook:index.html.twig', [
            'account' => $account[0],
            'customerId' => base64_encode($customerId),
            'record' => $result['record'],
            'totalPages' => $result['total'],
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
        $customerId = $form['customerId'];

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

        return $this->redirectToRoute('index', [
            'customerId' => $customerId,
        ]);
    }

    function pagination($page, $accountId)
    {
        $pageLimit = 20;

        if ($page <= 0) {
            $page = 1;
        }

        if ($page == 1) {
            $offset = 0;
        }

        if ($page != 1) {
            $offset = $pageLimit * ($page - 1);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $record = $entityManager
            ->getRepository('ScottPassbookBundle:Record')
            ->getPages($accountId, $offset, $pageLimit);

        $total = $entityManager
            ->getRepository('ScottPassbookBundle:Record')
            ->getCount($accountId);

        $totalPage = floor($total / $pageLimit);
        if (($total % $pageLimit) > 0) {
            $totalPage++;
        }

        if ($total == 0) {
            $totalPage =1;
        }

        return $result = [
            'record' => $record,
            'total' => $totalPage,
        ];
    }
}
