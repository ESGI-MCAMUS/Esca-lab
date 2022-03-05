<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class LoginController extends AbstractController
{
    #[Route('/connexion/mot-de-passe-oublie', name: 'forgotPassword')]
    public function forgotPassword()
    {
        $this->get('session')->clear();
        if (isset($_POST['passwordReset'])) {
            $emailOrUsername = htmlspecialchars($_POST['emailOrUsername']);
            $utils = new UtilsController();
            $otp = $utils->generateOTP();

            $entityManager = $this->getDoctrine()->getManager();
            $user_byEmail = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['email' => $emailOrUsername]);
            $user_byUsername = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['username' => $emailOrUsername]);
            if ($user_byEmail) {
                $user_byEmail[0]->setOtp($otp);
                $entityManager->flush();
                return $this->sendResetPasswordEmail(
                    $user_byEmail[0]->getEmail(),
                    $user_byEmail[0]->getFirstname(),
                    $otp
                );
            } elseif ($user_byUsername) {
                $user_byUsername[0]->setOtp($otp);
                $entityManager->flush();
                return $this->sendResetPasswordEmail(
                    $user_byUsername[0]->getEmail(),
                    $user_byUsername[0]->getFirstname(),
                    $otp
                );
            } else {
                return $this->render('login/resetPassword.html.twig', [
                    'error' => ['not_exists' => true],
                ]);
            }
        }
        return $this->render('login/resetPassword.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    #[Route('/connexion/mot-de-passe-oublie/otp', name: 'otpConfirmPwdReset')]
    public function otpConfirmPwdReset(): Response
    {
        dump($this->get('session')->get('email'));
        if (!$this->get('session')->get('email')) {
            return $this->redirectToRoute('forgotPassword');
        }
        if (isset($_POST['checkOTP'])) {
            $otp = htmlspecialchars($_POST['otpReset']);
            $password = htmlspecialchars($_POST['password']);
            $passwordConfirm = htmlspecialchars($_POST['passwordConfirm']);

            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager
                ->getRepository(User::class)
                ->findBy(['email' => $this->get('session')->get('email')]);

            if ($user[0]->getOtp() == $otp) {
                $errors = [];
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

                if (count($errors) !== 0) {
                    return $this->render('login/otp.html.twig', [
                        'errors' => $errors,
                        'otp' => $user[0]->getOtp(),
                    ]);
                } else {
                    $user[0]->setOtp(null);
                    $user[0]->setPassword(
                        password_hash($password, PASSWORD_DEFAULT)
                    );
                    $entityManager->flush();
                    return $this->redirectToRoute('login');
                }
            } else {
                //dump($user[0]->getOtp());
                return $this->render('login/otp.html.twig', [
                    'error' => 'otp',
                ]);
            }
        }
        return $this->render('login/otp.html.twig', [
            'email' => $this->get('session')->get('email'),
        ]);
    }

    private function sendResetPasswordEmail($to, $firstname, $otp)
    {
        $this->get('session')->set('email', $to);
        $mail = new MailerController();
        $mail->sendEmailOTP($to, $otp, $firstname);
        return $this->redirectToRoute('otpConfirmPwdReset');
    }
}