<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Security\Token\ChangePasswordToken;
use App\Security\Token\ConfirmationEmailToken;
use App\Tests\Tools\AbstractApiTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ResetPasswordControllerTest extends AbstractApiTestCase
{
    const API_RESTE_PASSWORD = "/reset/password.html";
    const HEADERS = ['Content-Type' => 'application/json',];

    /**
     * @dataProvider resetPasswordBadOrNotToken
     * @throws TransportExceptionInterface
     */
    public function testResetPasswordBadOrNotToken($method, $url, $exception)
    {
        $this->expectException($exception);
        $tt = self::createClient()->request($method, $url)->getContent();
    }

    public function resetPasswordBadOrNotToken()
    {
        return [
            [
                'GET',
                self::API_RESTE_PASSWORD,
                ClientException::class
            ],
            [
                'GET',
                self::API_RESTE_PASSWORD."?token=tototto",
                ServerException::class
            ]
        ];
    }
    /**
     * @dataProvider resetPasswordInvalidToken
     * @throws TransportExceptionInterface
     */
    public function testResetPasswordInvalidToken(string $method, string $url, string $regext)
    {
        $tt = self::createClient()->request($method, $url)->getContent();
        $this->assertResponseIsSuccessful();
        $this->assertMatchesRegularExpression($regext, $tt);
    }

    public function resetPasswordInvalidToken()
    {
        $confirmationEmailToken = static::getContainer()->get(ChangePasswordToken::class);
        $usertoken = new User();
        $usertoken->setUserName("totoro");
        $tokenbadEmail = $confirmationEmailToken->generateChangePasswordToken($usertoken, 600);
        $usertoken->setUserName("test@example3.com");
        $tokenExpired = $confirmationEmailToken->generateChangePasswordToken($usertoken, 0);
        $usertoken->setUserName("test@example.com");
        $tokenGood = $confirmationEmailToken->generateChangePasswordToken($usertoken, 600);

        return [
           [
                'GET',
                self::API_RESTE_PASSWORD."?token=$tokenExpired",
                '/token invalid/',
            ],
            [
                'GET',
                self::API_RESTE_PASSWORD."?token=$tokenbadEmail",
                '/token invalid/',
            ],
            [
                'GET',
                self::API_RESTE_PASSWORD."?token=$tokenGood",
                '/change_password/',
            ],
        ];
    }
}
