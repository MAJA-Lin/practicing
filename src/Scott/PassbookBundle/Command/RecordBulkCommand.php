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
        ini_set("memory_limit","8G");
        set_time_limit(6000);

        $max = $input->getArgument('data');
        $accountId = $input->getArgument('accountId');
        $size = $input->getArgument('size');
        $int = mt_rand(1262055681, 1462055681);
        $time = new \Datetime('@' . $int);
        $amount = (mt_rand(0,1) * 2 - 1) * mt_rand(0, 100);
        $newBalance = 0;

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
        $speedFile = $dir . date('Y-m-d') . "_speed.log";

        if (!file_exists($dir)) {
            $oldmask = umask(0);
            mkdir ($dir, 0744);
        }

        if ($input->getOption('orm')) {
            $time_start = microtime(true);

            for ($i=0; $i < $max; $i++) {
                $entityManager = $this->getContainer()->get('doctrine')->getManager();

                $record = new Record($accountId, $time, $newBalance, $amount);
                $record->setMemo("Mew");

                $entityManager->persist($record);
                $entityManager->flush();

                if ($i % 50 === 0) {
                    $output->writeln('Now Loading at ' . $i . '...');
                }
            }

            $time_end = microtime(true);
            $time_executed = $time_end - $time_start;
            $time_ran = $time_executed / $max;

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

                $record = new Record($accountId, $time, $newBalance, $amount);
                $record->setMemo("Mew");

                $entityManager->persist($record);

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
            $time_ran = $time_executed / $max;

            $info = '[ORM with batch (size:' . $batchSize .')] Data: ';
            $info .= $max . ', execution time is : ' . $time_executed;
            $output->writeln($info);

            $time = date('Y-m-d, H:i:s');
            $info = $time . $info;
        }

        if ($input->getOption('dbal')) {
            $time_start = microtime(true);
            $time = date('Y-m-d H:i:s', $int);

            for ($i=0; $i < $max; $i++) {
                $entityManager = $this->getContainer()->get('doctrine')->getManager();
                $conn = $entityManager->getConnection();

                if ($size != 0 && $i === 0) {
                    $conn->beginTransaction();
                }

                $recordArray = [
                    'account' => $accountId,
                    'create_time' => $time,
                    'balance' => $newBalance,
                    'amount' => $amount,
                    'memo' => "Who let the dogs out?"
                ];

                $valueClause = [
                    'balance' => $newBalance,
                ];

                $whereClause = [
                    'id' => $accountId
                ];

                $conn->insert('record', $recordArray);

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
            $time_ran = $time_executed / $max;

            $info = '[DBAL (Transaction = ' . $size . ')] Data: ' . $max;
            $info .= ', execution time is : ' . $time_executed;
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

        $handle = fopen($speedFile, "a+");
        fwrite($handle, $time_ran . PHP_EOL);
        fclose($handle);
    }
}
