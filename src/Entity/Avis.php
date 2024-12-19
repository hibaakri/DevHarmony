<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Vous pouvez ajouter un commentaire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $Commentaire = null;
 

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_creation = null;

    // #[ORM\Column(length: 255)]
    // private ?string $Etat = null;

    #[ORM\JoinColumn(nullable: true)]
    private ?bool $Visibilite = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
     #[Assert\Length(
        max: 255,
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $Reponse = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[ORM\JoinColumn(nullable: true)]
     private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'avis')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent' ,cascade: ['remove'])]
    private Collection $replies;

    public function __construct()
    {
        $this->replies = new ArrayCollection();
    }

   

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentaire(): ?string
    {
        return $this->Commentaire;
    }

    public function setCommentaire(string $Commentaire): static
    {
        $this->Commentaire = $Commentaire;

        return $this;
    }

    

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->Date_creation;
    }

    public function setDateCreation(\DateTimeInterface $Date_creation): static
    {
        $this->Date_creation = $Date_creation;

        return $this;
    }

    // public function getEtat(): ?string
    // {
    //     return $this->Etat;
    // }

    // public function setEtat(string $Etat): static
    // {
    //     $this->Etat = $Etat;

    //     return $this;
    // }

    public function isVisibilite(): ?bool
    {
        return $this->Visibilite;
    }

    public function setVisibilite(bool $Visibilite): static
    {
        $this->Visibilite = $Visibilite;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->Reponse;
    }

    public function setReponse(string $Reponse): static
    {
        $this->Reponse = $Reponse;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAvis(): Collection
    {
        return $this->replies;
    }

    public function addAvi(self $avi): static
    {
        if (!$this->replies->contains($avi)) {
            $this->replies->add($avi);
            $avi->setParent($this);
        }

        return $this;
    }

    public function removeAvi(self $avi): static
    {
        if ($this->replies->removeElement($avi)) {
            // set the owning side to null (unless already changed)
            if ($avi->getParent() === $this) {
                $avi->setParent(null);
            }
        }

        return $this;
    }
 



    
    
}
