<?php

namespace App\Controller;

use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GymController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/gym/kpi', name: 'gym_kpi')]
    public function index(): Response
    {
        if (
            $this->user
                ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
                : false
        ) {
            return $this->render('gym/index.html.twig', [
                'month' => date_format(new DateTime(), 'n'),
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }

    #[Route('/gym/voies', name: 'gym_routes')]
    public function routes(): Response
    {
        if (
            $this->user
                ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
                : false
        ) {
            return $this->render('gym/routes.html.twig', [
                'month' => date_format(new DateTime(), 'n'),
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }
}
