<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Event;
use App\Entity\Franchise;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class EventUserController extends AbstractController
{

    private $user;

    public function __construct(Security $security)
    {
        $this->user = $security->getUser();
    }

    #[Route('/events', name: 'list_all_events')]
    public function eventsList(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $events = $entityManager->getRepository(Event::class)->findAll();

        // dd($events[0]);

        return $this->render('event/index.html.twig', [
            'events'    => $events,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/event/resolved/{eventId}', name: 'event_resolved', defaults: ["eventId" => null], methods: ['POST'])]
    public function resolved(ManagerRegistry $doctrine, $eventId): Response
    {
        $success = true;

        $entityManager = $doctrine->getManager();
        $resolvedEvent = $entityManager
            ->getRepository(Event::class)
            ->find($eventId);

        if ($resolvedEvent->getId() !== null) {
            $this->user->addEvent($resolvedEvent);
            $entityManager->flush();
        } else {
            $success = false;
        }

        return new JsonResponse(['success' => $success]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/event/unresolved/{eventId}', name: 'event_unresolved', defaults: ["eventId" => null], methods: ['POST'])]
    public function unresolved(ManagerRegistry $doctrine, $eventId): Response
    {
        $success = true;

        $entityManager = $doctrine->getManager();
        $resolvedEvent = $entityManager
            ->getRepository(Event::class)
            ->find($eventId);

        if ($resolvedEvent->getId() !== null) {
            $this->user->removeEvent($resolvedEvent);
            $entityManager->flush();
        } else {
            $success = false;
        }

        return new JsonResponse(['success' => $success]);
    }
}
