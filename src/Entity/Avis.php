<?php

namespace App\Entity;

use App\Repository\AvisRepository;
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

    // #[ORM\Column]
    // #[Assert\NotBlank(message: "Vous pouvez ajouter une note.")]
    // #[Assert\Length(
    //     min: 0,
    //     max: 10
    // )]
    // private ?int $Note = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_creation = null;

    // #[ORM\Column(length: 255)]
    // private ?string $Etat = null;

    // #[ORM\Column]
    // private ?bool $Visibilite = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Vous pouvez répondre au commentaire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $Reponse = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[ORM\JoinColumn(nullable: true)]
    #[Assert\NotNull(message: "Champ obligatoire!")]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    private ?Produit $produit = null;

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

    // public function getNote(): ?int
    // {
    //     return $this->Note;
    // }

    // public function setNote(int $Note): static
    // {
    //     $this->Note = $Note;

    //     return $this;
    // }

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

    // public function isVisibilite(): ?bool
    // {
    //     return $this->Visibilite;
    // }

    // public function setVisibilite(bool $Visibilite): static
    // {
    //     $this->Visibilite = $Visibilite;

    //     return $this;
    // }

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



    
    
}
