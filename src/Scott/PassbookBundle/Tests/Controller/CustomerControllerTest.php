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
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadCustomerData',
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadAccountData',
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadRecordData'
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

    public function testSignupByValidCustomer()
    {
        $customer = [
            'email' => 'testDec09@2015.afternoon.org',
            'passwordFirst' => 'hey',
            'passwordSecond' => 'hey',
            'currency' => 'EUR'
        ];

        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = 'successful';
        $expectedData = [
            'customer' => [
                'id' => 6,
                'accountId' => 6,
                'password' => "hey",
                'email' => "testDec09@2015.afternoon.org"
            ],
            'account' => [
                'id' => 6,
                'customerId' => 6,
                'currency' => 'EUR',
                'balance' => 0
            ],
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedData, $response['data']);
    }

    public function testSignupByInvalidEmailFormat()
    {
        $customer = [
            'email' => 'HAKUNA MATATA',
            'passwordFirst' => 'hey',
            'passwordSecond' => 'hey',
            'currency' => 'EUR'
        ];

        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            "message" => "The format of email is invalid! Try again!",
            "code" => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testSignupByInvalidEmailLength()
    {
        $customer = [
            'email' => 'abcdefghiJklMnopqrstUvwxyzAre@26isnotenoughdigits.afternoon.org',
            'passwordFirst' => 'hey567',
            'passwordSecond' => 'hey567',
            'currency' => 'EUR'
        ];

        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            "message" => "The length of email should be less than 40!",
            "code" => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testSignupByInvalidEmailCharacters()
    {
        $customer = [
            'email' => "!#$%&'*+-/=?^_`{}|~@example.org",
            'passwordFirst' => 'hey567',
            'passwordSecond' => 'hey567',
            'currency' => 'EUR'
        ];

        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            "message" => "Available characters are: numbers, alphabets and @_.-",
            "code" => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testSignupByInvalidRepeatedPassword()
    {
        $customer = [
            'email' => "koty@example.org",
            'passwordFirst' => 'hey567',
            'passwordSecond' => 'hey56667',
            'currency' => 'EUR'
        ];

        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            "message" => "Passwords do not match! Please try again!",
            "code" => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testSignupByInvalidPasswordLength()
    {
        $customer = [
            'email' => "koty@example.org",
            'passwordFirst' => 'password12345678910',
            'passwordSecond' => 'password12345678910',
            'currency' => 'EUR'
        ];

        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            "message" => "The length of password should be less than 16!",
            "code" => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testSignupByInvalidPasswordCharacter()
    {
        $customer = [
            'email' => "koty@example.org",
            'passwordFirst' => 'passwor中文',
            'passwordSecond' => 'passwor中文',
            'currency' => 'EUR'
        ];

        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            "message" => "Available characters: numbers, alphabets and @_.-!",
            "code" => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testSignupByInvalidCurrency()
    {
        $customer = [
            'email' => "koty@example.org",
            'passwordFirst' => 'passwor17',
            'passwordSecond' => 'passwor17',
            'currency' => 'KYI'
        ];

        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            "message" => "The currency you select is invalid! Please try again!",
            "code" => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testSignupByRegisteredCustomer()
    {
        $customer = [
            'email' => "dora@co.jp",
            'passwordFirst' => 'passwor17',
            'passwordSecond' => 'passwor17',
            'currency' => 'USD'
        ];
        $this->client->request('POST', '/signup/check', $customer);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            "message" => "The email has been registered! Try another one!",
            "code" => 0
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