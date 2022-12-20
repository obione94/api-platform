<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Security\Token\ConfirmationEmailToken;
use App\Tests\Tools\AbstractApiTestCase;

class ConfirmationEmailControllerTest extends AbstractApiTestCase
{
    const  URI_CONFIRMATION_EMAIL ="/confirmation/email.html";

    /**
     * @dataProvider confirmationEmailProvider
     */
    public function testConfirmationEmailBadToken(string $tokenParameters): void
    {
        $client = self::createClient();
        $response = $client->request("GET", self::URI_CONFIRMATION_EMAIL.$tokenParameters);
        $this->assertMatchesRegularExpression('/token invalid/', $response->getContent());
    }

    public function confirmationEmailProvider(): array
    {
        $user = new User();
        $user->setUserName("test@example6.com");
        $confirmationEmailToken = static::getContainer()->get(ConfirmationEmailToken::class);

        return [
            [
                "?token=zefzoefjzoj",
            ],
            [
                "?token=".$confirmationEmailToken->generateConfirmationEmailToken($user, 0),
            ],
            [
                "?tata=zefzoefjzoj",
            ],
            [
                "",
            ],
        ];
    }


    public function testConfirmationEmailGoodToken(): void
    {
        $_em = static::getContainer()->get('doctrine');
        $repositoryUser = $_em->getRepository(User::class);
        $user = new User();
        $user->setUserName("test@example3.com");
        $password = static::getContainer()->get('security.password_hasher')->hashPassword($user, '$3CR3T');
        $user->setPassword($password);
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setIsVerified(false);
        $repositoryUser->save($user, true);

        $confirmationEmailToken = static::getContainer()->get(ConfirmationEmailToken::class);
        $response = self::createClient()->request(
            "GET",
            self::URI_CONFIRMATION_EMAIL."?token=" . $confirmationEmailToken->generateConfirmationEmailToken(
                $user,
                300
            )
        );

        $this->assertMatchesRegularExpression(
            '/Vous pouvez desormais vous connecter sur sur application préférée/',
            $response->getContent()
        );

        $userRepository = $repositoryUser->loadUserByIdentifier($user->getUserName());
        $this->assertTrue($userRepository->isIsVerified());
    }

}
