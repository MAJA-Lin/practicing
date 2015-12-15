<?php

namespace Scott\PassbookBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Scott\PassbookBundle\Entity\Customer;
use Scott\PassbookBundle\Entity\Account;
use Doctrine\Common\DataFixtures\Loader;

class CustomerControllerTest extends WebTestCase
{
    protected $client;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->client = static::makeClient();

        $this->loadFixtures([
            'Scott\PassbookBundle\DataFixtures\ORM\LoadCustomerData',
            'Scott\PassbookBundle\DataFixtures\ORM\LoadAccountData',
            'Scott\PassbookBundle\DataFixtures\ORM\LoadRecordData'
        ]);
    }

    public function testLoginByValidCriteria()
    {
        $validCriteria = [
            'email' => 'dora@co.jp',
            'password' => 'dd',
        ];

        $this->client->request('POST', '/login/check', $validCriteria);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $expectedStatus = "successful";
        $expectedData = ['customerId' => 1];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedData, $response['data']);
    }

    public function testLoginByInvalidPassword()
    {
        $invalidPassword = [
            'email' => 'dora@co.jp',
            'password' => 'hahauccu',
        ];

        $this->client->request('POST', '/login/check', $invalidPassword);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'Sorry, your email or password is wrong.',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testLoginByNotExistedCustomer()
    {
        $invalidCriteria = [
            'email' => 'whatever@orz.error',
            'password' => 'wrongPassword',
        ];

        $this->client->request('POST', '/login/check', $invalidCriteria);
        $response = json_decode($this->client->getResponse()->getContent(), true);
        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'Sorry, your email or password is wrong.',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
}