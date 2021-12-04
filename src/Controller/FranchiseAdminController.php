<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FranchiseAdminController extends AbstractController
{
    #[Route('/franchise/admin', name: 'franchise_admin')]
    public function index(): Response
    {
        return $this->render('franchise_admin/index.html.twig', [
            'controller_name' => 'FranchiseAdminController',
        ]);
    }
}
