<?php

namespace Scott\PassbookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Scott\PassbookBundle\Entity as Entity;

class PassbookController extends Controller
{
    /**
     * @Route("/login", name="login")
     *
     *
     */
    public function loginAction(Request $request)
    {
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

        return $this->render('ScottPassbookBundle:Passbook:login_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login/check", name="login_check")
     *
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
            return $this->render('ScottPassbookBundle:Passbook:login.html.twig', [
                'login' => 'successful',
                'email' => $email,
            ]);
        } else {
            return $this->render('ScottPassbookBundle:Passbook:login.html.twig', [
                'login' => 'failed',
            ]);
        }
    }

    /**
     * @Route("/logout", name="logout")
     *
     * @Method("POST")
     *
     */
    public function logoutAction(Request $request)
    {

    }

    /**
     * @Route("/signup", name="signup")
     *
     *
     */
    public function signupAction(Request $request)
    {
        //$signup = new Entity\Signup();
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
                'text',
                ['attr' => ['maxlength' => 10]
            ])
            ->add(
                'signup',
                'submit',
                ['label' => 'Sign up']
            )
            ->getForm();

        return $this->render('ScottPassbookBundle:Passbook:signup_form.html.twig', [
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

        if ($form['password']['first'] == $form['password']['second']) {
            $entityManager = $this->getDoctrine()->getManager();
            $customer = $entityManager
                ->getRepository("ScottPassbookBundle:Customer")
                ->findBy(['email' => $form['email']]);

            if (empty($customer)) {
                $customer = new Entity\Customer;
                $account = new Entity\Account;

                $customer->setEmail($form['email']);
                $customer->setPassword($form['password']['first']);

                $entityManager->persist($customer);
                $entityManager->flush();

                $account->setCurrency($form['currency']);
                $account->addCustomer($customer);
                $account->setBalance(0);
                $customer->setAccount($account);

                $entityManager->persist($account, $customer);
                $entityManager->flush();

            } else {
                return $this->render('ScottPassbookBundle:Passbook:signup_error.html.twig', [
                    'error' => 'email',
                ]);
            }

        } else {
            return $this->render('ScottPassbookBundle:Passbook:signup_error.html.twig', [
                    'error' => 'password',
                ]);
        }

        return $this->render('ScottPassbookBundle:Passbook:signup.html.twig', [
                'request' => $request,
                'customer' => $customer,
                'account' => $account,
            ]);
    }

}