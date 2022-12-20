<?php

namespace App\DTO\User;

use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(denormalizationContext: ["groups" => ["token"]],)]
final class UserConfirmationEmailDTO
{
    #[Groups(["token"])]
    public string $token;

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
