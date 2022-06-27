<?php

namespace App\Controller;

use App\Entity\Gym;
use App\Entity\Payments;
use App\Entity\User;
use App\Form\EmployeeType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use DateTime;

#[IsGranted('ROLE_ADMIN_FRANCHISE')]
class FranchiseAdminController extends AbstractController {
  private $user;

  public function __construct(Security $security) {
    $this->user = $security->getUser();
  }

  #[IsGranted('ROLE_ADMIN_FRANCHISE')]
  #[Route('/franchise/kpi', name: 'franchise_kpi')]
  public function index(): Response {
    $this->setInformations();
    return $this->render('franchise/index.html.twig', [
      'month' => date_format(new DateTime(), 'n'),
    ]);
  }

  #[IsGranted('ROLE_ADMIN_FRANCHISE')]
  #[Route('/franchise/employees', name: 'franchise_employees')]
  public function employees(): Response
  {
      if ($this->user->getFranchise() == null) {
          return $this->redirectToRoute('franchise_kpi');
      }
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

  #[IsGranted('ROLE_ADMIN_FRANCHISE')]
  #[Route('/franchise/employees/add', name: 'add_franchise_employee')]
  public function addEmployee(Request $request, EntityManagerInterface $em) {

        $this->setInformations();
        $form = $this->createForm(EmployeeType::class);
        $form->handleRequest($request);

    $liste_user = [];
    $gyms = $this->user->getFranchise()->getGyms();

    if ($form->isSubmitted() && $form->isValid()) {
      $search_user = $form->getData()['username'];

      // we retrieve our employees so we don't get employees and randoms
      $employees = $em->getRepository(User::class)->findBy(['franchise' => $this->user->getFranchise()->getId()]);

      $ids_arra = [];

      foreach ($employees as $employee) {
        $ids_arra[] = $employee->getId();
      }

      $liste_user = $em->getRepository(User::class)->findAllUserMatchingName($search_user);

      // we remove the users that are already employees with us
      $user_array = [];
      foreach ($liste_user as $user) {
        if (!in_array($user->getId(), $ids_arra)) {
          $user_array[] = $user;
        }
      }
      $liste_user = $user_array;
    }

    #[IsGranted('ROLE_ADMIN_SALLE')]
    #[Route('/franchise/employees/add/{userId}', name: 'add_franchise_employee_id', defaults: ["userId" => null], methods: ['POST'])]
    public function addEmployeeUserId(\Doctrine\Persistence\ManagerRegistry $doctrine, $userId, Request $request): Response
    {
        $success = true;

  #[IsGranted('ROLE_ADMIN_SALLE')]
  #[Route('/franchise/employees/add/{userId}', name: 'add_franchise_employee_id', defaults: ["userId" => null], methods: ['POST'])]
  public function addEmployeeUserId(\Doctrine\Persistence\ManagerRegistry $doctrine, $userId, Request $request): Response {
    $success = true;

    $entityManager = $doctrine->getManager();
    $newEmployee = $entityManager->getRepository(User::class)->find($userId);
    $gym = $entityManager->getRepository(Gym::class)->findOneBy(['id' => $request->get('gym')]);

    if ($newEmployee->getId() !== null) {
      $newEmployee->setGym($gym);
      $newEmployee->setFranchise($this->user->getFranchise());
      $newEmployee->setRoles(["ROLE_USER", "ROLE_OUVREUR"]);
      $entityManager->persist($newEmployee);
      $entityManager->flush();
    } else {
      $success = false;
    }

    return new JsonResponse(array('success' => $success));
  }

  #[IsGranted('ROLE_ADMIN_FRANCHISE')]
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

  #[IsGranted('ROLE_ADMIN_FRANCHISE')]
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
      $employee->setGym(null);
      $employee->setFranchise(null);

      $em = $this->getDoctrine()->getManager();
      $em->persist($employee);
      $em->flush();
    }

    return $this->redirectToRoute('franchise_employees');
  }

  #[IsGranted('ROLE_ADMIN_FRANCHISE')]
  #[Route('/franchise/salles', name: 'franchise_gyms')]
  public function routes(): Response
  {
    $this->setInformations();
    $gyms = $this->user->getFranchise()->getGyms();
    dump($gyms);
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
        $gyms = $this->user->getFranchise()->getGyms();
        $waysCount = 0;
        $openedWays = 0;
        foreach ($gyms as $gym) {
            $waysCount += count($gym->getRoutes());
            $openedWays += count($gym->getRoutes()->filter(function ($element) {
                return $element->getOpened() > 0;
            }));
        }
        $gymsCount = count($gyms);

        $payment_repo = $this->getDoctrine()
            ->getManager()
            ->getRepository(Payments::class);
        $payments = $payment_repo->findBy([
            'franchise' => $this->user->getFranchise()->getId(),
        ]);
        $total_payments = 0;
        foreach ($payments as $payment) {
            $payment->getStatus() !== "success" ? $total_payments++ : $total_payments;
        }

        $this->get('session')->set('employees_count', $employeesCount);
        $this->get('session')->set('gyms_count', $gymsCount);
        $this->get('session')->set('ways_count', $waysCount);
        $this->get('session')->set('opened_ways', $openedWays);
        $this->get('session')->set('payments', $total_payments);
    }

    $this->get('session')->set('employees_count', $employeesCount);
    $this->get('session')->set('gyms_count', $gymsCount);
    $this->get('session')->set('ways_count', $waysCount);
    $this->get('session')->set('opened_ways', $openedWays);
    $this->get('session')->set('payments', $total_payments);
  }
}