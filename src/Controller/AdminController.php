<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Payments;
use App\Entity\Route as RouteEntity;
use App\Entity\Gym;
use App\Entity\Media;
use App\Entity\Franchise;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use App\Entity\User;
use DateTime;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\HttpFoundation\Request;

use App\Form\GlobalSearchType;
use App\Form\AddFranchiseType;

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
  /**
   * Fin gestion des évènements
   */

  /**
   * Gestion des médias
   */

  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/medias', name: 'admin_medias')]
  public function admin_medias(Request $request): Response
  {
    $this->setInformations();
    $form = $this->createForm(GlobalSearchType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $search = $form->get('search')->getData();
      $mediaRepo = $this->getDoctrine()
        ->getManager()
        ->getRepository(Media::class);
      $result = $mediaRepo->search('%' . $search . '%');
      return $this->renderForm('admin/medias.html.twig', [
        'medias' => $result,
        'form' => $form,
      ]);
    }

    $entityManager = $this->getDoctrine()->getManager();
    $medias = $entityManager->getRepository(Media::class)->findAll();
    return $this->renderForm('admin/medias.html.twig', [
      'medias' => $medias,
      'form' => $form,
    ]);
  }

  // Remove media
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/medias/suppression/{id}', name: 'admin_medias_deletion')]
  public function admin_medias_deletion(int $id): Response
  {
    $media = $this->getDoctrine()
      ->getRepository(Media::class)
      ->find($id);
    $mediaRepo = $this->getDoctrine()
      ->getManager()
      ->getRepository(Media::class);
    $mediaRepo->remove($media);

    return $this->redirectToRoute('admin_medias');
  }

  /**
   * Fin gestion des médias
   */

  /**
   * Gestion des franchises
   */
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/franchises', name: 'admin_franchises')]
  public function admin_franchises(Request $request): Response
  {
    $this->setInformations();
    $form = $this->createForm(GlobalSearchType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $search = $form->get('search')->getData();
      $franchiseRepo = $this->getDoctrine()
        ->getManager()
        ->getRepository(Franchise::class);
      $result = $franchiseRepo->search('%' . $search . '%');
      return $this->renderForm('admin/franchises.html.twig', [
        'franchises' => $result,
        'form' => $form,
      ]);
    }

    $entityManager = $this->getDoctrine()->getManager();
    $franchises = $entityManager->getRepository(Franchise::class)->findAll();
    return $this->renderForm('admin/franchises.html.twig', [
      'franchises' => $franchises,
      'form' => $form,
    ]);
  }

  // Delete franchise by id
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/franchise/suppression/{id}', name: 'admin_franchises_deletion')]
  public function admin_franchises_deletion(int $id): Response
  {
    $franchise = $this->getDoctrine()
      ->getRepository(Franchise::class)
      ->find($id);
    $franchiseRepo = $this->getDoctrine()
      ->getManager()
      ->getRepository(Franchise::class);
    $franchiseRepo->remove($franchise);

    return $this->redirectToRoute('admin_franchises');
  }

  // Add franchise
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/franchises/ajouter', name: 'admin_franchises_add')]
  public function admin_franchises_add(Request $request): Response
  {
    // get all users
    $entityManager = $this->getDoctrine()->getManager();
    $users = $entityManager
      ->getRepository(User::class)
      ->findBy([], ['email' => 'ASC']);
    $this->setInformations();
    $franchise = new Franchise();
    $form = $this->createForm(AddFranchiseType::class, $franchise, [
      'users' => $users,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager = $this->getDoctrine()->getManager();
      $entityManager->persist($franchise);
      $entityManager->flush();

      dump($franchise);

      $user = $this->getDoctrine()
        ->getRepository(User::class)
        ->find($franchise->getAdmin());

      $user->setRoles(['ROLE_ADMIN_FRANCHISE']);
      $user->setFranchise($franchise);
      $entityManager->persist($user);
      $entityManager->flush();

      return $this->redirectToRoute('admin_franchises');
    }

    return $this->renderForm('admin/addFranchises.html.twig', [
      'form' => $form,
    ]);
  }

  /**
   * Fin gestion des franchises
   */

  /** Gestion des salles
   *
   */
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/salles', name: 'admin_salles')]
  public function admin_salles(Request $request): Response
  {
    $this->setInformations();
    $form = $this->createForm(GlobalSearchType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $search = $form->get('search')->getData();
      $gymRepo = $this->getDoctrine()
        ->getManager()
        ->getRepository(Gym::class);
      $result = $gymRepo->search('%' . $search . '%');
      return $this->renderForm('admin/salles.html.twig', [
        'salles' => $result,
        'form' => $form,
      ]);
    }

    $entityManager = $this->getDoctrine()->getManager();
    $gyms = $entityManager->getRepository(Gym::class)->findAll();
    return $this->renderForm('admin/salles.html.twig', [
      'salles' => $gyms,
      'form' => $form,
    ]);
  }

  // Delete gym by id
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/salle/suppression/{id}', name: 'admin_salles_deletion')]
  public function admin_salles_deletion(int $id): Response
  {
    $gym = $this->getDoctrine()
      ->getRepository(Gym::class)
      ->find($id);
    $gymRepo = $this->getDoctrine()
      ->getManager()
      ->getRepository(Gym::class);
    $gymRepo->remove($gym);

    return $this->redirectToRoute('admin_salles');
  }

  /**
   * Fin gestion des salles
   */

  /**
   * Gestion des voies
   */
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/voies', name: 'admin_voies')]
  public function admin_voies(Request $request): Response
  {
    $this->setInformations();
    $form = $this->createForm(GlobalSearchType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $search = $form->get('search')->getData();
      $voieRepo = $this->getDoctrine()
        ->getManager()
        ->getRepository(RouteEntity::class);
      $result = $voieRepo->search('%' . $search . '%');
      return $this->renderForm('admin/voies.html.twig', [
        'voies' => $result,
        'form' => $form,
      ]);
    }

    $entityManager = $this->getDoctrine()->getManager();
    $voies = $entityManager->getRepository(RouteEntity::class)->findAll();
    return $this->renderForm('admin/voies.html.twig', [
      'voies' => $voies,
      'form' => $form,
    ]);
  }

  // Delete voie by id
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/voie/suppression/{id}', name: 'admin_voies_deletion')]
  public function admin_voies_deletion(int $id): Response
  {
    $voie = $this->getDoctrine()
      ->getRepository(RouteEntity::class)
      ->find($id);
    $voieRepo = $this->getDoctrine()
      ->getManager()
      ->getRepository(RouteEntity::class);
    $voieRepo->remove($voie);

    return $this->redirectToRoute('admin_voies');
  }

  /**
   * Fin gestion des voies
   */

  /**
   * Gestion des paiements
   */
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/paiements', name: 'admin_paiements')]
  public function admin_paiements(Request $request): Response
  {
    $this->setInformations();
    $form = $this->createForm(GlobalSearchType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $search = $form->get('search')->getData();
      $paymentRepo = $this->getDoctrine()
        ->getManager()
        ->getRepository(Payments::class);
      $result = $paymentRepo->search('%' . $search . '%');
      return $this->renderForm('admin/paiements.html.twig', [
        'paiements' => $result,
        'form' => $form,
      ]);
    }

    $entityManager = $this->getDoctrine()->getManager();
    $payments = $entityManager->getRepository(Payments::class)->findAll();
    return $this->renderForm('admin/paiements.html.twig', [
      'paiements' => $payments,
      'form' => $form,
    ]);
  }

  // Send email reminder to user
  #[IsGranted("ROLE_SUPER_ADMIN")]
  #[Route('/admin/paiement/remind/{franchise}/{user}', name: 'admin_paiement_reminder')]
  public function admin_paiement_reminder($franchise, $user): Response
  {
    $franchise = $this->getDoctrine()
      ->getRepository(Franchise::class)
      ->find($franchise);
    $payments = $this->getDoctrine()
      ->getRepository(Payments::class)
      ->getNotPaidById($franchise->getId());

    // get user by id
    $userDoctrine = $this->getDoctrine()
      ->getRepository(User::class)
      ->find($user);

    dump($userDoctrine);

    $userEmail = $userDoctrine->getEmail();
    $userName = $userDoctrine->getFirstname();

    $mailer = new MailerController();
    $mailSent = $mailer->sendPaymentReminderEmail(
      $userEmail,
      $userName,
      $payments
    );
    if ($mailSent) {
      $this->addFlash(
        'success',
        'Un email de rappel a été envoyé à ' .
          $userName .
          ' (' .
          $userEmail .
          ') avec le récapitulatif de l\'intégralité de ses paiements à régulariser.'
      );
    } else {
      $this->addFlash(
        'error',
        'Une erreur s\est produite lors de l\'envoi de l\'email de rappel à ' .
          $userName .
          ' (' .
          $userEmail .
          '). Veuillez rééssayer.'
      );
    }
    return $this->redirectToRoute('admin_paiements');
  }

  private function setInformations()
  {
    $entityManager = $this->getDoctrine()->getManager();
    $usersCount = count($entityManager->getRepository(User::class)->findAll());
    $eventsCount = count(
      $entityManager->getRepository(Event::class)->findAll()
    );
    $mediasCount = count(
      $entityManager->getRepository(Media::class)->findAll()
    );
    $routesCount = -1;
    $gymsCount = count($entityManager->getRepository(Gym::class)->findAll());
    $franchisesCount = count(
      $entityManager->getRepository(Franchise::class)->findAll()
    );
    $routesCount = count(
      $entityManager->getRepository(RouteEntity::class)->findAll()
    );
    $paymentsCount = count(
      $entityManager->getRepository(Payments::class)->findAll()
    );

    $this->get('session')->set('users_count', $usersCount);
    $this->get('session')->set('events_count', $eventsCount);
    $this->get('session')->set('medias_count', $mediasCount);
    $this->get('session')->set('routes_count', $routesCount);
    $this->get('session')->set('gyms_count', $gymsCount);
    $this->get('session')->set('franchises_count', $franchisesCount);
    $this->get('session')->set('routes_count', $routesCount);
    $this->get('session')->set('payments_count', $paymentsCount);
  }
}