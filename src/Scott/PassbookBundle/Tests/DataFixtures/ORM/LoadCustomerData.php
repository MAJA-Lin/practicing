<?php
namespace Scott\PassbookBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Scott\PassbookBundle\Entity\Customer;

class LoadCustomerData extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $email = "dora@co.jp";
        $customer = new Customer();
        $customer->setEmail($email);
        $customer->setPassword('dd');
        $manager->persist($customer);

        $email = "HowAboutLengthOf@WillItActsNormally.question";
        $customer = new Customer();
        $customer->setEmail($email);
        $customer->setPassword('45567797');
        $manager->persist($customer);

        $email = "NOTEMAILFORMAT";
        $customer = new Customer();
        $customer->setEmail($email);
        $customer->setPassword('omg');
        $manager->persist($customer);

        $email = "中文字@NotEnglish.nope";
        $customer = new Customer();
        $customer->setEmail($email);
        $customer->setPassword('taiwan_no1');
        $manager->persist($customer);

        $email = "beHappy@apple.com";
        $customer = new Customer();
        $customer->setEmail($email);
        $customer->setPassword('AppleIsExpensive');
        $manager->persist($customer);

        $manager->flush();
    }
}
