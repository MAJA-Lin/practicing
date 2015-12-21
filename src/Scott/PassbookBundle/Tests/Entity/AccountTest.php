<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Account;
use Scott\PassbookBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountTest extends WebTestCase
{
    public function testGetId()
    {
        $account = new Account();
        $this->assertEquals(null, $account->getId());
    }

    public function testCustomer()
    {
        $account = new Account();
        $customer = new Customer();

        $account->addCustomer($customer);
        $this->assertEquals($customer, $account->getCustomer());
    }

    public function testCurrency()
    {
        $account = new Account();

        $account->setCurrency("NTD");
        $this->assertEquals("NTD", $account->getCurrency());
    }

    public function testBalance()
    {
        $account = new Account();

        $this->assertEquals(0, $account->getBalance(), 'Default value is not 0');

        $account->setBalance("12345");
        $this->assertEquals('12345', $account->getBalance());
    }

    public function testToArray()
    {
        $expectedResult = [
            'id' => null,
            'customerId' => null,
            'currency' => "NTD",
            'balance' => "2000"
        ];

        $account = new Account();
        $customer = new Customer();

        $account->addCustomer($customer);
        $account->setCurrency("NTD");
        $account->setBalance("2000");
        $result = $account->toArray();

        $this->assertEquals($expectedResult, $result, "toArray failed!");
    }

}