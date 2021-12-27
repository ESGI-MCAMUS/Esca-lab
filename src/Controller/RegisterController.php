<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class RegisterController extends AbstractController
{
    #[Route('/inscription-old', name: 'register-old')]
    public function index(): Response
    {
        // Register user to database
        if (isset($_POST['register'])) {
            // Form data
            $firstname = htmlspecialchars($_POST['firstname']);
            $lastname = htmlspecialchars($_POST['lastname']);
            $birthdate = htmlspecialchars($_POST['birthdate']);
            $username = htmlspecialchars($_POST['username']);
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);
            $passwordConfirm = htmlspecialchars($_POST['passwordConfirm']);

            $userInfo = [
                'firstname' => $firstname,
                'lastname' => $lastname,
                'birthdate' => $birthdate,
                'username' => $username,
                'email' => $email,
            ];

            // Form verification
            $errors = [];

            date_diff(
                date_create($birthdate),
                date_create(date('Y-m-d'))
            )->format('%y') < 13
                ? ($errors['age'] = true)
                : null; // Check if user is 13yo+

            strlen($username) < 3 || strlen($username) > 15
                ? ($errors['usernameLength'] = strlen($username))
                : null; // Checking username length

            !preg_match(
                // Checking password
                '/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$ %^&*-]).{8,}$/',
                $password
            )
                ? ($errors['password'] = true)
                : null; // Checking for password requirements

            $password !== $passwordConfirm
                ? ($errors['passwordMissmatch'] = true)
                : null; // Checking password missmatch

            $user_byUsername = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['username' => $username]);
            $user_byEmail = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['email' => $email]);

            $user_byUsername ? ($errors['username'] = true) : null; // Checking if username exists in database

            $user_byEmail ? ($errors['email'] = true) : null; // Checking if email exists in database

            if (count($errors) !== 0) {
                // dump($errors);
                // dump($userInfo);
                return $this->render('register/index.html.twig', [
                    'errors' => $errors,
                    'userInfo' => $userInfo,
                ]);
            } else {
                $manager = $this->getDoctrine()->getManager();
                $user = new User();
                $user->setFirstname($firstname);
                $user->setLastname($lastname);
                $user->setBirthdate(date_create($birthdate));
                $user->setUsername($username);
                $user->setEmail($email);
                $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
                $user->setRoles(['ROLE_USER']);
                $user->setCreatedAt(date_create());
                $user->setIsDeleted(false);
                $user->setIsActivated(false);
                $utils = new UtilsController();
                $user->setOtp($utils->generateOTP());
                $manager->persist($user);
                $manager->flush();

                $this->get('session')->set('email', $user->getEmail());

                $mail = new MailerController();
                $mail->sendEmailOTP(
                    $user->getEmail(),
                    $user->getOtp(),
                    $user->getFirstname()
                );

                return $this->redirectToRoute('otpConfirmCreateAccount');
            }
        }
    }

    #[Route('/inscription/otp', name: 'otpConfirmCreateAccount')]
    public function otpConfirmCreateAccount(): Response
    {
        if (!$this->get('session')->get('email')) {
            return $this->redirectToRoute('register');
        }
        if (isset($_POST['checkOTP'])) {
            $otp = htmlspecialchars($_POST['otp']);

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager
                ->getRepository(User::class)
                ->findBy(['email' => $this->get('session')->get('email')]);

            if ($user[0]->getOtp() == $otp) {
                $user[0]->setOtp(null);
                $user[0]->setIsActivated(true);
                $entityManager->flush();
                return $this->redirectToRoute('login');
            } else {
                dump($user[0]->getOtp());
                return $this->render('register/otp.html.twig', [
                    'error' => 'otp',
                    'email' => $this->get('session')->get('email'),
                ]);
            }
        }
        return $this->render('register/otp.html.twig', [
            'email' => $this->get('session')->get('email'),
        ]);

        //dump($this->get('session')->get('email'));
        return $this->render('register/index.html.twig', [
            'errors' => [],
        ]);
    }

    #[Route('/inscription/otp', name: 'otpConfirmCreateAccount')]
    public function otpConfirmCreateAccountT(): Response
    {
        if (!$this->get('session')->get('email') && !$_GET["email"]) {
            return $this->redirectToRoute('register');
        }
        if (isset($_POST['checkOTP']) || (isset($_GET["email"]) && isset($_GET["otp"]))) {
            $otp = /**htmlspecialchars($_GET['otp']) ?? */ htmlspecialchars($_POST['otp']);
            $email = $_GET["email"] ?? $this->get('session')->get('email');

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager
                ->getRepository(User::class)
                ->findBy(['email' => $email]);

            if ($user[0]->getOtp() == $otp) {
                $user[0]->setOtp(null);
                $user[0]->setIsActivated(true);
                $entityManager->flush();
                return $this->redirectToRoute('login');
            } else {
                dump($user[0]->getOtp());
                return $this->render('register/otp.html.twig', [
                    'error' => 'otp',
                    'email' => $this->get('session')->get('email'),
                ]);
            }
        }
        return $this->render('register/otp.html.twig', [
            'email' => $this->get('session')->get('email'),
        ]);
    }
}