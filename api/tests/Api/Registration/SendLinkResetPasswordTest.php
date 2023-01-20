<?php

namespace App\Tests\Api\Registration;

use App\Tests\Tools\AbstractApiTestCase;

class SendLinkResetPasswordTest extends AbstractApiTestCase
{
    const API_SEND_LINK = "/api/sendLinkResetPassword/";
    const HEADERS = ['Content-Type' => 'application/json'];

    /**
     * @dataProvider sendLinkResetPassword
     */
    public function testSendLinkResetPassword($method, $url, $headersParams, $codeResponse, $jsonSontains): void
    {
        self::createClient()->request($method, $url);
        $this->assertResponseStatusCodeSame($codeResponse);
        $this->assertJsonContains($jsonSontains);
    }

    public function sendLinkResetPassword() :array
    {
        return [
            [
                'GET',
                self::API_SEND_LINK."totoro",
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
                'GET',
                self::API_SEND_LINK."test@example.com",
                [
                    'headers' => self::HEADERS
                ],
                200,
                [
                    "@context"=>"/contexts/User",
                    '@id' => self::API_SEND_LINK.'test@example.com',
                    "@type"=>"User",
                    'id' => 1,
                    'userName' => 'test@example.com',
                    'roles' =>[
                        1 => 'ROLE_USER',
                    ],
                    'isVerified' => true,
                ]
            ],
        ];
    }
}
