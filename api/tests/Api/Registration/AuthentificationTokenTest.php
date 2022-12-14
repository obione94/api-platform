<?php

namespace App\Tests\Api\Registration;

use App\Tests\Tools\AbstractApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class AuthentificationTokenTest extends AbstractApiTestCase
{

    const API_REGISTRATION = "/authentication_token";
    const HEADERS = ['Content-Type' => 'application/json'];

    /**
     * @dataProvider authenticationTokenBadMethodProvider
     * @throws TransportExceptionInterface
     */
    public function testAuthenticationTokenBadMethod($method, $url, $headersParams, $codeResponse): void
    {
        self::createClient()->request($method, $url, $headersParams);
        $this->assertResponseStatusCodeSame($codeResponse);
    }

    public function authenticationTokenBadMethodProvider() :array
    {
        return [
            [
                'GET',
                self::API_REGISTRATION,
                [
                    'headers' => self::HEADERS,
                    'json' => [
                        'userName' => 'test@example.com',
                        'password' => 'totoro',
                    ]
                ],
                405
            ],
            [
                'PUT',
                self::API_REGISTRATION,
                [
                    'headers' => self::HEADERS,
                    'json' => [
                        'userName' => 'test@example.com',
                        'password' => 'totoro',
                    ]
                ],
                405
            ],
            [
                'PATCH',
                self::API_REGISTRATION,
                [
                    'headers' => self::HEADERS,
                    'json' => [
                        'userName' => 'test@example.com',
                        'password' => 'totoro',
                    ]
                ],
                405
            ],
            [
                'DELETE',
                self::API_REGISTRATION,
                [
                    'headers' => self::HEADERS,
                    'json' => [
                        'userName' => 'test@example.com',
                        'password' => 'totoro',
                    ]
                ],
                405
            ]
        ];
    }

    /**
     * @dataProvider authenticationTokenBadIdentifiantProvider
     */
    public function testAuthenticationTokenBadIdentifiant($method, $url, $headersParams, $codeResponse, $jsoncontains): void
    {
        self::createClient()->request($method, $url, $headersParams);
        $this->assertResponseStatusCodeSame($codeResponse);
        $this->assertJsonContains($jsoncontains);

    }

    public function authenticationTokenBadIdentifiantProvider() :array
    {
        return [
            [
                'POST',
                self::API_REGISTRATION,
                [
                    'headers' => self::HEADERS,
                    'json' => [
                        'userName' => 'test@examples.com',
                        'password' => 'totoro',
                    ]
                ],
                401,
                [
                    "code"=> 401,
                    "message"=> "Invalid credentials.",
                ]
            ],
        ];
    }

    /**
     * @dataProvider authenticationTokenVerifyEmailProvider
     */
    public function testAuthenticationTokenVerifyEmail($method, $url, $headersParams, $codeResponse, $jsoncontains): void
    {
        self::createClient()->request($method, $url, $headersParams);
        $this->assertResponseStatusCodeSame($codeResponse);
        $this->assertJsonContains($jsoncontains);
    }

    public function authenticationTokenVerifyEmailProvider() :array
    {
        return [
            [
                'POST',
                self::API_REGISTRATION,
                [
                    'headers' => self::HEADERS,
                    'json' => [
                        'userName' => 'test@example2.com',
                        'password' => '$3CR3T',
                    ]
                ],
                401,
                [
                    "code"=> 401,
                    "message"=> "Your user account no verify.",
                ]
            ],
        ];
    }

}
