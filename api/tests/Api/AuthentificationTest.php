<?php

namespace App\Tests\Api;

use App\Tests\Tools\AbstractApiTestCase;

class AuthenticationTest extends AbstractApiTestCase
{
    public function testLogin(): void
    {
        $client = self::createClient();
        // retrieve a token
        $response = $client->request('POST', '/authentication_token', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'userName' => 'test@example.com',
                'password' => '$3CR3T',
            ],
        ]);

        $json = $response->toArray();
        $this->assertResponseIsSuccessful();
        $this->assertArrayHasKey('token', $json);

        // test not authorized
        $client->request('GET', '/api/greetings');
        $this->assertResponseStatusCodeSame(401);
        // test authorized
        $client->request('GET', '/api/greetings', ['auth_bearer' => $json['token']]);
        $this->assertResponseIsSuccessful();
    }
}

