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
        $account = new Account();
        $account->setCurrency('USD');
        $account->setBalance(2000);
        $customer = $manager->find('ScottPassbookBundle:Customer', 1);
        $customer->setAccount($account);
        $manager->persist($account);

        $account = new Account();
        $account->setCurrency('EUR');
        $account->setBalance(600000);
        $customer = $manager->find('ScottPassbookBundle:Customer', 2);
        $customer->setAccount($account);
        $manager->persist($account);

        $account = new Account();
        $account->setCurrency('NTD');
        $account->setBalance(22000);
        $customer = $manager->find('ScottPassbookBundle:Customer', 3);
        $customer->setAccount($account);
        $manager->persist($account);

        $account = new Account();
        $account->setCurrency('JPY');
        $account->setBalance(0);
        $customer = $manager->find('ScottPassbookBundle:Customer', 4);
        $customer->setAccount($account);
        $manager->persist($account);

        $account = new Account();
        $account->setCurrency('KRW');
        $account->setBalance(333000);
        $customer = $manager->find('ScottPassbookBundle:Customer', 5);
        $customer->setAccount($account);
        $manager->persist($account);

        $manager->flush();
    }

    public function getDependencies()
    {
        return ['Scott\PassbookBundle\Tests\DataFixtures\ORM\LoadCustomerData'];
    }
}
