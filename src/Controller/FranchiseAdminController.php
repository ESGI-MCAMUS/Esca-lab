<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FranchiseAdminController extends AbstractController
{
    #[Route('/franchise/admin', name: 'franchise_admin')]
    public function index(): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['id' => $this->get('session')->get('user')]);
        if ($user[0]->getRole() === 3) {
            return $this->render('franchise_admin/index.html.twig', [
                'user' => $user[0],
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }
}
