<?php

namespace App\Service\Mail;

use App\Entity\User;
use App\Security\Token\ConfirmationEmailToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailVerifyEmail
{

    public function __construct(protected MailerInterface $mailer, protected ConfirmationEmailToken $confirmationEmailToken)
    {
    }

    public function sendWelcomeEmail(User $user, int $duration = 600): void
    {
        $email = new TemplatedEmail();
        $email->from('lemanour.david@gmail.com');
        $email->to($user->getUserName());
        $email->htmlTemplate('registration/confirmation_email.html.twig');
        $email->context(['token' => $this->confirmationEmailToken->generateConfirmationEmailToken($user, $duration)]);
        $this->mailer->send($email);
    }

}
