<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use App\Entity\Gym;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class GymUserController extends AbstractController
{

    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/gym/{id}', name: 'gym')]
    public function index($id, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $gym = $entityManager->getRepository(Gym::class)->find($id);

        $franchise = $gym->getFranchise();

        $routes = $gym->getRoutes();

        $id_resolved = [];
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $resolved = $this->user->getRoutes();
            foreach($resolved as $key => $value) {
                $id_resolved[] = $value->getId();
            }      
        }

        return $this->render('gym_user/index.html.twig', [
            'franchise' => $franchise,
            'gym'       => $gym,
            'routes'    => $routes,
            'resolved'  => $id_resolved,
        ]);
    }

    #[Route('/gym/favorite/add/{id}', name: 'add_favorite_gym')]
    public function addFavoriteGym($id, ManagerRegistry $doctrine)
    {
        $success = true;
        $entityManager = $doctrine->getManager();
        $gym = $entityManager->getRepository(Gym::class)->find($id);

        if ($gym != null) {
            $favorite_gym = new \App\Entity\FavoriteGym();
            $favorite_gym->setUserId($this->user);
            $favorite_gym->setGymId($gym);
            $entityManager->persist($favorite_gym);
            $entityManager->flush();
        } else {
            $success = false;
        }

        return new JsonResponse(['success' => $success]);
    }

    #[Route('/gym/favorite/remove/{id}', name: 'remove_favorite_gym')]
    public function removeFavoriteGym($id, ManagerRegistry $doctrine)
    {
        $success = true;
        $entityManager = $doctrine->getManager();
        $gym = $entityManager->getRepository(Gym::class)->find($id);
        $favorite_gym = $entityManager->getRepository(\App\Entity\FavoriteGym::class)->findOneBy(['userId' => $this->user, 'gymId' => $gym]);
        if ($favorite_gym != null) {
            $entityManager->remove($favorite_gym);
            $entityManager->flush();
        } else {
            $success = false;
        }
        return new JsonResponse(['success' => $success]);
    }
}
