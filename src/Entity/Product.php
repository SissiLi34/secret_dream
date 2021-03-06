<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le nom du produit est obligatoire")]
    #[Assert\Length(min:3, max:255, minMessage:"Le nom du produit doit contenir au moins 3 caractères")]
    private $name;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message:"Le prix du produit est obligatoire")]
    private $price;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'produts')]
    private $category;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"La photo principale est obligatoire")]
    #[Assert\Url(message:"La photo du produit doit être une URL valide")]
    private $mainPicture;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message:"La description doit être courte est obligatoire")]
    #[Assert\Length(min:20, minMessage:"La description courte doit quand même faire au moins 20 caractères")]
    private $shortDescription;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'products')]
    private $owner;

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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function setMainPicture(string $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}