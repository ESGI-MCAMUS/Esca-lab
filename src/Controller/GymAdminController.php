<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Gym;
use App\Entity\User;
use App\Form\EventType;
use App\Form\GlobalSearchType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GymAdminController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[IsGranted('ROLE_ADMIN_SALLE')]
    #[Route('/gym/kpi', name: 'gym_kpi')]
    public function index(): Response
    {
        $this->setInformations();

        return $this->render('gym/index.html.twig', [
            'month' => date_format(new DateTime(), 'n'),
        ]);
    }

    #[IsGranted('ROLE_OUVREUR')]
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

    #[IsGranted('ROLE_ADMIN_SALLE')]
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

    #[IsGranted('ROLE_ADMIN_SALLE')]
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

    #[IsGranted('ROLE_ADMIN_SALLE')]
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


    /**
     * Gestion des évènements
     */

    #[IsGranted("ROLE_ADMIN_SALLE")]
    #[Route('/gym/evenements', name: 'gym_events')]
    public function admin_events(Request $request): Response
    {
        $this->setInformations();
        $form = $this->createForm(GlobalSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $search = $form->get('search')->getData();
            $eventRepo = $this->getDoctrine()
                ->getManager()
                ->getRepository(Event::class);
            $result = $eventRepo->searchByGym('%' . $search . '%', $this->user->getGym()->getId());
            return $this->renderForm('admin/events.html.twig', [
                'events' => $result,
                'form' => $form,
            ]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $events = $entityManager->getRepository(Event::class)->findBy(['gym' => $this->user->getGym()->getId()]);
        return $this->renderForm('admin/events.html.twig', [
            'events' => $events,
            'form' => $form,
        ]);
    }

    #[IsGranted("ROLE_ADMIN_SALLE")]
    #[Route('/gym/evenements/ajout', name: 'gym_events_add')]
    public function admin_events_add(Request $request, EntityManagerInterface $em): Response
    {
        $this->setInformations();
        $form = $this->createForm(EventType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = $form->getData();
            $event->setGym($this->user->getGym());

            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute('gym_events');
        }

        return $this->renderForm('event/add.html.twig', [
            'form_add' => $form,
        ]);
    }

    #[IsGranted("ROLE_ADMIN_SALLE")]
    #[Route('/gym/evenements/edition/{id}', name: 'gym_events_edit')]
    public function admin_events_edit($id, Request $request, EntityManagerInterface $em): Response
    {
        $this->setInformations();

        $repo = $this->getDoctrine()->getRepository(Event::class);
        $event = $repo->findOneBy(["id" => $id, "gym" => $this->user->getGym()->getId()]);

        if ($event) {
            $form = $this->createForm(EventType::class)->setData($event);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $eventEdit = $form->getData();
                $event->setTitle($eventEdit->getTitle());
                $event->setDescription($eventEdit->getDescription());
                $event->setEventDate($eventEdit->getEventDate());
                $event->setEndDate($eventEdit->getEndDate());

                $em->persist($event);
                $em->flush();

                return $this->redirectToRoute('gym_events');
            }

            return $this->renderForm('event/edit.html.twig', [
                'form_edit' => $form,
            ]);
        }
        return $this->redirectToRoute('gym_events');
    }

    #[IsGranted("ROLE_ADMIN_SALLE")]
    #[Route('/gym/evenements/suppression/{id}', name: 'gym_events_deletion')]
    public function admin_events_deletion(int $id): Response
    {
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findOneBy(['id' => $id, 'gym' => $this->user->getGym()->getId()]);
        $eventRepo = $this->getDoctrine()
            ->getManager()
            ->getRepository(Event::class);

        if ($event) {
            $eventRepo->remove($event);
        }

        return $this->redirectToRoute('gym_events');
    }
    /**
     * Fin gestion des évènements
     */

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