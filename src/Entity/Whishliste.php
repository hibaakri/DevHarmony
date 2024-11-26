<?php

namespace App\Entity;

use App\Repository\WhishlisteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WhishlisteRepository::class)]
class Whishliste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\ManyToMany(targetEntity: Produit::class, inversedBy: 'WhishlisteProduit')]
    private Collection $Items;

    public function __construct()
    {
        $this->Items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getItems(): Collection
    {
        return $this->Items;
    }

    public function addItem(Produit $item): static
    {
        if (!$this->Items->contains($item)) {
            $this->Items->add($item);
        }

        return $this;
    }

    public function removeItem(Produit $item): static
    {
        $this->Items->removeElement($item);

        return $this;
    }
}
