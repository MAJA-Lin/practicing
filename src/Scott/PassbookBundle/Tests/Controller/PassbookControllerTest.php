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
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadCustomerData',
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadAccountData',
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadRecordData'
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

    public function testRecordAddByValidData()
    {
        $validAccountId = 4;
        $validData = [
            'amount' => 81000,
            'memo' => 'First Bill'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $validData);
        $expectedTime = new \DateTime();
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "successful";
        $expectedAccount = [
            'id' => 4,
            'customerId' => 4,
            'currency' => 'JPY',
            'balance' => 81000
        ];
        $expectedRecord = [
            'id' => 6,
            'accountId' => 4,
            'create_time' => get_object_vars($expectedTime),
            'balance' => 0,
            'amount' => 81000,
            'memo' => 'First Bill'
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedAccount, $response['data']['account']);
        $this->assertEquals($expectedRecord, $response['data']['record']);
    }

    public function testRecordAddByInvalidAmountLength()
    {
        $validAccountId = 4;
        $invalidData = [
            'amount' => 160049006894200.42,
            'memo' => 'Save'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $invalidData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'Length of amount must be less than 12! Try again!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordAddByInvalidAmountFormat()
    {
        $validAccountId = 4;
        $invalidData = [
            'amount' => 3.14159,
            'memo' => 'pi'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $invalidData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The amount must be a float and digits after decimal point must be less than 2!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordAddByInvalidCharacterAmount()
    {
        $validAccountId = 4;
        $invalidData = [
            'amount' => 'No money',
            'memo' => 'pi'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $invalidData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The amount must be a float and digits after decimal point must be less than 2!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordAddByInvalidNullAmount()
    {
        $validAccountId = 4;
        $invalidData = [
            'amount' => null,
            'memo' => 'test'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $invalidData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The amount must be a float and digits after decimal point must be less than 2!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordAddByInvalidEmptyAmount()
    {
        $validAccountId = 4;
        $invalidData = [
            'amount' => '',
            'memo' => 'test'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $invalidData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The amount must be a float and digits after decimal point must be less than 2!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordAddByInvalidZeroAmount()
    {
        $validAccountId = 4;
        $invalidData = [
            'amount' => '0.0',
            'memo' => 'test'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $invalidData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'One does not simply save or withdraw 0 dollar.',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordAddByInvalidMemoLength()
    {
        $validAccountId = 4;
        $invalidData = [
            'amount' => '500',
            'memo' => '臣亮言：先帝創業未半，而中道崩殂。今天下三分，益州 疲弊，此誠危急存亡之秋也。'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $invalidData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The length of Memo should be less than 50!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordAddByInvalidAccountId()
    {
        $invalidAccountId = 40;
        $validData = [
            'amount' => -1600,
            'memo' => 'Withdrawing'
        ];

        $this->client->request('POST', '/account/'. $invalidAccountId .'/record', $validData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The account is invalid. Please try again!',
            'code' => 0
        ];

        $this->assertEquals($expectedStatus, $response['status']);
        $this->assertEquals($expectedError, $response['error']);
    }

    public function testRecordAddByInvalidWithdrawing()
    {
        $validAccountId = 4;
        $invalidData = [
            'amount' => -1600000,
            'memo' => 'Withdrawing'
        ];

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $invalidData);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $expectedStatus = "failed";
        $expectedError = [
            'message' => 'The number you are withdrawing is too big!',
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