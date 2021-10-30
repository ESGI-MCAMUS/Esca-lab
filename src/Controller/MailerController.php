<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    public function sendEmailOTP($to, $otp, $firstname)
    {
        $html =
            '<div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
  <div style="margin:50px auto;width:70%;padding:20px 0">
    <div style="border-bottom:1px solid #eee">
      <a href="" style="font-size:1.4em;color: #00466a;text-decoration:none;font-weight:600">Esca\'Lab</a>
    </div>
    <p style="font-size:1.1em">Hello ' .
            $firstname .
            '</p>
    <p>Merci d\'avoir créé un compte sur Esca\'Lab ! Voici votre code vous permettant d\'activer votre compte</p>
    <h2 style="background: #00466a;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">' .
            $otp .
            '</h2>
    <p style="font-size:0.9em;">L\'Équipe Esca\'Lab</p>
    <hr style="border:none;border-top:1px solid #eee" />
    <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
      <p>Esca\'Lab</p>
      <p>242 Rue du Faubourg Saint-Antoine</p>
      <p>Paris, France</p>
    </div>
  </div>
</div>';
        $transport = Transport::fromDsn($_SERVER['MAILER_DSN']);
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from(new Address('escalab@turtlecorp.fr', 'Esca\'Lab'))
            ->to($to)
            ->priority(Email::PRIORITY_HIGH)
            ->subject($firstname . ", votre code Esca'Lab!")
            ->html($html);

        try {
            $mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            return $e;
        }
    }
}
