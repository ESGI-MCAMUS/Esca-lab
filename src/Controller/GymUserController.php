<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        $resolved = $this->user->getRoutes();
        $id_resolved = [];
        foreach($resolved as $key => $value) {
            $id_resolved[] = $value->getId();
        }

        return $this->render('gym_user/index.html.twig', [
            'franchise' => $franchise,
            'gym'       => $gym,
            'routes'    => $routes,
            'resolved'  => $id_resolved,
        ]);
    }
}
