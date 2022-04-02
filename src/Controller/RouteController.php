<?php

namespace App\Controller;

use \App\Entity\Route as RouteEntity;
use App\Entity\Gym;

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


class RouteController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[IsGranted('ROLE_ADMIN_SALLE')]
    #[Route('/route/add/{gymId}', name: 'route_add', defaults: ["gymId" => null])]
    public function add($gymId, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(RouteType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $route = $form->getData();

            $route->setGym($this->user->getGym() ?? $this->getDoctrine()
                    ->getRepository(Gym::class)
                    ->findOneBy(["id" => $gymId, "franchise" => $this->user->getFranchise()->getId()]));

            if ($route->getGym()) {
                $em->persist($form->getData());
                $em->flush();

                if ($this->user ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles()) : false)
                    return $this->redirectToRoute("gym_routes_franchise", ["id" => $gymId]);
                return $this->redirectToRoute("gym_routes");
            }
        }

        return $this->renderForm('route/add.html.twig', [
            'form_add' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN_SALLE')]
    #[Route('/route/edit/{id}/{gymId}', name: 'route_edit', defaults: ["gymId" => null])]
    public function edit($id, $gymId, EntityManagerInterface $em, Request $request): Response
    {
        $repo = $this->getDoctrine()->getRepository(RouteEntity::class);
        $route = $repo->findOneBy(["id" => $id]);

        $gym = $this->user->getGym() ?? $this->getDoctrine()
                ->getRepository(Gym::class)
                ->findOneBy(["id" => $gymId, "franchise" => $this->user->getFranchise()->getId()]);

        if ($gym && $gym->getId() == $route->getGym()->getId()) {

            $form = $this->createForm(RouteType::class)->setData($route);

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $routeEdit = $form->getData();

                $route->setName($routeEdit->getName());
                $route->setDifficulty($routeEdit->getDifficulty());

                $em->persist($route);
                $em->flush();

                if ($this->user ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles()) : false)
                    return $this->redirectToRoute("gym_routes_franchise", ["id" => $gymId]);
                return $this->redirectToRoute("gym_routes");
            }

            return $this->renderForm('route/edit.html.twig', [
                'route' => $route,
                'form_edit' => $form
            ]);
        }

        if ($this->user ? in_array("ROLE_ADMIN_FRANCHISE", $this->user->getRoles()) : false)
            return $this->redirectToRoute("gym_routes_franchise", ["id" => $gymId]);
        return $this->redirectToRoute("gym_routes");
    }

    #[IsGranted('ROLE_ADMIN_SALLE')]
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
            if ($this->user->getFranchise()->getId() == $route->getGym()->getFranchise()->getId()) {
                $em->remove($route);
                $em->flush();

                return $this->redirectToRoute("gym_routes_franchise", ["id" => $route->getGym()->getId()]);
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
                if ($state == "true") $route->setOpened(1);
                else $route->setOpened(0);
                $em->persist($route);
                $em->flush();
            } else {
                $this->redirectToRoute('accueil');
            }
        } else {
            if ($this->user->getFranchise()->getId() == $route->getGym()->getFranchise()->getId()) {
                if ($state == "true") $route->setOpened(1);
                else $route->setOpened(0);
                $em->persist($route);
                $em->flush();

                return $this->redirectToRoute("gym_routes_franchise", ["id" => $route->getGym()->getId()]);
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
        $resolvedRoute = $entityManager->getRepository(RouteEntity::class)->find($routeId);
        
        if($resolvedRoute->getId() !== null) {
            $this->user->addRoute($resolvedRoute);
            $entityManager->flush();
        } else {
            $success = false;
        }

        return new JsonResponse(array('success' => $success));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/route/unresolved/{routeId}', name: 'route_unresolved', defaults: ["routeId" => null], methods: ['POST'])]
    public function unresolved(ManagerRegistry $doctrine, $routeId): Response
    {
        $success = true;

        $entityManager = $doctrine->getManager();
        $resolvedRoute = $entityManager->getRepository(RouteEntity::class)->find($routeId);
        
        if($resolvedRoute->getId() !== null) {
            $this->user->removeRoute($resolvedRoute);
            $entityManager->flush();
        } else {
            $success = false;
        }

        return new JsonResponse(array('success' => $success));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/route/{routeId}', name: 'route_display')]
    public function route_display(ManagerRegistry $doctrine, $routeId): Response
    {
        $entityManager = $doctrine->getManager();
        $route      = $entityManager->getRepository(RouteEntity::class)->find($routeId);
        $gym        = $route->getGym();
        $franchise  = $gym->getFranchise();

        $resolvedRoutes = $this->user->getRoutes();
        $resolved = false;
        foreach($resolvedRoutes as $key => $value) {
            if($value->getId() === $routeId) {
                $resolved = true;
                break;
            }
        }

        return $this->render('route/index.html.twig', [
            'user'      => $this->user,
            'franchise' => $franchise,
            'gym'       => $gym,
            'route'     => $route,
            'resolved'  => $resolved
        ]);
    }
}