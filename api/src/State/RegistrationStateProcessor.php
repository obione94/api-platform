<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\Mail\MailVerifyEmail;

class RegistrationStateProcessor implements ProcessorInterface
{

    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ProcessorInterface $removeProcessor,
        private MailVerifyEmail $mailVerifyEmail
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
        $this->mailVerifyEmail->sendWelcomeEmail($data);
    }
}
