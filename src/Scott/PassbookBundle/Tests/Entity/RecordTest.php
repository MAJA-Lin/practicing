<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Record;
use Scott\PassbookBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecordTest extends WebTestCase
{
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new Record();
        $this->fixtureAccount = new Account();
    }

    public function testGetId()
    {
        $testId = 15;

        $reflector = new \ReflectionClass('Scott\PassbookBundle\Entity\Record');
        $property = $reflector->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->fixture, $testId);

        $this->assertEquals($testId, $this->fixture->getId());
    }

    public function testAccount()
    {
        $this->fixture->setAccount($this->fixtureAccount);
        $this->assertSame($this->fixtureAccount, $this->fixture->getAccount());
    }

    public function testCreateTime()
    {
        $time = new \DateTime();
        $this->fixture->setCreateTime($time);
        $this->assertEquals($time, $this->fixture->getCreateTime());
    }

    public function testBalance()
    {
        $this->fixture->setBalance("12345");
        $this->assertEquals('12345', $this->fixture->getBalance());
    }

    public function testAmount()
    {
        $this->fixture->setAmount("81000");
        $this->assertEquals("81000", $this->fixture->getAmount());
    }

    public function testMemo()
    {
        $this->fixture->setMemo("What ever you want");
        $this->assertEquals("What ever you want", $this->fixture->getMemo());
    }

    protected function tearDown()
    {
        unset($this->fixture);
    }
}