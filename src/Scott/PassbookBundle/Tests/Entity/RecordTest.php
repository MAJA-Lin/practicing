<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Record;
use Scott\PassbookBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecordTest extends WebTestCase
{
    public function testGetId()
    {
        $record = new Record();
        $this->assertEquals(null, $record->getId());
    }

    public function testAccount()
    {
        $record = new Record();
        $account = new Account();

        $record->setAccount($account);
        $this->assertEquals($account, $record->getAccount());
    }

    public function testCreateTime()
    {
        $record = new Record();
        $time = new \DateTime();

        $record->setCreateTime($time);
        $this->assertEquals($time, $record->getCreateTime());
    }

    public function testBalance()
    {
        $record = new Record();

        $record->setBalance("12345");
        $this->assertEquals('12345', $record->getBalance());
    }

    public function testAmount()
    {
        $record = new Record();

        $record->setAmount("81000");
        $this->assertEquals("81000", $record->getAmount());
    }

    public function testMemo()
    {
        $record = new Record();

        $record->setMemo("What ever you want");
        $this->assertEquals("What ever you want", $record->getMemo());
    }

}