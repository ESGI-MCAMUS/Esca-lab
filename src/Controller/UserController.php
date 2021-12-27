<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(): Response
    {
        return $this->render('user/resume.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/user/resume', name: 'resumeUser')]
    public function resumeUser(): Response
    {
        return $this->render('user/resume.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/events', name: 'eventsUser')]
    public function eventsUser(): Response
    {
        return $this->render('user/events.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/friends', name: 'friendsUser')]
    public function friendsUser(): Response
    {
        return $this->render('user/friends.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/medias', name: 'mediasUser')]
    public function mediasUser(): Response
    {
        return $this->render('user/medias.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
