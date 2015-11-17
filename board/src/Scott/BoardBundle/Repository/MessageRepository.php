<?php

namespace Scott\BoardBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    /**
     * 取得資料總筆數
     *
     * @return string $total 資料總數
     */
    public function getTotalNumber()
    {
        $dql = "SELECT count(m.id) FROM ScottBoardBundle:Message m";
        $query = $this->_em->createQuery($dql)->getSingleResult();
        $total = $query[1];
        return $total;
    }

    /**
     * 取得每頁資料
     * @param int $offset 目前第幾頁
     * @param int $pageLimit 每頁最大數量資料
     * @return array $query select 搜尋完的結果
     */
    public function getPages($offset, $pageLimit)
    {
        $dql = "SELECT m.id, m.name, m.time, m.msg FROM ScottBoardBundle:Message m";
        $query = $this->_em
            ->createQuery($dql)
            ->setFirstResult($offset)
            ->setMaxResults($pageLimit)
            ->getScalarResult();
        return $query;
    }

}
