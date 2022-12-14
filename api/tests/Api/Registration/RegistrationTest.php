<?php

namespace App\Tests\Api\Registration;

use App\Tests\Tools\AbstractApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class RegistrationTest extends AbstractApiTestCase
{

    const API_REGISTRATION = "/api/registration";
    const HEADERS = ['Content-Type' => 'application/json'];


    /**
     * @dataProvider registrationProviderBadMethod
     * @throws TransportExceptionInterface
     */
    public function testRegistrationBadMethod($method, $url, $headersParams, $codeResponse): void
    {
        self::createClient()->request($method, $url, $headersParams);
        $this->assertResponseStatusCodeSame($codeResponse);
    }

    public function registrationProviderBadMethod() :array
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
                'POST',
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
     * @dataProvider registrationProviderError
     * @throws TransportExceptionInterface
     */
    public function testRegistrationError($method, $url, $headersParams, $codeResponse, $jsoncontains): void
    {
        self::createClient()->request($method, $url, $headersParams);
        $this->assertJsonContains($jsoncontains);
        $this->assertResponseStatusCodeSame($codeResponse);
    }

    public function registrationProviderError() :array
    {
        return [
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
                422,
                [
                    "hydra:title"=> "An error occurred",
                    "hydra:description"=> "userName: already existe",
                    "violations"=> [
                        [
                            "propertyPath"=> "userName",
                            "message"=> "already existe",
                            "code"=> "23bd9dbf-6b9b-41cd-a99e-4844bcf3077f"
                        ]
                    ]
                ]
            ],
            [
                'PUT',
                self::API_REGISTRATION,
                [
                    'headers' => self::HEADERS,
                    'json' => [
                        'userName' => 'testexample.com',
                        'password' => 'totoro',
                    ]
                ],
                422,
                [

                    "hydra:title"=> "An error occurred",
                    "hydra:description"=> "userName: This value is not a valid email address.",
                    "violations"=> [
                        [
                            "propertyPath"=> "userName",
                            "message"=> "This value is not a valid email address.",
                            "code"=> "bd79c0ab-ddba-46cc-a703-a7a4b08de310"
                        ]
                    ]
                ]
            ],
        ];
    }


}
