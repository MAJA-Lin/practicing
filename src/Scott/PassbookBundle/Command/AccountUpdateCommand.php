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

        while (1) {
            $output->writeln(date('Y-m-d, H:i:s') . "\n");

            try {
                $allAccount = $redis->keys('account:*');

                foreach ($allAccount as $value) {
                    list($title, $accountId) = explode(":", $value);
                    $account = $entityManager->find('ScottPassbookBundle:Account', $accountId);

                    if (empty($account) || is_null($account)) {
                        $redis->del('account:' . $accountId);
                        continue;
                    }

                    $redisAccount = $redis->hgetall($value);

                    if ($redisAccount['version'] > $account->getVersion()) {
                        $account->setBalance($redisAccount['balance']);
                        $account->setVersion($redisAccount['version']);
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

            } catch (Exception $e) {
                $time = date('Y-m-d, H:i:s');
                $info = $time . " [Error] " . $e->getMessage() . PHP_EOL;

                $handle = fopen($errlogFile, "a+");
                fwrite($handle, $info);
                fclose($handle);
            }

            sleep(1);
        }
    }

}
