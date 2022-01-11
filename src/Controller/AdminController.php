<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\User;
use DateTime;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\HttpFoundation\Request;

use App\Form\UserSearchType;

use Doctrine\ORM\EntityManagerInterface;

class AdminController extends AbstractController
{
    private $user;
    private $em;

    public function __construct(
        Security $security,
        EntityManagerInterface $entityManager
    ) {
        $this->user = $security->getUser();
        $this->em = $entityManager;
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
    public function admin_users(Request $request): Response
    {
        $form = $this->createForm(UserSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('search')->getData();
            $userRepo = $this->getDoctrine()
                ->getManager()
                ->getRepository(User::class);
            $result = $userRepo->search('%' . $search . '%');
            return $this->renderForm('admin/users.html.twig', [
                'users' => $result,
                'form' => $form,
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->renderForm('admin/users.html.twig', [
            'users' => $users,
            'form' => $form,
        ]);
    }
}