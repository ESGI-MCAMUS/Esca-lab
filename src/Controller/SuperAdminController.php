<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;

class SuperAdminController extends AbstractController
{
    #[Route('/admin/superadmin', name: 'super_admin')]
    public function index(): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findBy(['id' => $this->get('session')->get('user')]);
        dump($user[0]);
        if ($user[0]->getRole() === 5) {
            return $this->render('super_admin/index.html.twig', [
                'user' => $user[0],
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }
}