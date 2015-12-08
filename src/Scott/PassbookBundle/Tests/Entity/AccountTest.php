<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Account;
use Scott\PassbookBundle\Entity\Customer;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountTest extends WebTestCase
{
    protected $fixture;
    protected $fixtureCustomer;

    protected function setUp()
    {
        $this->fixture = new Account();
        $this->fixtureCustomer = new Customer();
    }

    public function testGetId()
    {
        $testId = 15;

        $reflector = new \ReflectionClass('Scott\PassbookBundle\Entity\Account');
        $property = $reflector->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->fixture, $testId);

        $this->assertEquals($testId, $this->fixture->getId());
    }

    public function testCustomer()
    {
        $this->fixture->addCustomer($this->fixtureCustomer);
        $this->assertSame($this->fixtureCustomer, $this->fixture->getCustomer());
    }

    public function testCurrency()
    {
        $this->fixture->setCurrency("NTD");
        $this->assertEquals("NTD", $this->fixture->getCurrency());
    }

    public function testBalance()
    {
        $this->assertEquals(0, $this->fixture->getBalance(), 'Default value is not 0');
        $this->fixture->setBalance("12345");
        $this->assertEquals('12345', $this->fixture->getBalance());
    }

    protected function tearDown()
    {
        unset($this->fixture);
        unset($this->fixture_customer);
    }
}