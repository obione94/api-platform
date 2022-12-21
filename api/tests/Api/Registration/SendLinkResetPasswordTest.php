<?php

namespace App\Tests\Api\Registration;

use App\Security\Token\ChangePasswordToken;
use App\Service\Mail\MailResetPassword;
use App\Tests\Tools\AbstractApiTestCase;
use Symfony\Component\Mailer\MailerInterface;

class SendLinkResetPasswordTest extends AbstractApiTestCase
{
    const API_REGISTRATION = "/api/registration";
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
                "/api/sendLinkResetPassword/totoro",
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
                "/api/sendLinkResetPassword/test@example.com",
                [
                    'headers' => self::HEADERS
                ],
                200,
                [
                    "@context"=>"/contexts/User",
                    '@id' => '/api/sendLinkResetPassword/test@example.com',
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
