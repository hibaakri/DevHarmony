<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_panier = null;

    #[ORM\Column]
    private ?int $id_produit = null;

     /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'panier')]
    private Collection $Produit;

    public function __construct()
    {
        $this->Produit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPanier(): ?int
    {
        return $this->id_panier;
    }

    public function setIdPanier(int $id_panier): static
    {
        $this->id_panier = $id_panier;

        return $this;
    }

    public function getIdProduit(): ?int
    {
        return $this->id_produit;
    }

    public function setIdProduit(int $id_produit): static
    {
        $this->id_produit = $id_produit;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->Produit;
    }

    public function addProduit(Produit $produit): self
{
    if (!$this->Produit->contains($produit)) {
        $this->Produit->add($produit);
        $produit->setPanier($this);
    }
    return $this;
}

public function removeProduit(Produit $produit): self
{
    if ($this->Produit->removeElement($produit)) {
        if ($produit->getPanier() === $this) {
            $produit->setPanier(null);
        }
    }
    return $this;
}    
}