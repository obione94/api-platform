<?php

namespace App\Security\Token;

use App\Security\Encoder\EncoderInterface;
use App\Security\Encoder\NixillaJWTEncoder;

abstract class AbstractTokenManager implements EncoderInterface
{

    public function __construct(
        private readonly NixillaJWTEncoder $nixillaJWTEncoder,
    )
    {
    }

    public function generateToken(array $payload = [], int $expires = 60000): string
    {
        $payload["expires"] = time()+$expires;

        return $this->encode($payload);
    }

    public function isValidToken(string $token): bool
    {
        $payload = $this->decode($token);

        return !($this->isExpiresToken((int) $payload["expires"] ?? 0));
    }

    public function decode($token): array
    {
        return $this->nixillaJWTEncoder->decode($token);
    }

    public function encode($payload): string
    {
        return $this->nixillaJWTEncoder->encode($payload);
    }

    public function isExpiresToken(int $expires): bool
    {
        return !(time() < $expires);
    }

}
