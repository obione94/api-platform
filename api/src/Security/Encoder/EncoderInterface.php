<?php

namespace App\Security\Encoder;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;

interface EncoderInterface extends JWTEncoderInterface
{
    public function isExpiresToken(int $expires): bool;
    public function generateToken(array $payload = [], int $expires = 600): string;
    public function isValidToken(string $token): bool;

}
