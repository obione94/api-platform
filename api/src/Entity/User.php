<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Controller\SendLinkResetPassword;
use App\Controller\SendLinkVerifyEmail;
use App\Controller\VerifyEmailController;
use App\Controller\RegistrationController;
use App\DTO\User\UserConfirmationEmailDTO;
use App\Repository\UserRepository;
use App\State\Registration\SendLink\SendLinkResetPasswordStateProcessor;
use App\State\RegistrationStateProcessor;
use App\State\UserConfirmationEmailProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(

    operations: [
        new Get(),
        new Patch(),
        new Delete(),
        new GetCollection(),
        new Get(
            uriTemplate: '/sendLinkResetPassword/{userName}',
            uriVariables: ["userName"],
            controller: SendLinkResetPassword::class,
            openapiContext: [
                "tags" => [
                    "Account"
                ],
                "summary" => "Envoie d'un lien reset password",
                "description" => "Si le userName existe, il envoie a l'adresse userName un lien donnant acces a un formulaire pour modifier sont mot de passe",
                "operationId" => "resetPassword",
                "responses" => [
                    "200" => [
                        "description" => "opération effectué avec succes",
                        "content" => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User-read',
                                ],
                            ],
                        ]
                    ],
                    "404" => [
                        "description" => "Erreur inconnue ",
                    ],
                    "400" => [
                        "description" => "UserName not found",
                    ],
                ],
                "parameters" => [
                    [
                        "in" => "path",
                        "name" => "userName",
                        "required" => true,
                        "schema" => [
                            "type" => "string",
                        ],
                        "example" => "cocoro@yopmail.com",
                    ],
                ],
            ],
            exceptionToStatus: [ProductNotFoundException::class => 404],
            denormalizationContext: ["groups" => ["read"]],
            filters: [
            ],
            read: false,
            name: 'send_link_reset_password',
        ),
        new Get(
            uriTemplate: '/sendLinkVerifyEmail/{userName}',
            uriVariables: [
                "userName"
            ],
            controller: SendLinkVerifyEmail::class,
            openapiContext: [
                "summary" => "Envoie d'un lien valider l'email",
                "parameters" => [
                    [
                        "in" => "path",
                        "name" => "userName",
                        "required" => true,
                    ],
                ],
            ],
            denormalizationContext: ["groups" => ["read"]],
            filters: [
            ],
            read: false,
            name: 'send_link_verify_email',
        ),
        new Put(
            uriTemplate: '/registration',
            controller: RegistrationController::class,
            openapiContext: [
                "summary" => "Enregistre un utilisateur avec envoie de mail d'un lien de confirmation valable 3 heures",
                "parameters" => [],
            ],
           // paginationEnabled: false,
            normalizationContext: ["groups" => ["userName"]],
           // filters: [],
            read: false,
            name: 'register',
            processor: RegistrationStateProcessor::class
        ),
        new Put(
            uriTemplate: '/verify_email',
            controller: VerifyEmailController::class,
            //class: UserConfirmationEmailDTO::class,
           // defaults : ["token" => "string" ],
            openapiContext: [
                "summary" => "Confirme l'inscription",
                /*"requestBody" => [
                    "content" => [
                        "application/json" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => [
                                    "token" => [
                                        "type" => "string",
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],*/
                "parameters" => [
                ],
            ],
            normalizationContext: ["groups" => ["read"]],
            denormalizationContext: ["groups" => ["token"]],
            input: UserConfirmationEmailDTO::class,
            name: 'verify_email',
            processor: UserConfirmationEmailProcessor::class
        ),
    ],
    routePrefix: "/api",
    normalizationContext: ["groups" => ["read"]],
    denormalizationContext: ["groups" => ["write"]],
    mercure: true,
)
]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
    fields: "userName", message: 'already existe', errorPath: 'userName',ignoreNull: false
)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    #[Groups(["read"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["read","write","userName"])]
    #[Assert\Email]

    private ?string $userName = null;

    #[ORM\Column]
    #[Groups(["read"])]
    private array $roles = ["USER"];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(["write"])]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(["read","verify"])]
    private ?bool $isVerified = false;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->userName;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}

