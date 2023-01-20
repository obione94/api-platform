<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

/**
 * This is a dummy entity. Remove it!
 */
#[ApiResource(
    operations: [
        new Put(),
        new Post(),
        new Get(),
        new Patch(),
        new Delete(),
        new GetCollection(),
    ],
    routePrefix: "/api",
    mercure: true,
    paginationEnabled: true,
    paginationItemsPerPage: 2,
)]

#[ApiFilter(SearchFilter::class, properties: ["name"=> "ipartial","description"=>"partial name"])]
#[ApiFilter(PropertyFilter::class)]
#[ORM\Entity]
class Greeting
{
    /**
     * The entity ID
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    /**
     * A nice person
     */
    #[ORM\Column]
    #[Assert\NotBlank]
    public string $name = '';

    public function getId(): ?int
    {
        return $this->id;
    }
}
