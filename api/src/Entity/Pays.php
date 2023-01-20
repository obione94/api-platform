<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\PaysRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaysRepository::class)]
#[ApiResource(
    operations: [
        new Put(),
        new Post(),
        new Get(
            normalizationContext: ["groups" => ["pays:read", "pays:item:get"]],
        ),
        new Patch(),
        new Delete(),
        new GetCollection(
            normalizationContext: ["groups" => ["pays:read", "pays:collection:get"]],
        ),
    ],
    normalizationContext: ["groups" => ["pays:read", ]],
    denormalizationContext: ["groups" => ["pays:write", ]],
    mercure: true,
)]
#[ApiFilter(SearchFilter::class, properties: ["name" => "partial", "continent" => "exact", "continent.name" => "partial",])]
#[ApiFilter(PropertyFilter::class)]

class Pays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["pays:read",])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["pays:read","pays:write", "continent:read", "continent:write",])]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\ManyToOne(cascade: ["persist"], inversedBy: 'pays')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["pays:read", "pays:write",])]
    #[Assert\Valid]
    private ?Continent $continent = null;

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

    public function getContinent(): ?Continent
    {
        return $this->continent;
    }

    public function setContinent(?Continent $continent): self
    {
        $this->continent = $continent;

        return $this;
    }
}
