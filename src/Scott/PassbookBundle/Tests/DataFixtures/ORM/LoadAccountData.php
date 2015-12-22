<?php
namespace Scott\PassbookBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Scott\PassbookBundle\Entity\Account;

class LoadAccountData extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $customer = $manager->find('ScottPassbookBundle:Customer', 1);
        $account = new Account($customer, 'USD');
        $account->setBalance(2000);
        $manager->persist($account);

        $customer = $manager->find('ScottPassbookBundle:Customer', 2);
        $account = new Account($customer, 'EUR');
        $account->setBalance(600000);
        $manager->persist($account);

        $customer = $manager->find('ScottPassbookBundle:Customer', 3);
        $account = new Account($customer, 'NTD');
        $account->setBalance(22000);
        $manager->persist($account);

        $customer = $manager->find('ScottPassbookBundle:Customer', 4);
        $account = new Account($customer, 'JPY');
        $account->setBalance(0);
        $manager->persist($account);

        $customer = $manager->find('ScottPassbookBundle:Customer', 5);
        $account = new Account($customer, 'KRW');
        $account->setBalance(333000);
        $manager->persist($account);

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadCustomerData'];
    }
}
