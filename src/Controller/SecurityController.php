<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/connexion', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        dump($error);
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/inscription', name: 'register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (
                $form
                    ->get('password')
                    ->get('first')
                    ->getData() ===
                $form
                    ->get('password')
                    ->get('second')
                    ->getData()
            ) {
                $utils = new UtilsController();
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $user->getPassword())
                );
                $user->setBirthdate($user->getBirthdate());
                $user->setCreatedAt(new \DateTime());
                $user->setOtp($utils->generateOTP());

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->get('session')->set('email', $user->getEmail());

                $mail = new MailerController();
                $mail->sendEmailOTP(
                    $user->getEmail(),
                    $user->getOtp(),
                    $user->getFirstname()
                );

                return $this->redirectToRoute('login');
            }
        }

        return $this->renderForm('register/register.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
