<?php

namespace App\Tests\Api;

use App\Tests\Tools\AbstractApiTestCase;
use App\Tests\Tools\User;

class GreetingsTest extends AbstractApiTestCase
{
    public function testCreateGreeting(): void
    {
        $token = (new User())->getToken( static::createClient(),'test@example.com','$3CR3T');
        static::createClient()->request('POST', '/api/greetings',
            [
                'json' => ['name' => 'Kévin'],
                'auth_bearer'=> $token,
            ]
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            '@context' => '/contexts/Greeting',
            '@type' => 'Greeting',
            'name' => 'Kévin',
        ]);
    }
}
