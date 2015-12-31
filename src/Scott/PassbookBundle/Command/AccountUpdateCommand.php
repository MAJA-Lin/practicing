<?php

namespace Scott\PassbookBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Predis\Autoloader;
use Predis\Client;

class AccountUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('account:update')
            ->setDescription('Update account information automatically');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        Autoloader::register();
        $redis = new Client();

        $dir = __DIR__ . "/../../../../app/logs/accountUpdate/";
        $logFile = $dir . date('Y-m-d') . ".log";
        $errlogFile = $dir . date('Y-m-d') . "_error.log";

        if (!file_exists($dir)) {
            $oldmask = umask(0);
            mkdir ($dir, 0744);
        }

        $output->writeln(date('Y-m-d, H:i:s') . "\n");

        try {
            $updateTable = $redis->hgetall('updateTable');

            foreach ($updateTable as $key => $value) {
                if (preg_match('/^(version:)[1-9][0-9]*$/', $key)) {
                    list($title, $accountId) = explode("version:", $key);

                    if (!isset($updateTable['balance:' . $accountId])) {
                        $redis->hdel('updateTable', $key);
                        $message = "[Account] ID: " . $accountId . ", balance data missing.";
                        $output->writeln($message);
                        throw new \Exception($message);
                    }

                    $account = $entityManager->find('ScottPassbookBundle:Account', $accountId);

                    if (is_null($account) || empty($account)) {
                        $redis->hdel('updateTable', $key);
                        $redis->hdel('updateTable', 'balance:' . $accountId);

                        $message = "[Account] ID: " . $accountId . ", the account ID is invalid.";
                        $output->writeln($message);
                        throw new \Exception($message);
                    }

                    if ($account->getVersion() >= $updateTable[$key]) {
                        $redis->hdel('updateTable', $key);
                        $redis->hdel('updateTable', 'balance:' . $accountId);
                        continue;
                    }

                    $account->setVersion($updateTable[$key]);
                    $account->setBalance($updateTable['balance:' . $accountId]);
                    $entityManager->persist($account);
                    $entityManager->flush();

                    $updateInfo = "[Account] ID: " . $accountId . ", Balance: " . $account->getBalance();
                    $updateInfo .=  ", Version: " . $account->getVersion() . PHP_EOL;
                    $output->writeln("Account data updating...");
                    $output->writeln($updateInfo);

                    $time = date('Y-m-d, H:i:s');
                    $info = $time . " " . $updateInfo;

                    $handle = fopen($logFile, "a+");
                    fwrite($handle, $info);
                    fclose($handle);
                }
            }
        } catch (\Exception $e) {
            $time = date('Y-m-d, H:i:s');
            $info = $time . " [Error] " . $e->getMessage() . PHP_EOL;

            $handle = fopen($errlogFile, "a+");
            fwrite($handle, $info);
            fclose($handle);
        }

    }

}
