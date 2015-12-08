<?php

namespace Scott\PassbookBundle\Tests\Repository;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Scott\PassbookBundle\DataFixtures\ORM\LoadCustomerData;
use Scott\PassbookBundle\DataFixtures\ORM\LoadAccountData;
use Scott\PassbookBundle\DataFixtures\ORM\LoadRecordData;

class RecordRepositoryTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->loadFixtures([
            'Scott\PassbookBundle\DataFixtures\ORM\LoadCustomerData',
            'Scott\PassbookBundle\DataFixtures\ORM\LoadAccountData',
            'Scott\PassbookBundle\DataFixtures\ORM\LoadRecordData'
        ]);
    }

    public function testGetCount()
    {
        $account = $this->em
            ->getRepository('ScottPassbookBundle:Account')
            ->findAll();

        $recordCount = $this->em
            ->getRepository('ScottPassbookBundle:Record')
            ->getCount($account[0]->getId());

        $this->assertEquals(3, $recordCount);
    }

    /**
     * @group page
     */
    public function testGetPages()
    {

        $offset = 0;
        $pageLimit = 20;

        $account = $this->em
            ->getRepository('ScottPassbookBundle:Account')
            ->findAll();

        $record = $this->em
            ->getRepository('ScottPassbookBundle:Record')
            ->getPages($account[0]->getId(), $offset, $pageLimit);

        $expected = [
            0 => [
                'id' => "1",
                '1' => "1",
                'create_time' => "2015-12-05 12:30:30",
                'memo' => "Transaction #1",
                'amount' => "5000.0",
                'balance' => "2000.0",
            ],
            1 => [
                'id' => "2",
                '1' => "1",
                'create_time' => "2015-12-05 12:30:30",
                'memo' => "Transaction #2",
                'amount' => "12000.0",
                'balance' => "7000.0",
            ],
            2 => [
                'id' => "3",
                '1' => "1",
                'create_time' => "2015-12-05 12:30:30",
                'memo' => "Transaction #3",
                'amount' => "12000.0",
                'balance' => "19000.0",
            ],
        ];

        $this->assertSame($expected, $record, 'Results per pages not matched!');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
    }
}