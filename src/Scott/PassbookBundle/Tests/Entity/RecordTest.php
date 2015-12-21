<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Record;
use Scott\PassbookBundle\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecordTest extends WebTestCase
{
    public function testGetId()
    {
        $account = new Account();
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $this->assertEquals(null, $record->getId());
    }

    public function testConstructor()
    {
        $account = new Account();
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $this->assertEquals($account, $record->getAccount());
    }

    public function testCreateTime()
    {
        $account = new Account();
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $this->assertEquals($time, $record->getCreateTime());
    }

    public function testBalance()
    {
        $account = new Account();
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $this->assertEquals('12345', $record->getBalance());
    }

    public function testAmount()
    {
        $account = new Account();
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 81000);

        $this->assertEquals("81000", $record->getAmount());
    }

    public function testMemo()
    {
        $account = new Account();
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $record->setMemo("What ever you want");
        $this->assertEquals("What ever you want", $record->getMemo());
    }

    public function testToArray()
    {
        $time = new \DateTime('2015-12-05 12:30:30');

        $expectedResult = [
            'id' => null,
            'accountId' => null,
            'create_time' => $time,
            'balance' => "25000",
            'amount' => "6000",
            'memo' => "Save money",
        ];

        $account = new Account();
        $record = new Record($account, $time, 25000, 6000);

        $record->setMemo("Save money");
        $result = $record->toArray();

        $this->assertEquals($expectedResult, $result, "toArray failed!");
    }

}