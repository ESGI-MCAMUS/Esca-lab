<?php

namespace App\Controller;

use App\Entity\Route as RouteEntity;
use App\Entity\Gym;
use App\Entity\Message;
use App\Entity\Payments; 

use App\Form\RouteType;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class RouteController extends AbstractController
{
  private $user;

  public function __construct(Security $security)
  {
    $this->user = $security->getUser();
  }

  #[IsGranted('ROLE_OUVREUR')]
  #[Route('/route/add/{gymId}', name: 'route_add', defaults: ["gymId" => null])]
  public function add(
    $gymId,
    EntityManagerInterface $em,
    Request
    $request,
    SluggerInterface $slugger
  ): Response {
    $form = $this->createForm(RouteType::class);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $route = $form->getData();

      $picture = $form->get('picture')->getData();
      if ($picture) {
        $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = uniqid() . '.' . $picture->guessExtension();
        try {
          $picture->move(
            $this->getParameter('routes_pictures'),
            $newFilename
          );
          $route->setPicture($newFilename);
        } catch (FileException $e) {
          $route->setPicture("");
        }
      }

      $route->setGym(
        $this->user->getGym() ??
          $this->getDoctrine()
            ->getRepository(Gym::class)
            ->findOneBy([
              "id" => $gymId,
              "franchise" => $this->user->getFranchise()->getId(),
            ])
      );

      if ($route->getGym()) {
        $em->persist($form->getData());
        $em->flush();

        // Create a payment for the gym
        $payment = new Payments();
        $payment->setFranchise($route->getGym()->getFranchise());
        $payment->setType('route');
        $payment->setAmount(100);
        $payment->setStatus('pending');
        $payment->setToken(null);
        $payment->setCreatedAt(new \DateTime());
        $payment->setUpdatedAt(new \DateTime());
        $em->persist($payment);
        $em->flush();

        if (
          $this->user
            ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
            : false
        ) {
          return $this->redirectToRoute("gym_routes_franchise", [
            "id" => $gymId,
          ]);
        }
        return $this->redirectToRoute("gym_routes");
      }
    }

    return $this->renderForm('route/add.html.twig', [
      'form' => $form,
    ]);
  }

  #[IsGranted('ROLE_OUVREUR')]
  #[Route('/route/edit/{id}/{gymId}', name: 'route_edit', defaults: ["gymId" => null])]
  public function edit(
    $id,
    $gymId,
    EntityManagerInterface $em,
    Request $request
  ): Response {
    $repo = $this->getDoctrine()->getRepository(RouteEntity::class);
    $route = $repo->findOneBy(["id" => $id]);

    $gym =
      $this->user->getGym() ??
      $this->getDoctrine()
        ->getRepository(Gym::class)
        ->findOneBy([
          "id" => $gymId,
          "franchise" => $this->user->getFranchise()->getId(),
        ]);

    if ($gym && $gym->getId() == $route->getGym()->getId()) {
      $form = $this->createForm(RouteType::class)->setData($route);

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $routeEdit = $form->getData();

        $route->setName($routeEdit->getName());
        $route->setDifficulty($routeEdit->getDifficulty());

        $em->persist($route);
        $em->flush();

        if (
          $this->user
            ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
            : false
        ) {
          return $this->redirectToRoute("gym_routes_franchise", [
            "id" => $gymId,
          ]);
        }
        return $this->redirectToRoute("gym_routes");
      }

      return $this->renderForm('route/edit.html.twig', [
        'route' => $route,
        'form_edit' => $form,
      ]);
    }

    if (
      $this->user
        ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
        : false
    ) {
      return $this->redirectToRoute("gym_routes_franchise", ["id" => $gymId]);
    }
    return $this->redirectToRoute("gym_routes");
  }

  #[IsGranted('ROLE_OUVREUR')]
  #[Route('/route/remove/{id}', name: 'route_remove')]
  public function remove($id, EntityManagerInterface $em)
  {
    $repo = $this->getDoctrine()->getRepository(RouteEntity::class);

    $route = $repo->findOneBy(["id" => $id]);

    $verifGym = $this->user->getGym() ?? null;

    if ($verifGym !== null) {
      if ($verifGym->getId() == $route->getGym()->getId()) {
        $em->remove($route);
        $em->flush();
      } else {
        $this->redirectToRoute('accueil');
      }
    } else {
      if (
        $this->user->getFranchise()->getId() ==
        $route
          ->getGym()
          ->getFranchise()
          ->getId()
      ) {
        $em->remove($route);
        $em->flush();

        return $this->redirectToRoute("gym_routes_franchise", [
          "id" => $route->getGym()->getId(),
        ]);
      } else {
        $this->redirectToRoute('accueil');
      }
    }

    return $this->redirectToRoute("gym_routes");
  }

  #[IsGranted('ROLE_OUVREUR')]
  #[Route('/route/open/{id}/{state}', name: 'route_open')]
  public function open($id, $state)
  {
    $em = $this->getDoctrine()->getManager();
    $repo = $this->getDoctrine()->getRepository(RouteEntity::class);

    $route = $repo->findOneBy(["id" => $id]);

    $verifGym = $this->user
      ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles())
      : false;

    if (!$verifGym) {
      if ($this->user->getGym()->getId() == $route->getGym()->getId()) {
        if ($state == "true") {
          $route->setOpened(1);
        } else {
          $route->setOpened(0);
        }
        $em->persist($route);
        $em->flush();
      } else {
        $this->redirectToRoute('accueil');
      }
    } else {
      if (
        $this->user->getFranchise()->getId() ==
        $route
          ->getGym()
          ->getFranchise()
          ->getId()
      ) {
        if ($state == "true") {
          $route->setOpened(1);
        } else {
          $route->setOpened(0);
        }
        $em->persist($route);
        $em->flush();

        return $this->redirectToRoute("gym_routes_franchise", [
          "id" => $route->getGym()->getId(),
        ]);
      } else {
        $this->redirectToRoute('accueil');
      }
    }

    return $this->redirectToRoute("gym_routes");
  }

  #[IsGranted('ROLE_USER')]
  #[Route('/route/resolved/{routeId}', name: 'route_resolved', defaults: ["routeId" => null], methods: ['POST'])]
  public function resolved(ManagerRegistry $doctrine, $routeId): Response
  {
    $success = true;

    $entityManager = $doctrine->getManager();
    $resolvedRoute = $entityManager
      ->getRepository(RouteEntity::class)
      ->find($routeId);

    if ($resolvedRoute->getId() !== null) {
      $this->user->addRoute($resolvedRoute);
      $entityManager->flush();
    } else {
      $success = false;
    }

    return new JsonResponse(['success' => $success]);
  }

  #[IsGranted('ROLE_USER')]
  #[Route('/route/unresolved/{routeId}', name: 'route_unresolved', defaults: ["routeId" => null], methods: ['POST'])]
  public function unresolved(ManagerRegistry $doctrine, $routeId): Response
  {
    $success = true;

    $entityManager = $doctrine->getManager();
    $resolvedRoute = $entityManager
      ->getRepository(RouteEntity::class)
      ->find($routeId);

    if ($resolvedRoute->getId() !== null) {
      $this->user->removeRoute($resolvedRoute);
      $entityManager->flush();
    } else {
      $success = false;
    }

    return new JsonResponse(['success' => $success]);
  }

  // #[IsGranted('ROLE_USER')]
  #[Route('/route/{routeId}', name: 'route_display')]
  public function route_display(ManagerRegistry $doctrine, $routeId): Response
  {
    $entityManager = $doctrine->getManager();
    $route      = $entityManager->getRepository(RouteEntity::class)->find($routeId);
    $comments   = $route->getMessages();
    $gym        = $route->getGym();
    $franchise  = $gym->getFranchise();

    $resolved = false;
    $securityContext = $this->container->get('security.authorization_checker');
    if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
      $resolvedRoutes = $this->user->getRoutes();
      foreach($resolvedRoutes as $key => $value) {
        if($value->getId() === intval($routeId)) {
          $resolved = true;
          break;
        }
      }
    }

    return $this->render('route/index.html.twig', [
      'user'      => $this->user,
      'franchise' => $franchise,
      'gym'       => $gym,
      'route'     => $route,
      'comments'  => $comments,
      'resolved'  => $resolved
      ]);
  }

  #[IsGranted('ROLE_USER')]
  #[Route('/route/addMessage/{routeId}', name: 'route_add_message', defaults: ["routeId" => null], methods: ['POST'])]
  public function addMessage(ManagerRegistry $doctrine, Request $request, $routeId): Response
  {
    $entityManager = $doctrine->getManager();

    $ajaxParams = $request->request->all();

    $messageContent = $ajaxParams['message'];

    $success = true;
    $message = new Message();

    $message->setMessageContent($messageContent);
    $message->setDateCreated(new \DateTime());
    $message->setUserId($this->user);
    $message->setRouteId($entityManager->getRepository(RouteEntity::class)->find($routeId));

    $entityManager->persist($message);

    $entityManager->flush();

    // if ($resolvedRoute->getId() !== null) {
    //   $this->user->addRoute($resolvedRoute);
    //   $entityManager->flush();
    // } else {
    //   $success = false;
    // }

    return new JsonResponse(['success' => $success]);
  }
}