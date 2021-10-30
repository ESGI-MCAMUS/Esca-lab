<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'login')]
    public function index(): Response
    {
        if (isset($_POST['login'])) {
            $emailOrUsername = htmlspecialchars($_POST['emailOrUsername']);
            $password = htmlspecialchars($_POST['password']);

            $user_byEmail = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['email' => $emailOrUsername]);
            $user_byUsername = $this->getDoctrine()
                ->getRepository(User::class)
                ->findBy(['username' => $emailOrUsername]);
            if ($user_byEmail) {
                if (
                    password_verify($password, $user_byEmail[0]->getPassword())
                ) {
                    return $this->redirectToRoute('accueil');
                } else {
                    return $this->render('login/index.html.twig', [
                        'error' => ['missmatch' => true],
                        'emailOrUsername' => $emailOrUsername,
                    ]);
                }
            } elseif ($user_byUsername) {
                if (
                    password_verify(
                        $password,
                        $user_byUsername[0]->getPassword()
                    )
                ) {
                    return $this->redirectToRoute('accueil');
                } else {
                    return $this->render('login/index.html.twig', [
                        'error' => ['missmatch' => true],
                        'emailOrUsername' => $emailOrUsername,
                    ]);
                }
            } else {
                return $this->render('login/index.html.twig', [
                    'error' => ['not_exist' => true],
                    'emailOrUsername' => $emailOrUsername,
                ]);
            }
        }
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    private function checkPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
