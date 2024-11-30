<?php

namespace App\Entity;

use App\Repository\ServiceApresVenteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceApresVenteRepository::class)]
class ServiceApresVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull()]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $Type_probleme = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Vous pouvez ajouter une description.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $Description_probleme = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $Date_demande = null;

    // #[ORM\Column(type: Types::DATE_MUTABLE)]
    // private ?\DateTimeInterface $Date_resolution = null;

    // #[ORM\Column(length: 255)]
    // #[Assert\NotBlank(message: "N'ajouter rien ici .")]
    // #[Assert\Length(
    //     max: 255,
    //     maxMessage: "Le commentaire ne peut pas dépasser {{ limit }} caractères."
    // )]
    // private ?string $Commentaire_technicien = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'serviceApresVente')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $produit;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'serviceApresVente')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $user;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Etat_demande = null;

    public function __construct()
    {
        $this->produit = new ArrayCollection();
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeProbleme(): ?string
    {
        return $this->Type_probleme;
    }

    public function setTypeProbleme(string $Type_probleme): static
    {
        $this->Type_probleme = $Type_probleme;

        return $this;
    }

    public function getDescriptionProbleme(): ?string
    {
        return $this->Description_probleme;
    }

    public function setDescriptionProbleme(string $Description_probleme): static
    {
        $this->Description_probleme = $Description_probleme;

        return $this;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->Date_demande;
    }

    public function setDateDemande(\DateTimeInterface $Date_demande): static
    {
        $this->Date_demande = $Date_demande;

        return $this;
    }

    // public function getDateResolution(): ?\DateTimeInterface
    // {
    //     return $this->Date_resolution;
    // }

    // public function setDateResolution(\DateTimeInterface $Date_resolution): static
    // {
    //     $this->Date_resolution = $Date_resolution;

    //     return $this;
    // }

    // public function getCommentaireTechnicien(): ?string
    // {
    //     return $this->Commentaire_technicien;
    // }

    // public function setCommentaireTechnicien(string $Commentaire_technicien): static
    // {
    //     $this->Commentaire_technicien = $Commentaire_technicien;

    //     return $this;
    // }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->produit;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produit->contains($produit)) {
            $this->produit->add($produit);
            $produit->setServiceApresVente($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produit->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getServiceApresVente() === $this) {
                $produit->setServiceApresVente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
            $user->setServiceApresVente($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getServiceApresVente() === $this) {
                $user->setServiceApresVente(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isEtatDemande(): ?bool
    {
        return $this->Etat_demande;
    }

    public function setEtatDemande(?bool $Etat_demande): static
    {
        $this->Etat_demande = $Etat_demande;

        return $this;
    }



}
