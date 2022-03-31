<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Franchise;
use App\Entity\User;
// use App\Repository\FranchiseRepository;
use Doctrine\Persistence\ManagerRegistry;

class FranchiseUserController extends AbstractController
{

    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/franchise/all', name: 'index')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $array_franchise = $entityManager->getRepository(Franchise::class)->findAll();

        // dd($array_franchise);


        return $this->render('franchise_user/index.html.twig', [
            'franchises'    => $array_franchise,
        ]);
    }

    #[Route('/franchise/{id}/gyms', name: 'list_gyms_franchise')]
    public function franchiseGymList($id, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $franchise = $entityManager->getRepository(Franchise::class)->find($id);

        $array_gyms = $franchise->getGyms();

        return $this->render('franchise_user/gymList.html.twig', [
            'franchise_name' => $franchise->getName(),
            'gyms'    => $array_gyms,
        ]);
    }
}
