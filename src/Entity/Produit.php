<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $prix = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_AT = null;

    /**
     * @var Collection<int, Whishliste>
     */
    #[ORM\ManyToMany(targetEntity: Whishliste::class, mappedBy: 'Items')]
    private Collection $WhishlisteProduit;

    public function __construct()
    {
        $this->WhishlisteProduit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCreatedAT(): ?\DateTimeInterface
    {
        return $this->created_AT;
    }

    public function setCreatedAT(\DateTimeInterface $created_AT): static
    {
        $this->created_AT = $created_AT;

        return $this;
    }

    /**
     * @return Collection<int, Whishliste>
     */
    public function getWhishlisteProduit(): Collection
    {
        return $this->WhishlisteProduit;
    }

    public function addWhishlisteProduit(Whishliste $whishlisteProduit): static
    {
        if (!$this->WhishlisteProduit->contains($whishlisteProduit)) {
            $this->WhishlisteProduit->add($whishlisteProduit);
            $whishlisteProduit->addItem($this);
        }

        return $this;
    }

    public function removeWhishlisteProduit(Whishliste $whishlisteProduit): static
    {
        if ($this->WhishlisteProduit->removeElement($whishlisteProduit)) {
            $whishlisteProduit->removeItem($this);
        }

        return $this;
    }
}
