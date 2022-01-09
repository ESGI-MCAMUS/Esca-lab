<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bridge\Doctrine\ManagerRegistry;
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

    #[Route('/franchise/kpi', name: 'franchise_kpi')]
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

    #[Route('/franchise/employees', name: 'franchise_employees')]
    public function employees() : Response {
        if (
            $this->user
                ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
                : false
        ) {
            $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $employees = $repo->findBy(["franchise" => $this->user->getFranchise()->getId() ]);

            return $this->render('franchise/employees.html.twig', [
                'employees' => $employees,
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }
}
