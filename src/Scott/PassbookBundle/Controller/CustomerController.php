<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Scott\PassbookBundle\Entity\Account;
use Scott\PassbookBundle\Entity\Customer;
use \Exception;

class CustomerController extends Controller
{
    /**
     * @Route("/login", name="login")
     *
     * @Method("GET")
     */
    public function loginAction(Request $request)
    {
        return $this->render('ScottPassbookBundle:Customer:login_form.html.twig');
    }

    /**
     * @Route("/login/check", name="login_check")
     *
     * @Method("POST")
     */
    public function loginCheckAction(Request $request)
    {

        $form = $request->request->get('form');
        $email = $form['email'];
        $password = $form['password'];

        $criteria = [
            'email' => $email,
            'password' => $password,
        ];
        $entityManager = $this->getDoctrine()->getManager();
        $customer = $entityManager->getRepository("ScottPassbookBundle:Customer")
            ->findOneBy($criteria);

        try {
            if (empty($customer)) {
                throw new Exception("Sorry, your email or password is wrong.");
            }
        } catch (Exception $e) {
            $result = [
                'status' => 'failed',
                'action' => 'login',
                'data' => [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]
                ]
            ];
            return $this->render('ScottPassbookBundle:Customer:login_error.html.twig', ['result' => json_encode($result)]);
        }

        $customerId = $customer->getId();
        $request->attributes->set('customerId', $customerId);

        return $this->redirectToRoute('index', [
            'page' => 1,
            'customerId' => base64_encode($customerId),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     *
     * @Method("GET")
     */
    public function logoutAction(Request $request)
    {
        return $this->redirectToRoute('login');
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

        $form = $request->request->get('form');
        $email = $form['email'];
        $passwordFirst = $form['password']['first'];
        $passwordSecond = $form['password']['second'];
        $currency = $form['currency'];

        try {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("The format of email is invalid! Try again!");
            }

            if (strlen($email) > 40) {
                throw new Exception("The length of email should be less than 50!");
            }

            if (!preg_match("/^[a-zA-Z0-9@_.-]*$/", $email)) {
                throw new Exception("Available characters are: numbers, alphabets and @_.-");
            }

            if ($passwordFirst != $passwordSecond) {
                throw new Exception("Passwords do not match! Please try again!");
            }

            if (strlen($passwordFirst) > 16) {
                throw new Exception("The length of password should be less than 16!");
            }

            if (!preg_match("/^[a-zA-Z0-9@_.-]*$/", $passwordFirst)) {
                throw new Exception("Available characters: numbers, alphabets and @_.-!");
            }

            if (!in_array($currency, $currencyArray)) {
                throw new Exception("The currency you select is invalid! Please try again!");
            }
        } catch (Exception $e) {
            $result = [
                'status' => 'failed',
                'action' => 'signup',
                'data' => [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]
                ]
            ];
            return $this->render('ScottPassbookBundle:Default:error.html.twig', ['result' => json_encode($result)]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $customer = $entityManager->getRepository("ScottPassbookBundle:Customer")
            ->findOneBy(['email' => $email]);

        try {
            if (!empty($customer)) {
                throw new Exception("The email has been registered! Try another one!");
            }
        } catch (Exception $e) {
            $result = [
                'status' => 'failed',
                'action' => 'signup',
                'data' => [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ]
                ]
            ];
            return $this->render('ScottPassbookBundle:Default:error.html.twig', ['result' => json_encode($result)]);
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
            'action' => "signup",
            'data' => [
                'customer' => $customer,
                'account' => $account,
            ]
        ];

        return $this->render('ScottPassbookBundle:Customer:signup.html.twig', ['result' => json_encode($result)]);
    }
}