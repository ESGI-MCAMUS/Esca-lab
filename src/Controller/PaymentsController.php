<?php

namespace App\Controller;

use App\Entity\Payments;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use App\Form\GlobalSearchType;
use Symfony\Component\HttpFoundation\Request;

class PaymentsController extends AbstractController
{
  private $user;

  public function __construct(Security $security)
  {
    $this->user = $security->getUser();
  }

  #[IsGranted("ROLE_ADMIN_FRANCHISE")]
  #[Route('/franchise/paiements', name: 'app_payments')]
  public function showPayments(Request $request): Response
  {
    $this->setInformations();
    $form = $this->createForm(GlobalSearchType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $search = $form->get('search')->getData();

      $repo = $this->getDoctrine()
        ->getManager()
        ->getRepository(Payments::class);
      $payments = $repo->search('%' . $search . '%');

      return $this->renderForm('admin/users.html.twig', [
        'payments' => $payments,
        'form' => $form,
      ]);
    } else {
      $repo = $this->getDoctrine()
        ->getManager()
        ->getRepository(Payments::class);
      $payments = $repo->findBy(
        [
          'franchise' => $this->user->getFranchise()->getId(),
        ],
        [
          'updated_at' => 'DESC',
        ]
      );
      return $this->renderForm('payments/index.html.twig', [
        'payments' => $payments,
        'form' => $form,
      ]);
    }
  }

  #[IsGranted("ROLE_ADMIN_FRANCHISE")]
  #[Route('/franchise/checkout/{id}', name: 'app_init_payment')]
  public function initPayment($id)
  {
    \Stripe\Stripe::setApiKey($_SERVER['STRIPE_API_KEY']);

    $token = $this->uuidv4();

    $repo = $this->getDoctrine()
      ->getManager()
      ->getRepository(Payments::class);
    $payment = $repo->find($id);
    $payment->setToken($token);
    $payment->setUpdatedAt(new \DateTime());
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($payment);
    $entityManager->flush();

    $URL_SITE =
      'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'];

    $checkout_session = \Stripe\Checkout\Session::create([
      'payment_method_types' => ['card'],
      'line_items' => [
        [
          'price_data' => [
            'currency' => 'eur',
            'unit_amount' => $payment->getAmount(),
            'product_data' => [
              'name' =>
                $payment->getType() === "gym"
                  ? "Paiement pour une salle"
                  : "Paiement pour une voie",
              'images' => ["https://i.imgur.com/khcgl8T.png"],
            ],
          ],
          'quantity' => 1,
        ],
      ],
      'mode' => 'payment',
      'success_url' =>
        $URL_SITE . '/franchise/checkout/success/' . $id . '/' . $token,
      'cancel_url' =>
        $URL_SITE . '/franchise/checkout/failed/' . $id . '/' . $token,
    ]);
    return $this->redirect($checkout_session->url, 303);
  }

  #[IsGranted("ROLE_ADMIN_FRANCHISE")]
  #[Route('/franchise/checkout/success/{id}/{token}', name: 'app_success_payment')]
  public function successPayment($id, $token)
  {
    $repo = $this->getDoctrine()
      ->getManager()
      ->getRepository(Payments::class);
    $payment = $repo->find($id);
    if ($payment->getToken() === $token) {
      $payment->setStatus("success");
      $payment->setUpdatedAt(new \DateTime());
      $payment->setToken(null);
      $em = $this->getDoctrine()->getManager();
      $em->persist($payment);
      $em->flush();
      $this->addFlash(
        'success',
        'Le paiement n°' . $id . ' a été validé avec succès !'
      );
    } else {
      $payment->setStatus("failed");
      $payment->setUpdatedAt(new \DateTime());
      $payment->setToken(null);
      $em = $this->getDoctrine()->getManager();
      $em->persist($payment);
      $em->flush();
      $this->addFlash(
        'failed_token',
        'Quelque chose s\'est mal passé lors de la vérification du paiement n°' .
          $id .
          ', veuillez réessayer.'
      );
    }
    return $this->redirectToRoute('app_payments');
  }

  #[IsGranted("ROLE_ADMIN_FRANCHISE")]
  #[Route('/franchise/checkout/failed/{id}/{token}', name: 'app_failed_payment')]
  public function failedPayment($id, $token)
  {
    $repo = $this->getDoctrine()
      ->getManager()
      ->getRepository(Payments::class);
    $payment = $repo->find($id);
    if ($payment->getToken() === $token) {
      $payment->setStatus("failed");
      $payment->setUpdatedAt(new \DateTime());
      $payment->setToken(null);
      $em = $this->getDoctrine()->getManager();
      $em->persist($payment);
      $em->flush();
      $this->addFlash(
        'failed',
        'Quelque chose s\'est mal passé durant le paiement n°' .
          $id .
          ', veuillez réessayer.'
      );
    } else {
      $payment->setStatus("failed");
      $payment->setUpdatedAt(new \DateTime());
      $payment->setToken(null);
      $em = $this->getDoctrine()->getManager();
      $em->persist($payment);
      $em->flush();
      $this->addFlash(
        'failed_token',
        'Quelque chose s\'est mal passé lors de la vérification du paiement n°' .
          $id .
          '. <a href="/franchise/checkout/' .
          $id .
          '">Cliquez ici</a> pour réessayer.'
      );
    }
    return $this->redirectToRoute('app_payments');
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
    $gymsCount = count($this->user->getFranchise()->getGyms());

    // Payments
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
    $this->get('session')->set('ways_count', 0);
    $this->get('session')->set('payments', $total_payments);
  }

  private function uuidv4()
  {
    return sprintf(
      '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0xffff)
    );
  }
}