<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Scott\PassbookBundle\Entity\Record;
use \Exception;

class PassbookController extends Controller
{
    /**
     * @Route("/index/{page}",
     *      name="index",
     *      defaults={"page": 1},
     *      requirements={"page": "\d+"})
     *
     * @Method("GET")
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
        $account = $entityManager->getRepository('ScottPassbookBundle:Account')
            ->findOneBy(["customer" => $customerId]);

        try {
            if (empty($account)) {
                throw new Exception("Something went wrong! Please login again!");
            }
        } catch (Exception $e) {
            $result = [
                'status' => 'failed',
                'action' => 'index',
                'data' => [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]
                ]
            ];
            return $this->render('ScottPassbookBundle:Default:error.html.twig', ['result' => json_encode($result)]);
        }

        $accountId = $account->getId();
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

        $record = $entityManager->getRepository('ScottPassbookBundle:Record')
            ->getPages($accountId, $offset, $pageLimit);

        $total = $entityManager->getRepository('ScottPassbookBundle:Record')
            ->getCount($accountId);

        $totalPage = floor($total / $pageLimit);
        if (($total % $pageLimit) > 0) {
            $totalPage++;
        }

        if ($total == 0) {
            $totalPage =1;
        }

        try {
            if ($page > $totalPage) {
                 throw new Exception("Not a invalid page! Please try again!");
            }
        } catch (Exception $e) {
            $result = [
                'status' => 'failed',
                'action' => 'index',
                'data' => [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]
                ]
            ];
            return $this->render('ScottPassbookBundle:Default:error.html.twig', ['result' => json_encode($result)]);
        }

        $account = $account->toArray();
        $result = [
            'status' => 'successful',
            'action' => 'index',
            'data' => [
                'account' => $account,
                'customerId' => base64_encode($customerId),
                'record' => $record,
                'totalPages' => $totalPage,
            ],
        ];

        return $this->render('ScottPassbookBundle:Passbook:index.html.twig', ['result' => json_encode($result)]);
    }

    /**
     * @Route("/reocrd/add", name="record_add")
     *
     * @Method("POST")
     */
    public function recordAddAction(Request $request)
    {

        $form = $request->request->get('form');
        $amount = (float) $form['amount'];
        $memo = $form['memo'];
        $accountId = $form['account_id'];
        $customerId = $form['customerId'];

        try {
            if (strlen($form['amount']) > 12) {
                throw new Exception("Length of amount must be less than 12! Try again!");
            }

            if (!preg_match("/^[-+]?\d*\.?\d{1,2}?$/", $form['amount'])) {
                throw new Exception("The amount must be a float and digits after decimal point must be less than 2!");
            }

            if (empty($form['amount'])) {
                throw new Exception("The amount should not be empty or 0 !");
            }

            if ($form['amount'] == 0) {
                throw new Exception("One does not simply save or withdraw 0 dollar.");
            }

            if (strlen($memo) > 50) {
                throw new Exception("The length of Memo should be less than 50!");
            }
        } catch (Exception $e) {
            $result = [
                'status' => 'failed',
                'action' => 'recordAdd',
                'data' => [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]
                ]
            ];
            return $this->render('ScottPassbookBundle:Default:error.html.twig', ['result' => json_encode($result)]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $record = new Record();
        $updateAccount = $entityManager->find('ScottPassbookBundle:Account', $accountId);

        try {
            if (empty($updateAccount)) {
                throw new Exception("Something went wrong! Please login again!");
            }
        } catch (Exception $e) {
            $result = [
                'status' => 'failed',
                'action' => 'recordAdd',
                'data' => [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]
                ]
            ];
            return $this->render('ScottPassbookBundle:Default:error.html.twig', ['result' => json_encode($result)]);
        }

        $balance = $updateAccount->getBalance();
        $record->setAccount($updateAccount);
        $record->setBalance($balance);
        $record->setCreateTime(new \DateTime());
        $record->setMemo($memo);

        $record->setAmount($amount);
        $updateAccount->setBalance($balance + $amount);

        try {
            if ($balance+$amount < 0) {
                throw new Exception("The number you are withdrawing is too big!");
            }
        } catch (Exception $e) {
            $result = [
                'status' => 'failed',
                'action' => 'recordAdd',
                'data' => [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]
                ]
            ];
            return $this->render('ScottPassbookBundle:Default:error.html.twig', ['result' => json_encode($result)]);
        }

        $entityManager->persist($record);
        $entityManager->persist($updateAccount);
        $entityManager->flush();

        return $this->redirectToRoute('index', ['customerId' => $customerId]);
    }

}
