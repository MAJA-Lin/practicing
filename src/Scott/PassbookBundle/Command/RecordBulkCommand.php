<?php

namespace Scott\PassbookBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Scott\PassbookBundle\Entity\Record;

class RecordBulkCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('record:bulk')
            ->setDescription('Add a lot of records')
            ->addArgument(
                'accountId',
                InputArgument::REQUIRED,
                'The account ID'
            )
            ->addArgument(
                'data',
                InputArgument::OPTIONAL,
                'How many data you want to test?'
            )
            ->addArgument(
                'size',
                InputArgument::OPTIONAL,
                'How many transaction you want to deal at the same time?'
            )
            ->addOption(
                'pdo',
                null,
                InputOption::VALUE_NONE,
                'If set, the test will run by php-pdo'
            )
            ->addOption(
                'orm',
                null,
                InputOption::VALUE_NONE,
                'If set, the test will run by orm without batch'
            )
            ->addOption(
                'batch',
                null,
                InputOption::VALUE_NONE,
                'If set, the test will run by orm batch '
            )
            ->addOption(
                'dbal',
                null,
                InputOption::VALUE_NONE,
                'If set, the test will run by dbal'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        ini_set("memory_limit","4G");
        set_time_limit(6000);

        $max = $input->getArgument('data');
        $accountId = $input->getArgument('accountId');
        $size = $input->getArgument('size');

        if (is_null($max) || empty($max) || $max <= 0) {
            $output->writeln('Test with default data: 1');
            $max = 1;
        }

        if (is_null($size) || empty($size) || $size <= 0) {
            $output->writeln('Test with default size');
            $size = 0;
        }

        $dir = __DIR__ . "/../../../../app/logs/bulk/";
        $logFile = $dir . date('Y-m-d') . ".log";
        $digitFile = $dir . date('Y-m-d') . "_digit.log";

        if (!file_exists($dir)) {
            $oldmask = umask(0);
            mkdir ($dir, 0744);
        }

        if ($input->getOption('orm')) {
            $time_start = microtime(true);

            for ($i=0; $i < $max; $i++) {
                $entityManager = $this->getContainer()->get('doctrine')->getManager();
                //$account = $entityManager->find('ScottPassbookBundle:Account', $accountId);
                $account = $accountId;

                $int = mt_rand(1262055681, 1462055681);
                $time = new \Datetime('@' . $int);

                $amount = (mt_rand(0,1) * 2 - 1) * mt_rand(0, 100);
                //$newBalance = $account->getBalance() + $amount;
                $newBalance = 0;

                $record = new Record($account, $time, $newBalance, $amount);
                $record->setMemo("Mew");
                //$account->setBalance($newBalance);

                $entityManager->persist($record);
                //$entityManager->persist($account);
                $entityManager->flush();

                if ($i % 50 === 0) {
                    $output->writeln('Now Loading at ' . $i . '...');
                }
            }

            $time_end = microtime(true);
            $time_executed = $time_end - $time_start;

            $info = '[ORM without batch] Data: ' . $max . ', execution time is : ' . $time_executed;
            $output->writeln($info);

            $time = date('Y-m-d, H:i:s');
            $info = $time . $info;
        }

        if ($input->getOption('batch')) {
            $time_start = microtime(true);
            $batchSize = $size;

            for ($i=0; $i < $max; $i++) {
                $entityManager = $this->getContainer()->get('doctrine')->getManager();
                //$account = $entityManager->find('ScottPassbookBundle:Account', $accountId);
                $account = $accountId;

                $int = mt_rand(1262055681, 1462055681);
                $time = new \Datetime('@' . $int);

                $amount = (mt_rand(0,1) * 2 - 1) * mt_rand(0, 100);
                //$newBalance = $account->getBalance() + $amount;
                $newBalance = 0;

                $record = new Record($account, $time, $newBalance, $amount);
                $record->setMemo("Mew");
                //$account->setBalance($newBalance);

                $entityManager->persist($record);
                //$entityManager->persist($account);
                if (($i % $batchSize) === 0) {
                    $entityManager->flush();
                    $entityManager->clear();
                }

                if ($i % 100 === 0) {
                    $output->writeln('Now Loading at ' . $i . '...');
                }
            }

            $time_end = microtime(true);
            $time_executed = $time_end - $time_start;

            $info = '[ORM with batch (size:' . $batchSize .')] Data: ';
            $info .= $max . ', execution time is : ' . $time_executed;
            $output->writeln($info);

            $time = date('Y-m-d, H:i:s');
            $info = $time . $info;
        }

        if ($input->getOption('dbal')) {
            $time_start = microtime(true);

            for ($i=0; $i < $max; $i++) {
                $entityManager = $this->getContainer()->get('doctrine')->getManager();
                $conn = $entityManager->getConnection();

                if ($size != 0 && $i === 0) {
                    $conn->beginTransaction();
                    var_dump($conn);
                    var_dump(get_class_methods($conn));
                }

                /*
                $sql = "SELECT * FROM account WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(1, $accountId);
                $stmt->execute();
                $account = $stmt->fetch(0);
                */
                $account = $accountId;

                $int = mt_rand(1262055681, 1462055681);
                $time = date('Y-m-d H:i:s', $int);

                $amount = (mt_rand(0,1) * 2 - 1) * mt_rand(0, 100);
                //$newBalance = $account['balance'] + $amount;
                $newBalance = 0;

                $recordArray = [
                    //'account_id' => $account['id'],
                    'account_id' => $accountId,
                    'create_time' => $time,
                    'balance' => $newBalance,
                    'amount' => $amount,
                    'memo' => "Who let the dogs out?"
                ];

                $valueClause = [
                    'balance' => $newBalance,
                    'version' => $account['version'] + 1
                ];

                $whereClause = [
                    'id' => $accountId
                ];

                $conn->insert('record', $recordArray);
                //$conn->update('account', $valueClause, $whereClause);

                if (($size != 0) && ($i % $size === 0 && $i !== 0)) {
                    $conn->commit();
                    $conn->beginTransaction();
                }

                if ($i % 100 === 0) {
                    $output->writeln('Now Loading at ' . $i . '...');
                }
            }

            $time_end = microtime(true);
            $time_executed = $time_end - $time_start;

            $info = '[DBAL (Transaction = ' . $size . ')] Data: ' . $max;
            $info .= ', execution time is : ' . $time_executed;
            $output->writeln($info);

            $time = date('Y-m-d, H:i:s');
            $info = $time . $info;
        }

        if ($input->getOption('pdo')) {
            $time_start = microtime(true);

            for ($i=0; $i < $max; $i++) {
                $user = 'manager';
                $pass = 'manager5566';
                $dbh = new \PDO('mysql:host=localhost;dbname=passbook', $user, $pass);

                $sql = "SELECT * FROM account WHERE id =?";
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $accountId, \PDO::PARAM_INT);
                $stmt->execute();
                $account = $stmt->fetch(0);

                $int = mt_rand(1262055681, 1462055681);
                $time = date('Y-m-d H:i:s', $int);

                $amount = (mt_rand(0,1) * 2 - 1) * mt_rand(0, 100);
                $newBalance = $account['balance'] + $amount;

                $recordArray = [
                    'account_id' => $account['id'],
                    'create_time' => $time,
                    'balance' => $newBalance,
                    'amount' => $amount,
                    'memo' => "Who let the dogs out?"
                ];

                $valueClause = [
                    'balance' => $newBalance,
                    'version' => $account['version'] + 1
                ];

                $whereClause = [
                    'id' => $accountId
                ];

                $sql = "UPDATE account SET balance = ?, version =? WHERE id =?";
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $newBalance, \PDO::PARAM_INT);
                $stmt->bindValue(2, $account['version'] + 1, \PDO::PARAM_INT);
                $stmt->bindValue(3, $accountId, \PDO::PARAM_INT);
                $stmt->execute();

                $sql = "INSERT INTO record(account_id, create_time, balance, amount, memo) VALUES(?, ?, ?, ?, ?)";
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(1, $accountId, \PDO::PARAM_INT);
                $stmt->bindValue(2, $time, \PDO::PARAM_INT);
                $stmt->bindValue(3, $newBalance, \PDO::PARAM_INT);
                $stmt->bindValue(4, $amount, \PDO::PARAM_INT);
                $stmt->bindValue(5, 'Say my name!', \PDO::PARAM_STR);
                $stmt->execute();

                if ($i % 100 === 0) {
                    $output->writeln('Now Loading at ' . $i . '...');
                }
            }

            $time_end = microtime(true);
            $time_executed = $time_end - $time_start;

            $info = '[PHP pdo] Data: ' . $max . ', execution time is : ' . $time_executed;
            $output->writeln($info);

            $time = date('Y-m-d, H:i:s');
            $info = $time . $info;
        }

        $handle = fopen($logFile, "a+");
        $info .= PHP_EOL;
        fwrite($handle, $info);
        fclose($handle);

        $handle = fopen($digitFile, "a+");
        fwrite($handle, $time_executed . PHP_EOL);
        fclose($handle);
    }
}
