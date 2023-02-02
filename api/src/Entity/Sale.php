<?php

namespace App\Entity;


use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\SaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ApiResource(
    operations: [
        new Put(),
        new Post(),
        new Get(),
        new Patch(),
        new Delete(),
        new GetCollection(
            normalizationContext: ["groups" => ["sale:read", "sale:collection:get"]],
        ),
    ],
    routePrefix: "/api",
    normalizationContext: ["groups" => ["sale:read", ]],
    denormalizationContext: ["groups" => ["sale:write", ]],
    mercure: true,
)]

class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["sale:read"])]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["sale:read", "sale:write", "bid:item:get", 'bid:collection:get'])]
    private ?string $model = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Groups(["sale:read", "sale:write", "bid:item:get", 'bid:collection:get'])]
    private ?int $unit = null;

    #[ORM\Column]
    #[Groups(["sale:read", "sale:write", "bid:item:get", 'bid:collection:get'])]
    private ?float $baseUnitPrice = null;

    #[ORM\Column(length: 255)]
    #[Groups(["sale:read", "sale:write", "bid:item:get", 'bid:collection:get'])]
    private ?string $status = null;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'sales')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["user:read", "user:write","sale:read","sale:write"])]
    #[Assert\Valid]
    private ?User $seller = null;

    #[ORM\OneToMany(mappedBy: 'sale', targetEntity: Bid::class, cascade: ["persist"], orphanRemoval:true)]
    #[Groups(["sale:read"])]
    #[Assert\Valid]
    private Collection $bids;

    public function __construct()
    {
        $this->bids = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getUnit(): ?int
    {
        return $this->unit;
    }

    public function setUnit(int $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getBaseUnitPrice(): ?float
    {
        return $this->baseUnitPrice;
    }

    public function setBaseUnitPrice(float $baseUnitPrice): self
    {
        $this->baseUnitPrice = $baseUnitPrice;

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

    public function getSeller(): ?User
    {
        return $this->seller;
    }

    public function setSeller(?User $seller): self
    {
        $this->seller = $seller;

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
            $bid->setSale($this);
        }

        return $this;
    }

    public function removeBid(Bid $bid): self
    {
        if ($this->bids->removeElement($bid)) {
            // set the owning side to null (unless already changed)
            if ($bid->getSale() === $this) {
                $bid->setSale(null);
            }
        }

        return $this;
    }
}
