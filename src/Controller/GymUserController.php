<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Gym;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class GymUserController extends AbstractController
{
    // #[Route('/gym/user', name: 'app_gym_user')]
    // public function index(): Response
    // {
    //     return $this->render('gym_user/index.html.twig', [
    //         'controller_name' => 'GymUserController',
    //     ]);
    // }

    #[Route('/gym/{id}', name: 'gym')]
    public function index($id, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $gym = $entityManager->getRepository(Gym::class)->find($id);

        $franchise = $gym->getFranchise();

        $routes = $gym->getRoutes();
        // $array_gyms = $franchise->getGyms();

        return $this->render('gym_user/index.html.twig', [
            'franchise' => $franchise,
            'gym'       => $gym,
            'routes'    => $routes
        ]);
    }
}
