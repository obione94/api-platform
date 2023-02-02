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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
        new GetCollection(
            normalizationContext: ["groups" => ["user:read", "user:collection:get"]],

        ),
        new Put(
            uriTemplate: '/registration',
            controller: RegistrationController::class,
            openapiContext: [
                "summary" => "Enregistre un utilisateur",
                "parameters" => [],
            ],
           // paginationEnabled: false,
            normalizationContext: ["groups" => ["userName"]],
           // filters: [],
            read: false,
            name: 'register',
            processor: RegistrationStateProcessor::class
        ),
    ],
    routePrefix: "/api",
    normalizationContext: ["groups" => ["user:read"]],
    denormalizationContext: ["groups" => ["user:write"]],
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
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["user:read","bid:read","sale:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["user:read","user:write","userName","bid:read","sale:read"])]
    #[Assert\Email]

    private ?string $userName = null;

    #[ORM\Column]
    #[Groups(["user:read","bid:read","sale:read"])]
    private array $roles = ["USER"];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(["user:write"])]
    private ?string $password = null;

    #[ORM\Column]
    #[Groups(["user:read","verify"])]
    private ?bool $isVerified = false;

    #[ORM\OneToMany(mappedBy: 'bider', targetEntity: Bid::class)]
    #[Groups(["user:read"])]
    private Collection $bids;

    #[ORM\OneToMany(mappedBy: 'seller', targetEntity: Sale::class)]
    #[Groups(["user:read"])]
    private Collection $sales;

    public function __construct()
    {
        $this->bids = new ArrayCollection();
        $this->sales = new ArrayCollection();
    }


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

    /**
     * @return Collection<int, Bid>
     */
    public function getBids(): Collection
    {
        return $this->bids;
    }

    public function addBid(Bid $bid): self
    {
        if (!$this->bids->contains($bid)) {
            $this->bids->add($bid);
            $bid->setBider($this);
        }

        return $this;
    }

    public function removeBid(Bid $bid): self
    {
        if ($this->bids->removeElement($bid)) {
            // set the owning side to null (unless already changed)
            if ($bid->getBider() === $this) {
                $bid->setBider(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sale>
     */
    public function getSales(): Collection
    {
        return $this->sales;
    }

    public function addSale(Sale $sale): self
    {
        if (!$this->sales->contains($sale)) {
            $this->sales->add($sale);
            $sale->setSeller($this);
        }

        return $this;
    }

    public function removeSale(Sale $sale): self
    {
        if ($this->sales->removeElement($sale)) {
            // set the owning side to null (unless already changed)
            if ($sale->getSeller() === $this) {
                $sale->setSeller(null);
            }
        }

        return $this;
    }
}

