<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Record;
use Scott\PassbookBundle\Entity\Account;
use Scott\PassbookBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecordTest extends WebTestCase
{
    public function testGetId()
    {
        $customer = new Customer('hahaha1027@WTF.com', 'passwordPW');
        $account = new Account($customer, 'USD');
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $this->assertEquals(null, $record->getId());
    }

    public function testConstructor()
    {
        $customer = new Customer('hahaha1027@WTF.com', 'passwordPW');
        $account = new Account($customer, 'USD');
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $this->assertEquals($account, $record->getAccount());
    }

    public function testCreateTime()
    {
        $customer = new Customer('hahaha1027@WTF.com', 'passwordPW');
        $account = new Account($customer, 'USD');
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $this->assertEquals($time, $record->getCreateTime());
    }

    public function testBalance()
    {
        $customer = new Customer('hahaha1027@WTF.com', 'passwordPW');
        $account = new Account($customer, 'USD');
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 8000);

        $this->assertEquals('12345', $record->getBalance());
    }

    public function testAmount()
    {
        $customer = new Customer('hahaha1027@WTF.com', 'passwordPW');
        $account = new Account($customer, 'USD');
        $time = new \DateTime();
        $record = new Record($account, $time, 12345, 81000);

        $this->assertEquals("81000", $record->getAmount());
    }

    public function testMemo()
    {
        $customer = new Customer('hahaha1027@WTF.com', 'passwordPW');
        $account = new Account($customer, 'USD');
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

        $customer = new Customer('hahaha1027@WTF.com', 'passwordPW');
        $account = new Account($customer, 'USD');
        $record = new Record($account, $time, 25000, 6000);

        $record->setMemo("Save money");
        $result = $record->toArray();

        $this->assertEquals($expectedResult, $result, "toArray failed!");
    }

}