<?php

namespace App\Security\Token;

use App\Repository\UserRepository;
use App\Security\Encoder\NixillaJWTEncoder;

class ConfirmationEmailToken extends AbstractTokenManager
{
    public function __construct(
        private readonly NixillaJWTEncoder $nixillaJWTEncoder,
        private readonly UserRepository $userRepository
    )
    {
            parent::__construct($nixillaJWTEncoder);
    }

    public function isValidToken(string $token): bool
    {
        if (false === parent::isValidToken($token)) {
            return false;
        }
        $payload = parent::decode($token);

        if (null === ($payload['userName']??null)) {
            return false;
        }

        if (false === $this->isValidUser($payload['userName'])) {
            return false;
        }

        return true;
    }

    public function isValidUser(string $user): bool
    {
        return !(null === $this->userRepository->loadUserByIdentifier($user));
    }
}
