<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use DateTime;

class FranchiseAdminController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/franchise/admin', name: 'franchise_admin')]
    public function index(): Response
    {
        dump($this->user ? $this->user->getRoles() : 'null_user');
        if (
            $this->user
                ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
                : false
        ) {
            return $this->render('franchise/index.html.twig', [
                'month' => date_format(new DateTime(), 'n'),
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }
}
