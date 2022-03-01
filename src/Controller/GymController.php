<?php

namespace App\Controller;

use App\Entity\Gym;
use App\Entity\Route as RouteEntity;
use App\Form\GymType;
use App\Form\RouteType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[IsGranted('ROLE_ADMIN_SALLE')]
class GymController extends AbstractController
{
    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/gym/add', name: 'gym_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(GymType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $gym = $form->getData();

            $gym->setFranchise($this->user->getFranchise());

            $em->persist($form->getData());
            $em->flush();

            return $this->redirectToRoute("franchise_gyms");
        }

        return $this->renderForm('gym/add.html.twig', [
            'form_add' => $form,
        ]);
    }

    #[Route('/gym/edit/{id}', name: 'gym_edit')]
    public function edit($id, EntityManagerInterface $em, Request $request): Response
    {
        $repo = $this->getDoctrine()->getRepository(Gym::class);
        $gym = $repo->findOneBy(["id" => $id]);

        $form = $this->createForm(GymType::class)->setData($gym);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $gymEdit = $form->getData();

            $gym->setName($gymEdit->getName());
            $gym->setSize($gymEdit->getSize());
            $gym->setAddress($gymEdit->getAddress());
            $gym->setCity($gymEdit->getCity());
            $gym->setPc($gymEdit->getPc());

            $em->persist($gym);
            $em->flush();

            return $this->redirectToRoute("franchise_gyms");
        }

        return $this->renderForm('gym/edit.html.twig', [
            'route' => $gym,
            'form_edit' => $form
        ]);
    }

    #[Route('/gym/remove/{id}', name: 'gym_remove')]
    public function remove($id, EntityManagerInterface $em)
    {
        $repo = $this->getDoctrine()->getRepository(Gym::class);

        $gym = $repo->findOneBy(["id" => $id]);

        if ($this->user->getFranchise()->getId() == $gym->getFranchise()->getId()) {
            $em->remove($gym);
            $em->flush();
        } else {
            $this->redirectToRoute('accueil');
        }

        return $this->redirectToRoute("franchise_gyms");
    }
}
