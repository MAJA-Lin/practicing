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

        $record = new Record($account1, $date, $account1->getBalance(), 5000);
        $record->setMemo("Transaction #1");
        $account1->setBalance($account1->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account1);

        $record = new Record($account1, $date, $account1->getBalance(), 12000);
        $record->setMemo("Transaction #2");
        $account1->setBalance($account1->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account1);

        $record = new Record($account1, $date, $account1->getBalance(), 12000);
        $record->setMemo("Transaction #3");
        $account1->setBalance($account1->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account1);

        $record = new Record($account2, $date, $account2->getBalance(), 7000);
        $record->setMemo("What Bank?");
        $account2->setBalance($account2->getBalance() + $record->getAmount());
        $manager->persist($record);
        $manager->persist($account2);

        $record = new Record($account2, $date, $account2->getBalance(), -2000);
        $record->setMemo("Withdraw");
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
