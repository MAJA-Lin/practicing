<?php
namespace Scott\PassbookBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Scott\PassbookBundle\Entity\Account;

class LoadAccountData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {

        $account[0] = new Account();
        $account[0]->setCurrency('USD');
        $account[0]->setBalance(2000);

        $account[1] = new Account();
        $account[1]->setCurrency('EUR');
        $account[1]->setBalance(600000);

        $account[2] = new Account();
        $account[2]->setCurrency('NTD');
        $account[2]->setBalance(22000);

        $account[3] = new Account();
        $account[3]->setCurrency('JPY');
        $account[3]->setBalance(0);

        $account[4] = new Account();
        $account[4]->setCurrency('KRW');
        $account[4]->setBalance(333000);

        for ($i=0; $i < 5; $i++) {
            $account[$i]->addCustomer($this->getReference("customer$i"));
            $customer[$i] = $manager->getRepository('ScottPassbookBundle:Customer')
                ->findBy(['id' => $account[$i]->getCustomer()]);

            $customer[$i][0]->setAccount($account[$i]);

            $this->addReference("account$i", $account[$i]);
            $manager->persist($account[$i]);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}