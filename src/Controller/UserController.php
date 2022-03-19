<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\AddFriendType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

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
        $friends = $this->user->getFriends();

        return $this->render('user/friends.html.twig', [
              'controller_name' => 'UserController'
            , 'friends' => $friends
        ]);
    }

    /**
     * Permet d'appeler la page ou se trouve le formulaire de recherche
     * des amis
     */
    #[Route('/user/friends/add', name: 'addFriendsUser')]
    public function addFriendsUser(ManagerRegistry $doctrine, EntityManagerInterface $em, Request $request, UserRepository $userRepository): Response
    {

        $form = $this->createForm(AddFriendType::class);
        $form->handleRequest($request);

        $liste_user = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $search_user = $form->getData()['username'];

            // we retrieve our friends so we don't get friends and randoms
            $friends = $this->user->getFriends()->getValues();

            $ids_arra = [];

            foreach ($friends as $friend) {
                $ids_arra[] = $friend->getId();
            }

            $entityManager = $doctrine->getManager();
            $liste_user = $entityManager->getRepository(User::class)->findAllUserMatchingName($search_user);

            // we remove the users that are already friends with us
            $user_array = [];
            foreach ($liste_user as $user) {
                if(!in_array($user->getId(), $ids_arra)) {
                    $user_array[] = $user;
                }
            }
            $liste_user = $user_array;
        }

        return $this->renderForm('user/add-friends.html.twig', [
            'form_add'      => $form,
            'liste_user'    => $liste_user,
            'hidden_uri'    => $request->getUri()
        ]);
    }

    #[Route('/user/friends/add/{userId}', name: 'addFriendsUserId', defaults: ["userId" => null], methods: ['POST'])]
    public function add(ManagerRegistry $doctrine, $userId): Response
    {
        $success = true;
        
        $entityManager = $doctrine->getManager();
        $newFriend = $entityManager->getRepository(User::class)->find($userId);
        
        if($newFriend->getId() !== null) {
            $this->user->addFriend($newFriend);
        } else {
            $success = false;
        }

        return new JsonResponse(array('success' => $success));
    }

    #[Route('/user/medias', name: 'mediasUser')]
    public function mediasUser(): Response
    {
        return $this->render('user/medias.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
