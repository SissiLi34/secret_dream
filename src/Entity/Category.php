<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: product::class)]
    private $produts;

    public function __construct()
    {
        $this->produts = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, product>
     */
    public function getProduts(): Collection
    {
        return $this->produts;
    }

    public function addProdut(product $produt): self
    {
        if (!$this->produts->contains($produt)) {
            $this->produts[] = $produt;
            $produt->setCategory($this);
        }

        return $this;
    }

    public function removeProdut(product $produt): self
    {
        if ($this->produts->removeElement($produt)) {
            // set the owning side to null (unless already changed)
            if ($produt->getCategory() === $this) {
                $produt->setCategory(null);
            }
        }

        return $this;
    }
}