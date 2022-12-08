<?php

namespace App\Security\Token;

use App\Security\Encoder\NixillaJWTEncoder;
use Symfony\Component\Mailer\MailerInterface;

class ChangePasswordToken extends AbstractTokenManager
{
    public function __construct(
        private readonly NixillaJWTEncoder $nixillaJWTEncoder,
    )
    {
        parent::__construct($nixillaJWTEncoder);
    }

}
