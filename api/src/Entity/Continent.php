<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\ContinentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContinentRepository::class)]
#[ApiResource(
    operations: [
        new Put(),
        new Post(),
        new Get(),
        new Patch(),
        new Delete(),
        new GetCollection(),
    ],
    normalizationContext: ["groups" => ["continent:read", ]],
    denormalizationContext: ["groups" => ["continent:write", ]],
    mercure: true,
)]
#[ApiFilter(PropertyFilter::class)]
#[ApiResource(
    uriTemplate: '/pays/{id}/continent',
    operations: array(new Get()),
    uriVariables: [
        'id' => new Link(
            fromProperty: 'continent',
            fromClass: Pays::class
        )
    ]
)]
class Continent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["continent:read", "continent:write",])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(["continent:read", "continent:write", "pays:item:get", 'pays:collection:get', "pays:write",])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'continent', targetEntity: Pays::class, cascade: ["persist"], orphanRemoval:true)]
    #[Groups(["continent:read", "continent:write",])]
    #[Assert\Valid]
    private Collection $pays;

    public function __construct()
    {
        $this->pays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Pays>
     */
    public function getPays(): Collection
    {
        return $this->pays;
    }

    public function addPay(Pays $pay): self
    {
        if (!$this->pays->contains($pay)) {
            $this->pays->add($pay);
            $pay->setContinent($this);
        }

        return $this;
    }

    public function removePay(Pays $pay): self
    {
        if ($this->pays->removeElement($pay)) {
            // set the owning side to null (unless already changed)
            if ($pay->getContinent() === $this) {
                $pay->setContinent(null);
            }
        }

        return $this;
    }
}
