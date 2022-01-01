<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use DateTime;
use Symfony\Component\Security\Core\Security;

class AdminController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        dump($this->user ? $this->user->getRoles() : 'null_user');
        return $this->render('admin/index.html.twig', [
            'month' => date_format(new DateTime(), 'n'),
        ]);
    }

    #[Route('/admin/utilisateurs', name: 'admin_users')]
    public function admin_users(): Response
    {
        dump($this->user ? $this->user->getRoles() : 'null_user');
        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(User::class)->findAll();
        dump($users);
        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }
}