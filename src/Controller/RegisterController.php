<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'register')]
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
                $user->setRole(1);
                $user->setCreatedAt(date_create());
                $user->setIsDeleted(false);
                $user->setIsActivated(false);
                $user->setOtp($this->generateOTP());
                $manager->persist($user);
                $manager->flush();

                $this->get('session')->set('email', $user->getEmail());

                $mail = new MailerController();
                $mail->sendEmailOTP(
                    $user->getEmail(),
                    $user->getOtp(),
                    $user->getFirstname()
                );

                return $this->redirectToRoute('otpConfirm');
            }
        }

        dump($this->get('session')->get('email'));
        return $this->render('register/index.html.twig', [
            'errors' => [],
        ]);
    }

    #[Route('/inscription/otp', name: 'otpConfirm')]
    public function otpConfirm(): Response
    {
        if (!$this->get('session')->get('email')) {
            return $this->redirectToRoute('register');
        }
        if (isset($_POST['checkOTP'])) {
            $otp = htmlspecialchars($_POST['otp']);

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['email' => $this->get('session')->get('email')]);

            if ($user[0]->getOtp() == $otp) {
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

    // Generate OTP code
    private function generateOTP($n = 6)
    {
        $generator = '1357902468';
        $result = '';

        for ($i = 1; $i <= $n; $i++) {
            $result .= substr($generator, rand() % strlen($generator), 1);
        }
        return $result;
    }
}
