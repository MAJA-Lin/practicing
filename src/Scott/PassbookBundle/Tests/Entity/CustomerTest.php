<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Customer;
use Scott\PassbookBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerTest extends WebTestCase
{
    protected $fixture;
    protected $fixtureAccount;

    protected function setUp()
    {
        $this->fixture = new Customer();
        $this->fixtureAccount = new Account();
    }

    public function testGetId()
    {
        $testId = 15;

        $reflector = new \ReflectionClass('Scott\PassbookBundle\Entity\Customer');
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

    public function testPassword()
    {
        $this->fixture->setPassword("Test12345");
        $this->assertEquals('Test12345', $this->fixture->getPassword());
    }

    public function testEmail()
    {
        $this->fixture->setEmail("LOL@omg.com");
        $this->assertEquals('LOL@omg.com', $this->fixture->getEmail());
    }

    protected function tearDown()
    {
        unset($this->fixture);
        unset($this->fixtureAccount);
    }

}