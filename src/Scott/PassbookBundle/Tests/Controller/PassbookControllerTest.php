<?php

namespace Scott\PassbookBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Scott\PassbookBundle\Entity\Account;
use Scott\PassbookBundle\Entity\Record;
use Doctrine\Common\DataFixtures\Loader;

class PassbookControllerTest extends WebTestCase
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

    public function testRecordListByValidData()
    {
        $validAccountId = 1;
        $pageData = [
            'page' => 1,
            'pageLimit' => 2,
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "successful";
        $expectedAccount = [
            'id' => 1,
            'customerId' => 1,
            'currency' => 'USD',
            'balance' => 31000
        ];
        $expectedRecord = [
            0 => [
                '1' => 1,
                'id' => 1,
                'create_time' => '2015-12-05 12:30:30',
                'memo' => 'Transaction #1',
                'amount' => 5000,
                'balance' => 2000
            ],
            1 => [
                '1' => 1,
                'id' => 2,
                'create_time' => '2015-12-05 12:30:30',
                'memo' => 'Transaction #2',
                'amount' => 12000,
                'balance' => 7000
            ]
        ];
        $expectedTotalPages = 2;

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedAccount, $response['data']['account']);
        $this->assertEquals($expectedRecord, $response['data']['record']);
        $this->assertEquals($expectedTotalPages, $response['data']['totalPages']);
    }

    public function testRecordListByInvalidAccount()
    {
        $invalidAccountId = 0;
        $pageData = [
            'page' => 1,
            'pageLimit' => 2,
        ];

        $this->client->request('GET', '/account/'. $invalidAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The account is invalid. Please try again!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordListByNegativePageLimit()
    {
        $validAccountId = 1;
        $pageData = [
            'page' => 1,
            'pageLimit' => -5,
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The page limit should be an integer!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordListByCharacterPageLimit()
    {
        $validAccountId = 1;
        $pageData = [
            'page' => 1,
            'pageLimit' => 'haha',
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The page limit should be an integer!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordListByEmptyPageLimit()
    {
        $validAccountId = 1;
        $pageData = [
            'page' => 1,
            'pageLimit' => '',
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The page limit should be an integer!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordListByLargePageLimit()
    {
        $validAccountId = 1;
        $pageData = [
            'page' => 1,
            'pageLimit' => 2000,
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The page limit should be less than 100. Try again!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordListByNegativePage()
    {
        $validAccountId = 1;
        $pageData = [
            'page' => -3,
            'pageLimit' => 2,
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "successful";
        $expectedAccount = [
            'id' => 1,
            'customerId' => 1,
            'currency' => 'USD',
            'balance' => 31000,
        ];
        $expectedRecord = [
            0 => [
                '1' => 1,
                'id' => 1,
                'create_time' => '2015-12-05 12:30:30',
                'memo' => 'Transaction #1',
                'amount' => 5000,
                'balance' => 2000
            ],
            1 => [
                '1' => 1,
                'id' => 2,
                'create_time' => '2015-12-05 12:30:30',
                'memo' => 'Transaction #2',
                'amount' => 12000,
                'balance' => 7000
            ]
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedAccount, $response['data']['account']);
        $this->assertEquals($expectedRecord, $response['data']['record']);
    }

    public function testRecordListByCharacterPage()
    {
        $validAccountId = 1;
        $pageData = [
            'page' => 'QQ',
            'pageLimit' => 2,
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "successful";
        $expectedAccount = [
            'id' => 1,
            'customerId' => 1,
            'currency' => 'USD',
            'balance' => 31000,
        ];
        $expectedRecord = [
            0 => [
                '1' => 1,
                'id' => 1,
                'create_time' => '2015-12-05 12:30:30',
                'memo' => 'Transaction #1',
                'amount' => 5000,
                'balance' => 2000
            ],
            1 => [
                '1' => 1,
                'id' => 2,
                'create_time' => '2015-12-05 12:30:30',
                'memo' => 'Transaction #2',
                'amount' => 12000,
                'balance' => 7000
            ]
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedAccount, $response['data']['account']);
        $this->assertEquals($expectedRecord, $response['data']['record']);
    }

    public function testRecordListByEmptyPage()
    {
        $validAccountId = 1;
        $pageData = [
            'pageLimit' => 2,
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "successful";
        $expectedAccount = [
            'id' => 1,
            'customerId' => 1,
            'currency' => 'USD',
            'balance' => 31000,
        ];
        $expectedRecord = [
            0 => [
                '1' => 1,
                'id' => 1,
                'create_time' => '2015-12-05 12:30:30',
                'memo' => 'Transaction #1',
                'amount' => 5000,
                'balance' => 2000
            ],
            1 => [
                '1' => 1,
                'id' => 2,
                'create_time' => '2015-12-05 12:30:30',
                'memo' => 'Transaction #2',
                'amount' => 12000,
                'balance' => 7000
            ]
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedAccount, $response['data']['account']);
        $this->assertEquals($expectedRecord, $response['data']['record']);
    }

    public function testRecordListByTotalPageEqualOne()
    {
        $validAccountId = 3;
        $pageData = [
            'page' => 1,
            'pageLimit' => 10,
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "successful";
        $expectedAccount = [
            'id' => 3,
            'customerId' => 3,
            'currency' => 'NTD',
            'balance' => 22000,
        ];
        $expectedRecord = [];
        $expectedTotalPages = 1;

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedAccount, $response['data']['account']);
        $this->assertEquals($expectedRecord, $response['data']['record']);
        $this->assertEquals($expectedTotalPages, $response['data']['totalPages']);
    }

    public function testRecordListByNotExistedPage()
    {
        $validAccountId = 1;
        $pageData = [
            'page' => 2,
            'pageLimit' => 10,
        ];

        $this->client->request('GET', '/account/'. $validAccountId .'/record', $pageData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'Not a invalid page! Please try again!',
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