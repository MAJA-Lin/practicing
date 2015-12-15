<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Scott\PassbookBundle\Entity\Account;
use Scott\PassbookBundle\Entity\Customer;

class CustomerController extends Controller
{
    /**
     * @Route("/login/check", name="login_check")
     *
     * @Method("POST")
     */
    public function loginCheckAction(Request $request)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $criteria = [
            'email' => $email,
            'password' => $password,
        ];
        $entityManager = $this->getDoctrine()->getManager();
        $customer = $entityManager->getRepository("ScottPassbookBundle:Customer")
            ->findOneBy($criteria);

        try {
            if (empty($customer)) {
                throw new \Exception("Sorry, your email or password is wrong.");
            }

            $result = [
                'status' => 'successful',
                'data' => ['customerId' => $customer->getId()]
            ];
        } catch (\Exception $e) {
            $result = [
                'status' => 'failed',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]
            ];
        }
        return new Response(json_encode($result));
    }

    /**
     * @Route("/signup", name="signup")
     *
     * @Method("GET")
     */
    public function signupAction(Request $request)
    {
        return $this->render('ScottPassbookBundle:Customer:signup_form.html.twig');
    }

    /**
     * @Route("/signup/check", name="signup_check")
     *
     * @Method("POST")
     */
    public function signupCheckAction(Request $request)
    {
        $currencyArray = ["NTD", "USD", "JPY", "EUR"];

        $email = $request->request->get('email');
        $passwordFirst = $request->request->get('passwordFirst');
        $passwordSecond =$request->request->get('passwordSecond');
        $currency = $request->request->get('currency');

        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("The format of email is invalid! Try again!");
            }

            if (strlen($email) > 40) {
                throw new \Exception("The length of email should be less than 40!");
            }

            if (!preg_match("/^[a-zA-Z0-9@_.-]*$/", $email)) {
                throw new \Exception("Available characters are: numbers, alphabets and @_.-");
            }

            if ($passwordFirst != $passwordSecond) {
                throw new \Exception("Passwords do not match! Please try again!");
            }

            if (strlen($passwordFirst) > 16) {
                throw new \Exception("The length of password should be less than 16!");
            }

            if (!preg_match("/^[a-zA-Z0-9@_.-]*$/", $passwordFirst)) {
                throw new \Exception("Available characters: numbers, alphabets and @_.-!");
            }

            if (!in_array($currency, $currencyArray)) {
                throw new \Exception("The currency you select is invalid! Please try again!");
            }

            $entityManager = $this->getDoctrine()->getManager();
            $customer = $entityManager->getRepository("ScottPassbookBundle:Customer")
                ->findOneBy(['email' => $email]);

            if (!empty($customer)) {
                throw new \Exception("The email has been registered! Try another one!");
            }

            $customer = new Customer();
            $account = new Account();

            $customer->setEmail($email);
            $customer->setPassword($passwordFirst);

            $entityManager->persist($customer);
            $entityManager->flush();

            $account->setCurrency($currency);
            $customer->setAccount($account);

            $entityManager->persist($account);
            $entityManager->persist($customer);
            $entityManager->flush();

            $customer = $customer->toArray();
            $account = $account->toArray();

            $result = [
                'status' => "successful",
                'data' => [
                    'customer' => $customer,
                    'account' => $account,
                ]
            ];
        } catch (\Exception $e) {
            $result = [
                'status' => 'failed',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]
            ];
        }

        return new Response(json_encode($result));
    }

}