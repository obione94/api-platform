<?php

namespace App\Service\Mail;

use App\Entity\User;
use App\Security\Token\ChangePasswordToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailResetPassword
{

    public function __construct(protected MailerInterface $mailer, protected ChangePasswordToken $confirmationEmailToken)
    {
    }

    public function send(User $user, int $duration = 600): void
    {
        $email = new TemplatedEmail();
        $email->from('lemanour.david@gmail.com');
        $email->to($user->getUserName());
        $email->htmlTemplate('Account/resetPassword.html.twig');
        $email->context(['token' => $this->confirmationEmailToken->generateChangePasswordToken($user, $duration)]);
        $this->mailer->send($email);
    }

}
