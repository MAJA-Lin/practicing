<?php
namespace Scott\PassbookBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Scott\PassbookBundle\Entity\Record;

class LoadRecordData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime('2015-12-05 12:30:30');

        $account1 = $manager->find('ScottPassbookBundle:Account', 1);
        $account2 = $manager->find('ScottPassbookBundle:Account', 2);

        $record = new Record();
        $record->setAmount(5000);
        $record->setMemo("Transaction #1");
        $record->setCreateTime($date);
        $record->setAccount($account1);
        $record->setBalance($account1->getBalance());
        $account1->setBalance($account1->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account1);

        $record = new Record();
        $record->setAmount(12000);
        $record->setMemo("Transaction #2");
        $record->setCreateTime($date);
        $record->setAccount($account1);
        $record->setBalance($account1->getBalance());
        $account1->setBalance($account1->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account1);

        $record = new Record();
        $record->setAmount(12000);
        $record->setMemo("Transaction #3");
        $record->setCreateTime($date);
        $record->setAccount($account1);
        $record->setBalance($account1->getBalance());
        $account1->setBalance($account1->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account1);

        $record = new Record();
        $record->setAmount(7000);
        $record->setMemo("What Bank?");
        $record->setCreateTime($date);
        $record->setAccount($account2);
        $record->setBalance($account2->getBalance());
        $account2->setBalance($account2->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account2);

        $record = new Record();
        $record->setAmount(-2000);
        $record->setMemo("Withdraw");
        $record->setCreateTime($date);
        $record->setAccount($account2);
        $record->setBalance($account2->getBalance());
        $account2->setBalance($account2->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account2);

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadAccountData'];
    }
}
