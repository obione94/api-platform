<?php

namespace App\Tests\Api\Registration;

use App\Tests\Tools\AbstractApiTestCase;

class SendLinkVerifyEmailTest extends AbstractApiTestCase
{

    const API_REGISTRATION = "/api/registration";
    const HEADERS = ['Content-Type' => 'application/json'];



    /**
     * @dataProvider sendLinkVerifyEmail
     */
    public function testSendLinkVerifyEmail($method, $url, $headersParams, $codeResponse, $jsonSontains): void
    {
        self::createClient()->request($method, $url);
        $this->assertResponseStatusCodeSame($codeResponse);
        $this->assertJsonContains($jsonSontains);
    }

    public function sendLinkVerifyEmail() :array
    {
        return [
            [
                'GET',
                "/api/sendLinkVerifyEmail/test@example2.com",
                [
                    'headers' => self::HEADERS
                ],
                200,
                [
                    "id" => 2,
                    "userName" => "test@example2.com",
                    "roles" => ["ROLE_ADMIN","ROLE_USER"],
                    "isVerified" => false
                ]
            ],
            [
                'GET',
                "/api/sendLinkVerifyEmail/totoro",
                [
                    'headers' => self::HEADERS
                ],
                404,
                [
                    "@context"=>"/contexts/Error",
                    "@type"=>"hydra:Error",
                    "hydra:title"=>"An error occurred",
                    "hydra:description"=>"The mail \"totoro\" does not exist."
                ]
            ],
            [
                'POST',
                "/api/sendLinkVerifyEmail/totoro",
                [
                    'headers' => self::HEADERS
                ],
                405,
                [
                    "@context"=>"/contexts/Error",
                    "@type"=>"hydra:Error",
                    "hydra:title"=>"An error occurred",
                ]
            ],
            [
                'PUT',
                "/api/sendLinkVerifyEmail/totoro",
                [
                    'headers' => self::HEADERS
                ],
                405,
                [
                    "@context"=>"/contexts/Error",
                    "@type"=>"hydra:Error",
                    "hydra:title"=>"An error occurred",
                ]
            ],
            [
                'DELETE',
                "/api/sendLinkVerifyEmail/totoro",
                [
                    'headers' => self::HEADERS
                ],
                405,
                [
                    "@context"=>"/contexts/Error",
                    "@type"=>"hydra:Error",
                    "hydra:title"=>"An error occurred",
                ]
            ],
            [
                'PATCH',
                "/api/sendLinkVerifyEmail/totoro",
                [
                    'headers' => self::HEADERS
                ],
                405,
                [
                    "@context"=>"/contexts/Error",
                    "@type"=>"hydra:Error",
                    "hydra:title"=>"An error occurred",
                ]
            ],
        ];
    }

}
