<?php
namespace Scott\PassbookBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Scott\PassbookBundle\Entity\Record;

class LoadRecordData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $account = $manager->getRepository('ScottPassbookBundle:Account')
                        ->findALL();
        $date = new \DateTime('2015-12-05');
        $date->setTime(12, 30, 30);

        $record[0] = new Record();
        $record[0]->setAmount(5000);
        $record[0]->setMemo("Transaction #1");
        $record[0]->setCreateTime($date);
        $record[0]->setAccount($account[0]);
        $record[0]->setBalance($account[0]->getBalance());
        $account[0]->setBalance($account[0]->getBalance() + $record[0]->getAmount());

        $record[1] = new Record();
        $record[1]->setAmount(12000);
        $record[1]->setMemo("Transaction #2");
        $record[1]->setCreateTime($date);
        $record[1]->setAccount($account[0]);
        $record[1]->setBalance($account[0]->getBalance());
        $account[0]->setBalance($account[0]->getBalance() + $record[1]->getAmount());

        $record[2] = new Record();
        $record[2]->setAmount(12000);
        $record[2]->setMemo("Transaction #3");
        $record[2]->setCreateTime($date);
        $record[2]->setAccount($account[0]);
        $record[2]->setBalance($account[0]->getBalance());
        $account[0]->setBalance($account[0]->getBalance() + $record[2]->getAmount());

        $record[3] = new Record();
        $record[3]->setAmount(7000);
        $record[3]->setMemo("What Bank?");
        $record[3]->setCreateTime($date);
        $record[3]->setAccount($account[1]);
        $record[3]->setBalance($account[1]->getBalance());
        $account[1]->setBalance($account[1]->getBalance() + $record[3]->getAmount());

        $record[4] = new Record();
        $record[4]->setAmount(-2000);
        $record[4]->setMemo("Withdraw");
        $record[4]->setCreateTime($date);
        $record[4]->setAccount($account[1]);
        $record[4]->setBalance($account[1]->getBalance());
        $account[1]->setBalance($account[1]->getBalance() + $record[4]->getAmount());

        for ($i=0; $i < 5; $i++) {
            $manager->persist($record[$i]);
            $manager->persist($account[$i]);
            $this->addReference("record$i", $record[$i]);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}