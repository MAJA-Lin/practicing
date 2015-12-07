<?php

namespace Scott\PassbookBundle\Repository;

use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
    /**
     * 取得資料總筆數
     *
     * @return string $total 資料總數
     */
    public function getCount($accountId)
    {
        $dql = "SELECT count(r.id) FROM ScottPassbookBundle:Record r 
            WHERE r.account = ?1";
        $query = $this
            ->_em
            ->createQuery($dql)
            ->setParameter(1, $accountId)
            ->getSingleResult();
        $total = $query[1];
        return $total;
    }

    /**
     * 取得每頁資料
     * @param int $accountId 搜尋目標的account id
     * @param int $offset 目前第幾頁
     * @param int $pageLimit 每頁最大數量資料
     * @return array $query select 搜尋完的結果
     */
    public function getPages($accountId, $offset, $pageLimit)
    {
        $dql = "SELECT r.id, IDENTITY(r.account), r.create_time, r.memo, r.amount, r.balance
            FROM ScottPassbookBundle:Record r WHERE r.account = ?1";
        $query = $this->_em
            ->createQuery($dql)
            ->setParameter(1, $accountId)
            ->setFirstResult($offset)
            ->setMaxResults($pageLimit)
            ->getScalarResult();
        return $query;
    }

}
