<?php

namespace App\Controller;

use App\Form\RouteType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use \App\Entity\Route as RouteEntity;
use Symfony\Component\Security\Core\Security;

class RouteController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[IsGranted('ROLE_ADMIN_SALLE')]
    #[Route('/route/add', name: 'route_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(RouteType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $route = $form->getData();

            $route->setGym($this->user->getGym());

            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute("gym_routes");
        }

        return $this->renderForm('route/add.html.twig', [
            'form_add' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN_SALLE')]
    #[Route('/route/edit/{id}', name: 'route_edit')]
    public function edit($id, EntityManagerInterface $em, Request $request): Response
    {
        $repo = $this->getDoctrine()->getRepository(RouteEntity::class);
        $route = $repo->findOneBy(["id" => $id]);

        $form = $this->createForm(RouteType::class)->setData($route);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $routeEdit = $form->getData();

            $route->setName($routeEdit->getName());
            $route->setDifficulty($routeEdit->getDifficulty());

            $em->persist($route);
            $em->flush();

            return $this->redirectToRoute("gym_routes");
        }

        return $this->renderForm('route/edit.html.twig', [
            'route' => $route,
            'form_edit' => $form
        ]);
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
                $em->remove($route);
                $em->flush();
            } else {
                $this->redirectToRoute('accueil');
            }
        }

        return $this->redirectToRoute("gym_routes");
    }

}
