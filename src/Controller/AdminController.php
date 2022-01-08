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

    #[Route('/admin', name: 'super_admin')]
    public function index(): Response
    {
        dump($this->user ? $this->user->getRoles() : 'null_user');
        if (
            $this->user
                ? in_array('ROLE_SUPER_ADMIN', $this->user->getRoles())
                : false
        ) {
            return $this->render('admin/index.html.twig', [
                'month' => date_format(new DateTime(), 'n'),
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }
}