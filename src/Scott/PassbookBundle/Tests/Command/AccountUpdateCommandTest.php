<?php

namespace Scott\PassbookBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Tester\CommandTester;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Loader;
use Scott\PassbookBundle\Command\AccountUpdateCommand;
use Predis\Autoloader;
use Predis\Client;

 class AccountUpdateCommandTest extends WebTestCase
 {
    protected $client;

    protected $application;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->client = static::makeClient();

        //$this->runCommand('account:update --env=test');

        $this->loadFixtures([
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadCustomerData',
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadAccountData',
            'Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadRecordData'
        ]);
    }

    protected function runCommand($command)
    {
        return $this->getApplication()->run(new StringInput($command));
    }

    protected function getApplication()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);

        return $this->application;
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

        //$application = new Application();
        $this->application = new Application($kernel);
        $this->application->add(new AccountUpdateCommand());

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

        $conn = $em->getConnection();
        var_dump($conn->getHost());
        var_dump($conn->getDatabase());

        $account = $em->find("ScottPassbookBundle:Account", $validAccountId);
        $account = $account->toArray();

        $this->assertEquals($beforeUpdateAccount, $account, 'Before the updating command');

        /*
        $regex = '/^(20)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])[,][ ]';
        $regex .= '(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/';
        */

        $command = $this->application->find('account:update');
        /*
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);
        */
        $conn = $em->getConnection();
        var_dump($conn->getHost());
        var_dump($conn->getDatabase());
        echo "\nFisrt Command exec\n";

        //$this->runCommand('account:update');
        $this->runCommand('account:update --env=test');
        var_dump($redis->hgetall('updateTable'));

        /*
        $dir = __DIR__ . "/../../../../../";
        exec('php ' . $dir . 'app/console account:update --env=test');
        */
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);
        echo "\nupdateTable cleaned\n";
        var_dump($redis->hgetall('updateTable'));

        // Todo: 檢查db連線
        $conn = $em->getConnection();
        var_dump($conn->getHost());
        var_dump($conn->getDatabase());
        echo "\nThird Command exec\n";
        //$conn = DriverManager::getConnection($params, $config);
        //var_dump($)

        //$this->assertContains('[Account] ID: 4, Balance: 78970, Version: 4', $commandTester->getDisplay());
        //sleep(0);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $record = $em->find("ScottPassbookBundle:Record", 7);
        $record = $record->toArray();
        //var_dump($record);

        echo "\nFinal\n";
        $conn = $em->getConnection();
        var_dump($conn->getHost());
        var_dump($conn->getDatabase());

        $account = $em->find("ScottPassbookBundle:Account", $validAccountId);
        $account = $account->toArray();

        //$this->assertEquals($afterUpdateAccount, $account, 'After the updating command');
        /*
        $pid = pcntl_fork();

        if ($pid === -1) {
            die('Could not fork new process');
        }

        if ($pid === 0) {
            $commandTester->execute(['command' => $command->getName()]);
        }

        if ($pid != 0) {
            sleep(4);

            echo "\nThis is father\n";
            //$this->assertRegExp($regex, $commandTester->getDisplay());
            //exec("kill -2 $pid");
            posix_kill($pid, SIGINT);

            $account = $em->find("ScottPassbookBundle:Account", $validAccountId);
            $account = $account->toArray();
            $this->assertEquals($afterUpdateAccount, $account, 'After the updating command');
            //posix_kill(0, SIGINT);

            $dir = __DIR__ . "/../../../../../app/logs/accountUpdate/";
            $logFile = $dir . date('Y-m-d') . ".log";
            $handle = fopen($logFile, "r+");
            $contents = stream_get_contents($handle);
            fclose($handle);

            /*
            $time = date('Y-m-d');
            $pattern = '/^(' . $time . ',)[ ](?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)[ ]';
            $pattern .= '(Account)[ ](ID:)[ ](4,)[ ](Balance:)[ ](78970,)$/';

            $this->assertRegExp($pattern, $contents, 'message');
            //($contents, '[Account] ID: 4, Balance: 78970');
            */
            /*
            $this->assertContains(
                "2016-01-04, 17:20:32 [Account] ID: 4, Balance: 78970, Version: 4",
                $contents
            );
        }
        */
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
    }
 }