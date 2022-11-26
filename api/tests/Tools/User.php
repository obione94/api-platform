<?php

namespace App\Tests\Tools;

use ApiPlatform\Symfony\Bundle\Test\Client;

class User
{
    public static function getToken(Client $client ,string $userName,string $password): string
    {
        return ($client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'userName' => $userName,
                'password' => $password,
            ],
        ])->toArray())['token'];
    }
}
