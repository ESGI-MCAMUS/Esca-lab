<?php

namespace App\Controller;

use App\Entity\Gym;
use App\Entity\RouteUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\AddFriendType;
use App\Form\AddEventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\User;
use App\Entity\Event;
use App\Form\UpdateProfilePictureType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;


#[IsGranted("ROLE_USER")]
class UserController extends AbstractController {

    private $user;

    public function __construct(Security $security) {
        $this->user = $security->getUser();
    }

    #[Route('/user', name: 'user')]
    public function index(Request $request, SluggerInterface $slugger): Response {

        $user = new User();
        $form = $this->createForm(UpdateProfilePictureType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $brochureFile */
            $profilePicture = $form->get('profile_picture')->getData();
            if ($profilePicture) {
                $originalFilename = pathinfo($profilePicture->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePicture->guessExtension();
                try {
                    $profilePicture->move(
                        $this->getParameter('users_pp'),
                        $newFilename
                    );
                    $this->user->setPicture($newFilename);
                } catch (FileException $e) {
                    $this->user->setPicture("");
                }
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($this->user);
                $entityManager->flush();
            }

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('user');
        }

        $finishedWaysCount = 0;
        $chartData = [];

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            $url = "https://";
        else
            $url = "http://";
        // Append the host(domain name, ip) to the URL.   
        $url .= $_SERVER['HTTP_HOST'];
        $url .= "/user/friends/add/";
        $url .= $this->user->getId();
        $url .= "/qrcode";
        // Create QR code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($url)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(150)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();
        if ($this->isGranted('ROLE_ADMIN_SALLE')) {
            $this->setInformations();
        } elseif ($this->isGranted('ROLE_OUVREUR')) {
            $this->setInformations();

            $finishedWays = $this->user->getRoutes()->getValues();

            foreach ($finishedWays as $finishedWay) {
                if (array_key_exists($finishedWay->getDifficulty(), $chartData)) {
                    $chartData[$finishedWay->getDifficulty()]++;
                } else {
                    $chartData[$finishedWay->getDifficulty()] = 1;
                }
            }
            ksort($chartData);
            $finishedWaysCount = sizeof($finishedWays);
        } else {
            $finishedWays = $this->user->getRoutes()->getValues();

            foreach ($finishedWays as $finishedWay) {
                if (array_key_exists($finishedWay->getDifficulty(), $chartData)) {
                    $chartData[$finishedWay->getDifficulty()]++;
                } else {
                    $chartData[$finishedWay->getDifficulty()] = 1;
                }
            }
            ksort($chartData);
            $finishedWaysCount = sizeof($finishedWays);
        }

        return $this->renderForm('user/resume.html.twig', [
            'qrcode' => $result->getDataUri(),
            'form' => $form,
            'finishedWaysCount' => $finishedWaysCount,
            'chartData' => $chartData,
        ]);
    }

    #[Route('/user/resume', name: 'resumeUser')]
    public function resumeUser(): Response {
        return $this->render('user/resume.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/user/events', name: 'eventsUser')]
    public function eventsUser(ManagerRegistry $doctrine, EntityManagerInterface $em, Request $request, UserRepository $userRepository): Response {

        $gyms = $this->getDoctrine()->getManager()->getRepository(\App\Entity\Gym::class)->findAll();

        $form = $this->createForm(AddEventType::class, ['gyms' => $gyms]);
        $form->handleRequest($request);

        $liste_user = [];

        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form);
            // dd($form->get('gymId')->getData());

            $entityManager = $this->getDoctrine()->getManager();

            $gym_event = $entityManager->getRepository(Gym::class)->find($form->get('gymId')->getData());

            $new_event = new Event();
            $new_event->setTitle($form->get('title')->getData());
            $new_event->setDescription($form->get('description')->getData());
            $new_event->setEventDate($form->get('eventDate')->getData());
            $new_event->setEndDate($form->get('endDate')->getData());
            $new_event->setEndDate($form->get('endDate')->getData());
            $new_event->setGym($gym_event);
            $new_event->setCreator($this->user);

            $entityManager->persist($new_event);
            $entityManager->flush();

            return $this->redirectToRoute('eventsUser');
        }
        // dd($routes);

