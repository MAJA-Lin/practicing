<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Scott\PassbookBundle\Entity as Entity;

class CustomerController extends Controller
{
    /**
     * @Route("/login", name="login")
     *
     *
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $loginBefore = $session->get('customer');

        if (isset($loginBefore) && !empty($loginBefore)) {
            return $this->redirectToRoute('index');
        }

        $customer = new Entity\Customer();

        $form = $this->createFormBuilder($customer)
            ->setMethod('POST')
            ->setAction($this->generateUrl('login_check'))
            ->add(
                'email',
                'email',
                ['attr' => ['maxlength' => 40]
            ])
            ->add(
                'password',
                'password',
                ['attr' => ['maxlength' => 16]
            ])
            ->add(
                'login',
                'submit',
                ['label' => 'Login']
            )
            ->getForm();

        return $this->render('ScottPassbookBundle:Customer:login_form.html.twig', [
            'form' => $form->createView(),
            'session' => $loginBefore,
        ]);

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
            ->findBy([
                'email' => $email,
                'password' => $password,
            ]);

        if (!empty($customer)) {
            $session = $request->getSession();
            $session->set('customer', $customer);

            return $this->redirectToRoute('index');
        }

        return $this->render('ScottPassbookBundle:Customer:login_error.html.twig', [
            'error' => 'login_failed',
        ]);

    }

    /**
     * @Route("/logout", name="logout")
     *
     *
     */
    public function logoutAction(Request $request)
    {
        $session = $request->getSession();
        $session->clear();
        //$cookie = $request->headers->clearCookie();
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/signup", name="signup")
     *
     */
    public function signupAction(Request $request)
    {
        $signup = [];
        $form = $this->createFormBuilder($signup)
            ->setMethod('POST')
            ->setAction($this->generateUrl('signup_check'))
            ->add(
                'email',
                'email', [
                    'attr' => ['maxlength' => 40]
                ])
            ->add(
                'password',
                'repeated', [
                    'type' => 'password',
                    'invalid_message' => 'The password fields must match.',
                    'options' => ['attr' => [
                        'class' => 'password-field',
                        'maxlength' => 16
                    ]],
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                ])
            ->add(
                'currency',
                'choice', [
                    'choices' => [
                        'NTD' => 'NTD',
                        'USD' => 'USD',
                        'JPY' => 'JPY',
                        'EUR' => 'EUR',
                    ]
            ])
            ->add(
                'signup',
                'submit',
                ['label' => 'Sign up']
            )
            ->getForm();

        return $this->render('ScottPassbookBundle:Customer:signup_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/signup/check", name="signup_check")
     * @Method("POST")
     *
     */
    public function signupCheckAction(Request $request)
    {

        $form = $request->request->get('form');
        $email = $form['email'];
        $passwordFirst = $form['password']['first'];
        $passwordSecond = $form['password']['second'];
        $currency = $form['currency'];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 40) {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'email',
            ]);
        }

        if ($passwordFirst == $passwordSecond && strlen($passwordFirst) <16) {
            $entityManager = $this->getDoctrine()->getManager();
            $customer = $entityManager
                ->getRepository("ScottPassbookBundle:Customer")
                ->findBy(['email' => $email]);

            if (empty($customer)) {
                $customer = new Entity\Customer;
                $account = new Entity\Account;

                $customer->setEmail($email);
                $customer->setPassword($passwordFirst);

                $entityManager->persist($customer);
                $entityManager->flush();

                $account->setCurrency($currency);
                $account->addCustomer($customer);
                $account->setBalance(0);
                $customer->setAccount($account);

                $entityManager->persist($account, $customer);
                $entityManager->flush();

            } else {
                return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'email',
                ]);
            }

        } else {
            return $this->render('ScottPassbookBundle:Customer:signup_error.html.twig', [
                    'error' => 'password',
                ]);
        }

        return $this->render('ScottPassbookBundle:Customer:signup.html.twig', [
                'request' => $request,
                'customer' => $customer,
                'account' => $account,
            ]);
    }



}
