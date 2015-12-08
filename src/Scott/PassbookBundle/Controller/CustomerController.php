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
     * @Route("/login", name="login")
     * @Method("GET")
     *
     */
    public function loginAction(Request $request)
    {
        return $this->render('ScottPassbookBundle:Customer:login_form.html.twig');
    }

    /**
     * @Route("/login/check", name="login_check")
     * @Method("POST")
     *
     */
    public function loginCheckAction(Request $request)
    {

        $form = $request->request->get('form');
        $email = $form['email'];
        $password = $form['password'];

        $entityManager = $this->getDoctrine()->getManager();
        $customer = $entityManager
            ->getRepository("ScottPassbookBundle:Customer")
            ->findOneBy([
                'email' => $email,
                'password' => $password,
            ]);

        if (!empty($customer)) {
            $customerId = $customer->getId();
            $request->attributes->set('customerId', $customerId);

            return $this->redirectToRoute('index', [
                'page' => 1,
                'customerId' => base64_encode($customerId),
            ]);
        }

        return $this->render('ScottPassbookBundle:Customer:login_error.html.twig', [
            'error' => 'login_failed',
        ]);

    }

    /**
     * @Route("/logout", name="logout")
     * @Method("GET")
     *
     */
    public function logoutAction(Request $request)
    {
        $request->query->remove('customerId');
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/signup", name="signup")
     * @Method("GET")
     */
    public function signupAction(Request $request)
    {
        return $this->render('ScottPassbookBundle:Customer:signup_form.html.twig');
    }

    /**
     * @Route("/signup/check", name="signup_check")
     * @Method("POST")
     *
     */
    public function signupCheckAction(Request $request)
    {

        $currencyArray = ["NTD", "USD", "JPY", "EUR"];

        $form = $request->request->get('form');
        $email = $form['email'];
        $passwordFirst = $form['password']['first'];
        $passwordSecond = $form['password']['second'];
        $currency = $form['currency'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'email',
                    'detail' => 'format',
            ]);
        }

        if (strlen($email) > 40) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'email',
                    'detail' => 'length',
            ]);
        }

        if (!preg_match("/^[a-zA-Z0-9@_.-]*$/", $email)) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'email',
                    'detail' => 'character',
            ]);
        }

        if ($passwordFirst != $passwordSecond) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'password',
                    'detail' => 'repeat',
            ]);
        }

        if (strlen($passwordFirst) > 16) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'password',
                    'detail' => 'length',
            ]);
        }

        if (!preg_match("/^[a-zA-Z0-9@_.-]*$/", $passwordFirst)) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'password',
                    'detail' => 'format',
            ]);
        }

        if (!in_array($currency, $currencyArray)) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'currency',
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $customer = $entityManager
            ->getRepository("ScottPassbookBundle:Customer")
            ->findOneBy(['email' => $email]);

        if (!empty($customer)) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                'error' => 'email',
                'detail' => 'existed',
            ]);
        }

        $customer = new Customer();
        $account = new Account();

        $customer->setEmail($email);
        $customer->setPassword($passwordFirst);

        $entityManager->persist($customer);
        $entityManager->flush();

        $account->setCurrency($currency);
        $account->setBalance(0);
        $customer->setAccount($account);

        $entityManager->persist($account);
        $entityManager->persist($customer);
        $entityManager->flush();

        return $this->render('ScottPassbookBundle:Customer:signup.html.twig', [
                'customer' => $customer,
                'account' => $account,
            ]);
    }
}