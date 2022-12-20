<?php

namespace App\Security\Token;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Encoder\NixillaJWTEncoder;

class ChangePasswordToken extends AbstractTokenManager
{
    public function __construct(
        private readonly NixillaJWTEncoder $nixillaJWTEncoder,
        private readonly UserRepository $userRepository
    )
    {
        parent::__construct($nixillaJWTEncoder);
    }

    public function getUserEmail(string $token) :string
    {
        return parent::decode($token)["userNameChangePassword"] ?? "";
    }

    public function generateChangePasswordToken(User $user, int $expires = 60000): string
    {
        return parent::generateToken(["userNameChangePassword" => $user->getUserName()], $expires);
    }

    public function isValidToken(string $token): bool
    {
        if (false === parent::isValidToken($token)) {
            return false;
        }

        $payload = parent::decode($token);

        return $this->isValidUser($payload['userNameChangePassword'] ?? "");
    }

    private function isValidUser(string $user): bool
    {
        return !(null === $this->userRepository->loadUserByIdentifier($user));
    }

}
