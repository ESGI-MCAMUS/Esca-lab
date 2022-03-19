<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Gym;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\User;
use DateTime;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\HttpFoundation\Request;

use App\Form\GlobalSearchType;

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
    $this->setInformations();
    return $this->render('admin/index.html.twig', [
      'month' => date_format(new DateTime(), 'n'),
    ]);
  }

  /**
   * Gestion des utilisateurs
   */
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/utilisateurs', name: 'admin_users')]
  public function admin_users(Request $request): Response
  {
    $this->setInformations();
    $form = $this->createForm(GlobalSearchType::class);
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

  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/utilisateurs/suppression/{id}', name: 'admin_users_deletion')]
  public function admin_users_deletion(int $id): Response
  {
    $user = $this->getDoctrine()
      ->getRepository(User::class)
      ->find($id);

    if ($user->getIsDeleted() === false) {
      $user->setIsDeleted(true);
    } else {
      $user->setIsDeleted(false);
    }
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    return $this->redirectToRoute('admin_users');
  }
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/utilisateurs/activation/{id}', name: 'admin_users_activation')]
  public function admin_users_activation(int $id): Response
  {
    $user = $this->getDoctrine()
      ->getRepository(User::class)
      ->find($id);

    if ($user->getIsActivated() === false) {
      $user->setIsActivated(true);
    } else {
      $user->setIsActivated(false);
    }
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    return $this->redirectToRoute('admin_users');
  }

  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/utilisateurs/setRole/{role}/{action}/{id}', name: 'admin_user_setrole')]
  public function admin_user_setrole(
    string $role,
    string $action,
    int $id
  ): Response {
    $user = $this->getDoctrine()
      ->getRepository(User::class)
      ->find($id);
    $roles = $user->getRoles();
    if ($action === 'add') {
      array_push($roles, $role);
      $user->setRoles($roles);
    } else {
      if (($key = array_search($role, $roles)) !== false) {
        unset($roles[$key]);
      }
      $user->setRoles($roles);
    }
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    return $this->redirectToRoute('admin_users');
  }

  /**
   * Fin gestion des utilisateurs
   */

  /**
   * Gestion des évènements
   */

  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/evenements', name: 'admin_events')]
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
      $result = $eventRepo->search('%' . $search . '%');
      $entityManager = $this->getDoctrine()->getManager();
      $gyms = $entityManager->getRepository(Gym::class)->findAll();
      return $this->renderForm('admin/events.html.twig', [
        'events' => $result,
        'gyms' => $gyms,
        'form' => $form,
      ]);
    }

    $entityManager = $this->getDoctrine()->getManager();
    $events = $entityManager->getRepository(Event::class)->findAll();
    $gyms = $entityManager->getRepository(Gym::class)->findAll();
    dump($events);
    return $this->renderForm('admin/events.html.twig', [
      'events' => $events,
      'gyms' => $gyms,
      'form' => $form,
    ]);
  }

  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/evenements/suppression/{id}', name: 'admin_events_deletion')]
  public function admin_events_deletion(int $id): Response
  {
    $event = $this->getDoctrine()
      ->getRepository(Event::class)
      ->find($id);
    $eventRepo = $this->getDoctrine()
      ->getManager()
      ->getRepository(Event::class);
    $eventRepo->remove($event);

    return $this->redirectToRoute('admin_events');
  }

  private function setInformations()
  {
    $entityManager = $this->getDoctrine()->getManager();
    $usersCount = count($entityManager->getRepository(User::class)->findAll());
    $eventsCount = count(
      $entityManager->getRepository(Event::class)->findAll()
    );
    $this->get('session')->set('users_count', $usersCount);
    $this->get('session')->set('events_count', $eventsCount);
  }
}