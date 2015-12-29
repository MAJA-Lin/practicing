<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Scott\PassbookBundle\Entity\Record;
use Predis\Autoloader;
use Predis\Client;
use Predis\Replication\ReplicationStrategy;
use Predis\Connection\Aggregate\MasterSlaveReplication;

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

            $recordRepo = $entityManager->getRepository('ScottPassbookBundle:Record');
            $record = $recordRepo->getRecords($accountId, $offset, $pageLimit);
            $total = $recordRepo->getCount($accountId);

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
        return new JsonResponse($result);
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
        Autoloader::register();
        $redis = new Client();

        $amount = $request->request->get('amount');
        $memo = $request->request->get('memo');
        $entityManager = $this->getDoctrine()->getManager();

        try {
            if (strlen($amount) > 12) {
                throw new \Exception("Length of amount must be less than 12! Try again!");
            }

            if (!preg_match("/^[-+]?\d*\.?\d{1,2}?$/", $amount)) {
                throw new \Exception("The amount must be a float and digits after decimal point must be less than 2!");
            }

            if ($amount == 0) {
                throw new \Exception("One does not simply save or withdraw 0 dollar.");
            }

            if (strlen($memo) > 50) {
                throw new \Exception("The length of Memo should be less than 50!");
            }

            $account = $entityManager->find('ScottPassbookBundle:Account', $accountId);

            if (empty($account) || is_null($account)) {
                $redis->del('account:' . $accountId);
                throw new \Exception("The account is invalid. Please try again!");
            }

            if ($redis->exists('account:' . $accountId)) {
                $accountArray = $redis->hgetall('account:' . $accountId);
            }

            if (!$redis->exists('account:' . $accountId)) {
                $accountArray = [
                    'id' => $account->getId(),
                    'balance' => $account->getBalance(),
                    'version' => $account->getVersion()
                ];
                $redis->hmset('account:' . $accountId, $accountArray);
            }

            $redis->watch('account:' . $accountId);

            $balance = $accountArray['balance'];
            $version = $accountArray['version'];
            $newBalance = $balance + $amount;
            if ($newBalance < 0) {
                throw new \Exception("The number you are withdrawing is too big!");
            }

            $redis->multi();
            $redis->hincrby('account:' . $accountId, 'version', 1);
            $redis->hincrbyfloat('account:' . $accountId, 'balance', $amount);
            $result = $redis->exec();

            if (is_null($result)) {
                throw new \Exception("Transaction failed! Please try again!");
            }

            $time = new \DateTime('now');
            $record = new Record($account, $time, $result[1], $amount);
            $record->setMemo($memo);
            $entityManager->persist($record);
            $entityManager->flush();

            $record = $record->toArray();
            $account = $account->toArray();
            $accountResult = [
                'id' => $account['id'],
                'customerId' => $account['customerId'],
                'currency' => $account['currency'],
                'balance' => $redis->hget('account:' . $accountId, 'balance')
            ];

            $result = [
                'status' => 'successful',
                'data' => [
                    'account' => $accountResult,
                    'record' => $record,
                ]
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
        return new JsonResponse($result);
    }
}
