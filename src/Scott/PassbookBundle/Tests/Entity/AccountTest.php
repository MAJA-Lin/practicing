<?php

namespace Scott\PassbookBundle\Tests\Entity;

use Scott\PassbookBundle\Entity\Account;
use Scott\PassbookBundle\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountTest extends WebTestCase
{
    public function testGetId()
    {
        $customer = new Customer('hohoho@christmas.en', 'jinglebell');
        $account = new Account($customer, 'NTD');
        $this->assertEquals(null, $account->getId());
    }

    public function testCustomer()
    {
        $customer = new Customer('hohoho@christmas.en', 'jinglebell');
        $account = new Account($customer, 'NTD');

        $this->assertEquals($customer, $account->getCustomer());
    }

    public function testVersion()
    {
        $customer = new Customer('hohoho@christmas.en', 'jinglebell');
        $account = new Account($customer, 'NTD');

        $this->assertEquals(null, $account->getversion());
    }

    public function testCurrency()
    {
        $customer = new Customer('hohoho@christmas.en', 'jinglebell');
        $account = new Account($customer, 'NTD');

        $this->assertEquals("NTD", $account->getCurrency());
    }

    public function testBalance()
    {
        $customer = new Customer('hohoho@christmas.en', 'jinglebell');
        $account = new Account($customer, 'NTD');

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

        $customer = new Customer('hohoho@christmas.en', 'jinglebell');
        $account = new Account($customer, 'NTD');
        $account->setBalance("2000");
        $result = $account->toArray();

        $this->assertEquals($expectedResult, $result, "toArray failed!");
    }

}
