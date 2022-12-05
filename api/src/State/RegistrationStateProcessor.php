<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Security\Encoder\NixillaJWTEncoder;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class RegistrationStateProcessor implements ProcessorInterface
{

    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private NixillaJWTEncoder $nixillaJWTEncoder,
        private MailerInterface $mailer,
    )
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
            return;
        }

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        $this->sendWelcomeEmail($data);
    }

    private function sendWelcomeEmail(User $user)
    {
        $data = ["userName" => $user->getUserName()];
        $token = $this->nixillaJWTEncoder->encode($data);
        $email = new TemplatedEmail();
        $email->from('lemanour.david@gmail.com');
        $email->to($user->getUserName());
        $email->htmlTemplate('registration/confirmation_email.html.twig');
        $email->context(['token' => $token]);
        $this->mailer->send($email);
    }
}
