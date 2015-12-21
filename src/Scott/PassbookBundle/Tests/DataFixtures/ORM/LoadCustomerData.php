<?php
namespace Scott\PassbookBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Scott\PassbookBundle\Entity\Customer;

class LoadCustomerData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $email = "dora@co.jp";
        $customer[0] = new Customer();
        $customer[0]->setEmail($email);
        $customer[0]->setPassword('dd');

        $email = "HowAboutLengthOf@WillItActsNormally.question";
        $customer[1] = new Customer();
        $customer[1]->setEmail($email);
        $customer[1]->setPassword('45567797');

        $email = "NOTEMAILFORMAT";
        $customer[2] = new Customer();
        $customer[2]->setEmail($email);
        $customer[2]->setPassword('omg');

        $email = "中文字@NotEnglish.nope";
        $customer[3] = new Customer();
        $customer[3]->setEmail($email);
        $customer[3]->setPassword('taiwan_no1');

        $email = "beHappy@apple.com";
        $customer[4] = new Customer();
        $customer[4]->setEmail($email);
        $customer[4]->setPassword('AppleIsExpensive');

        for ($i=0; $i < 5; $i++) {
            $manager->persist($customer[$i]);
            $this->addReference("customer$i", $customer[$i]);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
