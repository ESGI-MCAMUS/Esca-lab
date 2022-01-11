<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class RegisterController extends AbstractController
{
    #[Route('/inscription/otp', name: 'otpConfirmCreateAccount')]
    public function otpConfirmCreateAccountT(): Response
    {
        if (!$this->get('session')->get('email') && !$_GET['email']) {
            return $this->redirectToRoute('register');
        }
        if (
            isset($_POST['checkOTP']) ||
            (isset($_GET['email']) && isset($_GET['otp']))
        ) {
            $otp =
                htmlspecialchars($_GET['otp']) ??
                htmlspecialchars($_POST['otp']);
            $email = $_GET['email'] ?? $this->get('session')->get('email');

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