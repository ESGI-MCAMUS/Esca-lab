<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class UserController extends AbstractController
{

    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

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
        // $openRoutes = $this->user
        //     ->getGym()
        //     ->getRoutes()
        //     ->filter(function ($element) {
        //         return $element->getOpened() > 0;
        //     });

        // return $this->render('gym/index.html.twig', [
        //     'openRoutes' => $openRoutes,
        //     'month' => date_format(new DateTime(), 'n'),
        // ]);

        $friends = $this->user->getFriends();

        //dd($friends);
        
        return $this->render('user/friends.html.twig', [
              'controller_name' => 'UserController'
            , 'friends' => $friends
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
