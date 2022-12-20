<?php

namespace App\DTO\User;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;

#[ApiResource(
    normalizationContext: ["groups" => ["email"]],
)]

class EmailDTO
{
    #[Groups(["email"])]
    public string $userEmail;

    /**
     * @return string
     */
    public function getUserEmail() : string
    {
        return $this->userEmail;
    }

    /**
     * @param string $userEmail
     */
    public function setUserEmail(string $userEmail) : void
    {
        $this->userEmail = $userEmail;
    }
}
