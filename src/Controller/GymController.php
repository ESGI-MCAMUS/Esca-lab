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
                ? in_array("ROLE_ADMIN_SALLE", $this->user->getRoles())
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
                ? in_array("ROLE_ADMIN_SALLE", $this->user->getRoles())
                : false
        ) {
            return $this->render('gym/routes.html.twig', [
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }

    #[Route('/gym/employees', name: 'gym_employees')]
    public function employees() : Response {
        if (
            $this->user
                ? in_array("ROLE_ADMIN_SALLE", $this->user->getRoles())
                : false
        ) {
            $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $employees = $repo->findBy(["gym" => $this->user->getGym()->getId() ]);

            return $this->render('gym/employees.html.twig', [
                'employees' => $employees,
            ]);
        } else {
            return $this->redirectToRoute('accueil');
        }
    }


    #[Route('/gym/employees/edit/{id}/{check}', name: 'edit_gym_employee')]
    public function editEmployee($id, $check = 'user') {
        if (
            $this->user
                ? in_array("ROLE_ADMIN_SALLE", $this->user->getRoles())
                : false
        ) {
            $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $employee = $repo->find($id);

            if ($employee->getGym()->getId() === $this->user->getGym()->getId()) {
                $roles = $employee->getRoles();

                switch ($check) {
                    case 'ouvreur':
                        $key = array_search("ROLE_OUVREUR", $roles);
                        if ($key) $employee->setRoles(["ROLE_USER"]);
                        else $employee->setRoles(["ROLE_OUVREUR"]);
                        break;
                    case 'admin_salle':
                        $key = array_search("ROLE_ADMIN_SALLE", $roles);
                        if ($key) $employee->setRoles(["ROLE_USER"]);
                        else $employee->setRoles(["ROLE_ADMIN_SALLE"]);
                        break;
                    default:
                        return $this->redirectToRoute('gym_employees');
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($employee);
                $em->flush();
            }

            return $this->redirectToRoute('gym_employees');
        } else {
            return $this->redirectToRoute('accueil');
        }
    }

    #[Route('/gym/employees/remove/{id}', name: 'remove_gym_employee')]
    public function removeEmployee($id) {
        if (
            $this->user
                ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
                : false
        ) {
            $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $employee = $repo->find($id);

            dump($employee);

            if ($employee->getGym()->getId() === $this->user->getGym()->getId()) {
                $employee->setRoles(["ROLE_USER"]);
                $employee->setgym(null);

                $em = $this->getDoctrine()->getManager();
                $em->persist($employee);
                $em->flush();
            }

            return $this->redirectToRoute('gym_employees');
        } else {
            return $this->redirectToRoute('accueil');
        }
    }
}
