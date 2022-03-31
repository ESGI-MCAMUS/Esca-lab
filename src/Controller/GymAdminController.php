<?php

namespace App\Controller;

use App\Entity\Gym;
use App\Entity\User;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[IsGranted('ROLE_ADMIN_SALLE')]
class GymAdminController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/gym/kpi', name: 'gym_kpi')]
    public function index(): Response
    {
        $this->setInformations();

        return $this->render('gym/index.html.twig', [
            'month' => date_format(new DateTime(), 'n'),
        ]);
    }

    #[Route('/gym/voies', name: 'gym_routes')]
    public function routes(): Response
    {
        $routes = $this->user->getGym()->getRoutes();
        return $this->render('gym/routes.html.twig', [
            'routes' => $routes,
        ]);
    }

    #[IsGranted('ROLE_ADMIN_FRANCHISE')]
    #[Route('/gym/voies/{id}', name: 'gym_routes_franchise', defaults: ["id" => null])]
    public function routesByFranchise($id): Response
    {
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(Gym::class);
        $gym =
            $this->user->getGym() ??
            $repo->findOneBy([
                'id' => $id,
                'franchise' => $this->user->getFranchise()->getId(),
            ]);
        $routes = $gym->getRoutes();
        return $this->render('gym/routes.html.twig', [
            'routes' => $routes,
        ]);
    }

    #[Route('/gym/employees', name: 'gym_employees')]
    public function employees(): Response
    {
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);
        $employees = $repo->findBy(['gym' => $this->user->getGym()->getId()]);

        return $this->render('gym/employees.html.twig', [
            'employees' => $employees,
        ]);
    }

    #[Route('/gym/employees/edit/{id}/{check}', name: 'edit_gym_employee')]
    public function editEmployee($id, $check = 'user')
    {
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);
        $employee = $repo->find($id);

        if ($employee->getGym()->getId() === $this->user->getGym()->getId()) {
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
                case 'admin_salle':
                    $key = array_search('ROLE_ADMIN_SALLE', $roles);
                    if ($key) {
                        $employee->setRoles(['ROLE_USER']);
                    } else {
                        $employee->setRoles(['ROLE_ADMIN_SALLE']);
                    }
                    break;
                default:
                    return $this->redirectToRoute('gym_employees');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($employee);
            $em->flush();
        }

        return $this->redirectToRoute('gym_employees');
    }

    #[Route('/gym/employees/remove/{id}', name: 'remove_gym_employee')]
    public function removeEmployee($id)
    {
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);
        $employee = $repo->find($id);

        if ($employee->getGym()->getId() === $this->user->getGym()->getId()) {
            $employee->setRoles(['ROLE_USER']);
            $employee->setgym(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($employee);
            $em->flush();
        }

        return $this->redirectToRoute('gym_employees');
    }

    private function setInformations()
    {
        $repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class);
        $employeesCount = count(
            $repo->findBy([
                'gym' => $this->user->getGym()->getId(),
            ])
        );
        $ways = $this->user->getGym()->getRoutes();

        $waysCount = count($ways);
        $openedWays = count($ways->filter(function ($element) {
            return $element->getOpened() > 0;
        }));

        $this->get('session')->set('employees_count', $employeesCount);
        $this->get('session')->set('ways_count', $waysCount);
        $this->get('session')->set('opened_ways', $openedWays);
    }
}