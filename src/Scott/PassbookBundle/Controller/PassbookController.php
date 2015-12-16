<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Scott\PassbookBundle\Entity\Record;

class PassbookController extends Controller
{
    /**
     * @Route("/account/{accountId}/record",
     *      name="record_list",
     *      requirements={"accountId": "\d+"})
     *
     * @param int $accountId
     *
     * @Method("GET")
     */
    public function recordListAction(Request $request, $accountId)
    {
        $page = $request->query->get('page');
        $pageLimit = $request->query->get('pageLimit');

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $account = $entityManager->find('ScottPassbookBundle:Account', $accountId);

            if (empty($account)) {
                throw new \Exception("The account is invalid. Please try again!");
            }

            if (!preg_match('/^[0-9]*[1-9][0-9]*$/', $pageLimit)) {
                throw new \Exception("The page limit should be an integer!");
            }

            if ($pageLimit > 100) {
                throw new \Exception("The page limit should be less than 100. Try again!");
            }

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
                $totalPage = 1;
            }

            if ($page > $totalPage) {
                 throw new \Exception("Not a invalid page! Please try again!");
            }

            $account = $account->toArray();
            $result = [
                'status' => 'successful',
                'data' => [
                    'account' => $account,
                    'record' => $record,
                    'totalPages' => $totalPage,
                ],
            ];
        } catch (\Exception $e) {
            $result = [
                'status' => 'failed',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]
            ];
        }

        return new Response(json_encode($result));
    }

    /**
     * @Route("/account/{accountId}/record",
     *      name="record_add",
     *      requirements={"accountId": "\d+"})
     *
     * @param int $accountId
     *
     * @Method("POST")
     */
    public function recordAddAction(Request $request, $accountId)
    {
        $amount = $request->request->get('amount');
        $memo = $request->request->get('memo');

        try {
            if (strlen($amount) > 12) {
                throw new \Exception("Length of amount must be less than 12! Try again!");
            }

            if (!preg_match("/^[-+]?\d*\.?\d{1,2}?$/", $amount)) {
                throw new \Exception("The amount must be a float and digits after decimal point must be less than 2!");
            }

            if (empty($amount)) {
                throw new \Exception("The amount should not be empty or 0 !");
            }

            if ($amount == 0) {
                throw new \Exception("One does not simply save or withdraw 0 dollar.");
            }

            if (strlen($memo) > 50) {
                throw new \Exception("The length of Memo should be less than 50!");
            }

            $entityManager = $this->getDoctrine()->getManager();
            $record = new Record();
            $updateAccount = $entityManager->find('ScottPassbookBundle:Account', $accountId);

            if (empty($updateAccount)) {
                throw new \Exception("The account is invalid. Please try again!");
            }

            $balance = $updateAccount->getBalance();
            $record->setAccount($updateAccount);
            $record->setBalance($balance);
            $record->setCreateTime(new \DateTime());
            $record->setMemo($memo);

            $record->setAmount($amount);
            $updateAccount->setBalance($balance + $amount);

            if ($balance+$amount < 0) {
                throw new \Exception("The number you are withdrawing is too big!");
            }

            $entityManager->persist($record);
            $entityManager->persist($updateAccount);
            $entityManager->flush();

            $result = [
                'accountId' => $accountId,
                'record' => $record,
            ];

            return new Response($result);

        } catch (\Exception $e) {
            $result = [
                'status' => 'failed',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]
            ];
            return $this->render('ScottPassbookBundle:Default:error.html.twig', ['result' => json_encode($result)]);
        }
    }

}
