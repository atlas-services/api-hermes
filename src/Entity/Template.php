<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\TemplateRepository;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
#[ApiResource(
    order: ['type' => 'ASC'],
    paginationItemsPerPage: 20,
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    normalizationContext: ['groups' => ['template:read:collection']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['template:read:collection', 'template:read:item', 'template:read:formation:collection']]
        ),
        new GetCollection(
        ),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            normalizationContext: ['groups' => ['template:write:item']]
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
        ),
    ],
)

]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['template:read:collection', 'template:write:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['template:read:collection', 'template:write:item'])]
    private ?string $type = null;


    #[ORM\Column(length: 255)]
    #[Groups(['template:read:collection', 'template:write:item'])]
    private ?string $name = null;

    #[ORM\Column(type: "text",length: 2000)]
    #[Groups(['template:read:collection', 'template:write:item'])]
    private ?string $content = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

}
