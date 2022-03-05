<?php

namespace App\Controller;

use App\Entity\Gym;
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
        $this->setInformations();
        return $this->render('franchise/index.html.twig', [
            'month' => date_format(new DateTime(), 'n'),
        ]);
    }

    #[Route('/franchise/employees', name: 'franchise_employees')]
    public function employees(): Response
    {
        $this->setInformations();
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);
        $employees = $repo->findBy([
            'franchise' => $this->user->getFranchise()->getId(),
        ]);

        return $this->render('franchise/employees.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/franchise/employees/edit/{id}/{check}', name: 'edit_franchise_employee')]
    public function editEmployee($id, $check = 'user')
    {
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);
        $employee = $repo->find($id);

        if (
            $employee->getFranchise()->getId() ===
            $this->user->getFranchise()->getId()
        ) {
            $roles = $employee->getRoles();

            switch ($check) {
                case 'ouvreur':
                    $key = array_search('ROLE_OUVREUR', $roles);
                    if ($key) {
                        $employee->setRoles(['ROLE_USER']);
                    } else {
                        $employee->setRoles(['ROLE_OUVREUR']);
                    }
                    break;
                case 'admin_franchise':
                    $key = array_search('ROLE_ADMIN_FRANCHISE', $roles);
                    if ($key) {
                        $employee->setRoles(['ROLE_USER']);
                    } else {
                        $employee->setRoles(['ROLE_ADMIN_FRANCHISE']);
                    }
                    break;
                case 'admin_salle':
                    $key = array_search('ROLE_ADMIN_SALLE', $roles);
                    if ($key) {
                        $employee->setRoles(['ROLE_USER']);
                    } else {
                        $employee->setRoles(['ROLE_ADMIN_SALLE']);
                    }
                    break;
                default:
                    return $this->redirectToRoute('franchise_employees');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($employee);
            $em->flush();
        }

        return $this->redirectToRoute('franchise_employees');
    }

    #[Route('/franchise/employees/remove/{id}', name: 'remove_franchise_employee')]
    public function removeEmployee($id)
    {
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);
        $employee = $repo->find($id);

        if (
            $employee->getFranchise()->getId() ===
            $this->user->getFranchise()->getId()
        ) {
            $employee->setRoles(['ROLE_USER']);
            $employee->setFranchise(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($employee);
            $em->flush();
        }

        return $this->redirectToRoute('franchise_employees');
    }

    #[Route('/franchise/salles', name: 'franchise_gyms')]
    public function routes(): Response
    {
        $this->setInformations();
        $gyms = $this->user->getFranchise()->getGyms();
        return $this->render('franchise/gyms.html.twig', [
            'gyms' => $gyms,
        ]);
    }

    private function setInformations()
    {
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);
        $employeesCount = count(
            $repo->findBy([
                'franchise' => $this->user->getFranchise()->getId(),
            ])
        );
        $gymsCount = count($this->user->getFranchise()->getGyms());
        $this->get('session')->set('employees_count', $employeesCount);
        $this->get('session')->set('gyms_count', $gymsCount);
        $this->get('session')->set('ways_count', 0);
    }
}