<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Customer;
use Scott\PassbookBundle\Entity\Account;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerTest extends WebTestCase
{
    public function testGetId()
    {
        $customer = new Customer();
        $this->assertEquals(null, $customer->getId());
    }

    public function testAccount()
    {
        $customer = new Customer();
        $account = new Account();

        $customer->setAccount($account);
        $this->assertEquals($account, $customer->getAccount());
    }

    public function testPassword()
    {
        $customer = new Customer();

        $customer->setPassword("Test12345");
        $this->assertEquals('Test12345', $customer->getPassword());
    }

    public function testEmail()
    {
        $customer = new Customer();

        $customer->setEmail("LOL@omg.com");
        $this->assertEquals('LOL@omg.com', $customer->getEmail());
    }

    public function testToArray()
    {
        $expectedResult = [
            'id' => null,
            'accountId' => null,
            'email' => "NTD@test.org",
            'password' => "2000QQ2000"
        ];

        $customer = new Customer();
        $account = new Account();

        $customer->setAccount($account);
        $customer->setEmail("NTD@test.org");
        $customer->setPassword("2000QQ2000");
        $result = $customer->toArray();

        $this->assertEquals($expectedResult, $result, "toArray failed!");
    }

}