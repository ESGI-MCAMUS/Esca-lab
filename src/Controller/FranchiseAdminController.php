<?php

namespace App\Controller;

use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use DateTime;

#[IsGranted('ROLE_ADMIN_FRANCHISE')]
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

    #[Route('/franchise/employees/edit/{id}/{check}', name: 'edit_employee')]
    public function editEmployee($id, $check = 'user') {
        if (
            $this->user
                ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
                : false
        ) {
            $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $employee = $repo->find($id);

            if ($employee->getFranchise()->getId() === $this->user->getFranchise()->getId()) {
                $roles = $employee->getRoles();

                switch ($check) {
                    case 'ouvreur':
                        $key = array_search("ROLE_OUVREUR", $roles);
                        if ($key) $employee->setRoles(["ROLE_USER"]);
                        else $employee->setRoles(["ROLE_OUVREUR"]);
                        break;
                    case 'admin_franchise':
                        $key = array_search("ROLE_ADMIN_FRANCHISE", $roles);
                        if ($key) $employee->setRoles(["ROLE_USER"]);
                        else $employee->setRoles(["ROLE_ADMIN_FRANCHISE"]);
                        break;
                    case 'admin_salle':
                        $key = array_search("ROLE_ADMIN_SALLE", $roles);
                        if ($key) $employee->setRoles(["ROLE_USER"]);
                        else $employee->setRoles(["ROLE_ADMIN_SALLE"]);
                        break;
                    default:
                        return $this->redirectToRoute('franchise_employees');
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($employee);
                $em->flush();
            }

            return $this->redirectToRoute('franchise_employees');
        } else {
            return $this->redirectToRoute('accueil');
        }
    }

    #[Route('/franchise/employees/remove/{id}', name: 'remove_employee')]
    public function removeEmployee($id) {
        if (
            $this->user
                ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
                : false
        ) {
            $repo = $this->getDoctrine()->getManager()->getRepository(User::class);
            $employee = $repo->find($id);

            dump($employee);

            if ($employee->getFranchise()->getId() === $this->user->getFranchise()->getId()) {
                $employee->setRoles(["ROLE_USER"]);
                $employee->setFranchise(null);

                $em = $this->getDoctrine()->getManager();
                $em->persist($employee);
                $em->flush();
            }

            return $this->redirectToRoute('franchise_employees');
        } else {
            return $this->redirectToRoute('accueil');
        }
    }
}
