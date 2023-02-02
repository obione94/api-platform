<?php

namespace App\Entity;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\BidRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: BidRepository::class)]
#[ApiResource(
    operations: [
        new Put(),
        new Post(),
        new Get(
            normalizationContext: ["groups" => ["bid:read", "bid:item:get"]],
        ),
        new Patch(),
        new Delete(),
        new GetCollection(
            normalizationContext: ["groups" => ["bid:read", "bid:collection:get"]],
        ),
    ],
    routePrefix: "/api",
    normalizationContext: ["groups" => ["bid:read", ]],
    denormalizationContext: ["groups" => ["bid:write", ]],
    mercure: true,
)]

class Bid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["bid:read","bid:write","pays:read","sale:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bids')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["bid:read", "bid:write",])]
    private ?User $bider = null;

    #[ORM\Column]
    #[Groups(["bid:read","bid:write", "user:read", "user:write","sale:read"])]
    private ?float $unitPrice = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(["bid:read","bid:write", "user:read", "user:write","sale:read"])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Groups(["bid:read","bid:write", "user:read", "user:write","sale:read"])]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'bids')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["bid:read","bid:write",])]
    private ?Sale $sale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBider(): ?User
    {
        return $this->bider;
    }

    public function setBider(?User $bider): self
    {
        $this->bider = $bider;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getSale(): ?Sale
    {
        return $this->sale;
    }

    public function setSale(?Sale $sale): self
    {
        $this->sale = $sale;

        return $this;
    }
}
