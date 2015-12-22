<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Customer;
use Scott\PassbookBundle\Entity\Account;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerTest extends WebTestCase
{
    public function testGetId()
    {
        $customer = new Customer('user5566', 'qq1234');
        $this->assertEquals(null, $customer->getId());
    }

    public function testAccount()
    {
        $customer = new Customer('user5566', 'qq1234');
        $account = new Account($customer, 'JPY');

        $this->assertEquals($account, $customer->getAccount());
    }

    public function testPassword()
    {
        $customer = new Customer('user5566', 'qq1234');
        $this->assertEquals('qq1234', $customer->getPassword());

        $customer->setPassword("Test12345");
        $this->assertEquals('Test12345', $customer->getPassword());
    }

    public function testEmail()
    {
        $customer = new Customer('LOL@omg.com', 'wow');

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

        $customer = new Customer("NTD@test.org", "2000QQ2000");
        $account = new Account($customer, 'USD');
        $result = $customer->toArray();

        $this->assertEquals($expectedResult, $result, "toArray failed!");
    }

}