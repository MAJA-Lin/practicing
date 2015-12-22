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
        $password = "dd";
        $customer = new Customer($email, $password);
        $manager->persist($customer);

        $email = "HowAboutLengthOf@WillItActsNormally.question";
        $password = "45567797";
        $customer = new Customer($email, $password);
        $manager->persist($customer);

        $email = "NOTEMAILFORMAT";
        $password = "omg";
        $customer = new Customer($email, $password);
        $manager->persist($customer);

        $email = "中文字@NotEnglish.nope";
        $password = "taiwan_no1";
        $customer = new Customer($email, $password);
        $manager->persist($customer);

        $email = "beHappy@apple.com";
        $password = "AppleIsExpensive";
        $customer = new Customer($email, $password);
        $manager->persist($customer);

        $manager->flush();
    }
}