        return $this->renderForm('user/events.html.twig', [
            'events'        => $this->user->getEvents(),
            'form_add'      => $form,
            'hidden_uri'    => $request->getUri(),
            'routes'        => $gyms,
            'user_events'   => $this->user->getCreatedEvents(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/user/event/delete/{eventId}', name: 'event_delete', defaults: ["eventId" => null], methods: ['POST'])]
    public function resolved(ManagerRegistry $doctrine, $eventId): Response
    {
        $success = true;

        $entityManager = $doctrine->getManager();
        $eventToDelete = $entityManager
            ->getRepository(Event::class)
            ->find($eventId);

        $entityManager->remove($eventToDelete);
        $entityManager->flush();

        return new JsonResponse(['success' => $success]);
    }

    #[Route('/user/friends', name: 'friendsUser')]
    public function friendsUser(): Response {
        $friends = $this->user->getFriends();

        return $this->render('user/friends.html.twig', [
            'controller_name' => 'UserController', 'friends' => $friends
        ]);
    }

    #[Route('/user/routes', name: 'finishedRoutes')]
    public function finishedRoutes(): Response {
        $routes = $this->user->getRoutes();

        return $this->render('user/finishedRoutes.html.twig', [
            'routes' => $routes
        ]);
    }

    #[Route('/user/gyms', name: 'favoriteGyms')]
    public function favoriteGyms(): Response {
        $favoriteGyms = $this->user->getFavoriteGyms();

        return $this->render('user/favoriteGyms.html.twig', [
            'gyms' => $favoriteGyms
        ]);
    }

    /**
     * Permet d'appeler la page ou se trouve le formulaire de recherche
     * des amis
     */
    #[Route('/user/friends/add', name: 'addFriendsUser')]
    public function addFriendsUser(ManagerRegistry $doctrine, EntityManagerInterface $em, Request $request, UserRepository $userRepository): Response {

        $form = $this->createForm(AddFriendType::class);
        $form->handleRequest($request);

        $liste_user = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $search_user = $form->getData()['username'];

            // we retrieve our friends so we don't get friends and randoms
            $friends = $this->user->getFriends()->getValues();

            $ids_arra = [];

            foreach ($friends as $friend) {
                $ids_arra[] = $friend->getId();
            }

            $entityManager = $doctrine->getManager();
            $liste_user = $entityManager->getRepository(User::class)->findAllUserMatchingName($search_user);

            // we remove the users that are already friends with us
            $user_array = [];
            foreach ($liste_user as $user) {
                if (!in_array($user->getId(), $ids_arra)) {
                    $user_array[] = $user;
                }
            }
            $liste_user = $user_array;
        }

        return $this->renderForm('user/add-friends.html.twig', [
            'form_add'      => $form,
            'liste_user'    => $liste_user,
            'hidden_uri'    => $request->getUri()
        ]);
    }

    #[Route('/user/friends/add/{userId}', name: 'addFriendsUserId', defaults: ["userId" => null], methods: ['POST'])]
    public function addFriendsUserId(ManagerRegistry $doctrine, $userId): Response {
        $success = true;

        $entityManager = $doctrine->getManager();
        $newFriend = $entityManager->getRepository(User::class)->find($userId);

        if ($newFriend->getId() !== null) {
            $this->user->addFriend($newFriend);
            $entityManager->flush();
        } else {
            $success = false;
        }

        return new JsonResponse(array('success' => $success));
    }

    // Route to add a friend from the QR code
    #[Route('/user/friends/add/{userId}/qrcode', name: 'addFriendsUserIdQrCode')]
    public function addFriendsUserIdQrCode(ManagerRegistry $doctrine, $userId): Response {

        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
            $entityManager = $doctrine->getManager();
            $newFriend = $entityManager->getRepository(User::class)->find($userId);

            if ($newFriend->getId() !== null) {
                $this->user->addFriend($newFriend);
                $entityManager->flush();
            }
        } else {
            // Redirect to the login page with redirectTo page set to the current page you are coming from
            return $this->redirectToRoute('login', ['redirect_to' => $this->generateUrl('addFriendsUserIdQrCode', ['userId' => $userId])]);
        }

        return $this->redirectToRoute('friendsUser');
    }

    #[Route('/user/friends/remove/{userId}', name: 'removeFriendsUserId', defaults: ["userId" => null], methods: ['POST'])]
    public function removeFriendsUserId(ManagerRegistry $doctrine, $userId): Response {
        $success = true;

        $entityManager = $doctrine->getManager();
        $newFriend = $entityManager->getRepository(User::class)->find($userId);

        if ($newFriend->getId() !== null) {
            $this->user->removeFriend($newFriend);
            $entityManager->flush();
        } else {
            $success = false;
        }

        return new JsonResponse(array('success' => $success));
    }

    #[Route('/user/medias', name: 'mediasUser')]
    public function mediasUser(): Response {
        return $this->render('user/medias.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    private function setInformations() {
        $waysCount = 0;

        if ($this->isGranted("ROLE_SUPER_ADMIN")) {
            $waysCount = count($this->getDoctrine()->getManager()->getRepository(\App\Entity\Route::class)->findAll());
        } elseif ($this->isGranted("ROLE_ADMIN_FRANCHISE") && $this->user->getFranchise() !== null) {
            foreach ($this->user->getFranchise()->getGyms() as $gym) {
                $waysCount += count($gym->getRoutes());
            }
        } else {
            if ($this->user->getGym() !== null) {
                $waysCount = count($this->user->getGym()->getRoutes());
            }
        }

        $this->get('session')->set('ways_count', $waysCount);
    }
}