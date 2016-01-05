<?php

namespace Scott\PassbookBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Scott\PassbookBundle\Command\AccountUpdateCommand;
use Predis\Autoloader;
use Predis\Client;

 class AccountUpdateCommandTest extends WebTestCase
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

    public function testExecuteWithInvalidUpdateTable()
    {
        Autoloader::register();
        $redis = new Client();

        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new AccountUpdateCommand());

        $redis->del('updateTable');
        $redis->hset('updateTable', 'version:2', 40);

        $command = $application->find('account:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $expectedError = "[Account] ID: 2, balance data missing.";
        $this->assertContains($expectedError ,$commandTester->getDisplay());
    }

    public function testExecuteWithInvalidAccountId()
    {
        Autoloader::register();
        $redis = new Client();

        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new AccountUpdateCommand());

        $redis->del('updateTable');
        $redis->hset('updateTable', 'version:100', 40);
        $redis->hset('updateTable', 'balance:100', 40);

        $command = $application->find('account:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $expectedError = "[Account] ID: 100, the account ID is invalid.";
        $this->assertContains($expectedError ,$commandTester->getDisplay());
    }

    public function testExecuteSuccessful()
    {
        Autoloader::register();
        $redis = new Client();

        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new AccountUpdateCommand());

        $validAccountId = 4;
        $validData1 = [
            'amount' => 81000,
            'memo' => 'First Bill'
        ];

        $validData2 = [
            'amount' => -2000,
            'memo' => 'Moment'
        ];

        $validData3 = [
            'amount' => -30,
            'memo' => 'Black Tea'
        ];

        $beforeUpdateAccount = [
            'id' => 4,
            'customerId' => 4,
            'currency' => 'JPY',
            'balance' => 0
        ];

        $afterUpdateAccount = [
            'id' => 4,
            'customerId' => 4,
            'currency' => 'JPY',
            'balance' => 78970
        ];

        $redis->del('account:' . $validAccountId);
        $redis->del('updateTable');

        $this->client->request('POST', '/account/'. $validAccountId .'/record', $validData1);
        $this->client->request('POST', '/account/'. $validAccountId .'/record', $validData2);
        $this->client->request('POST', '/account/'. $validAccountId .'/record', $validData3);

        $em = $this->getContainer()->get('doctrine')->getManager();

        $account = $em->find("ScottPassbookBundle:Account", $validAccountId);
        $account = $account->toArray();

        $this->assertEquals($beforeUpdateAccount, $account, 'Before the updating command');

        $command = $application->find('account:update');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $account = $em->find("ScottPassbookBundle:Account", $validAccountId);
        $account = $account->toArray();

        $this->assertEquals($afterUpdateAccount, $account, 'After the updating command');
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
 }