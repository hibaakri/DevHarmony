<?php

namespace App\Entity;

use App\Repository\ProduitRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;



use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]

    //controle de saisir titre length
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'tittre doit contenir au min{{ 3 }} caractère ',
        maxMessage: 'titre doit contenir au max{{ 150 }} caractère',
        )] 
    #[Assert\NotBlank(message: "Le Titre est obligatoire.")]    
    #[Assert\NotNull()] 
    private ?string $titre = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez ajouter une description.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix est obligatoire.")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif.")]

    private ?int $prix = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $created_At = null;


    #[ORM\ManyToOne(inversedBy: 'produits')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: "La catégorie est obligatoire.")]
    private ?Category $category = null;

    #[ORM\Column(length: 255)]
    #[Assert\File(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG ou PNG)."
    )]
    private ?string $image = null;

   

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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_At;
    }

    public function setCreatedAt(\DateTimeInterface $created_At): static
    {
        $this->created_At = $created_At;

        return $this;
    }

  
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;
 

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


    


   

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function __toString()
    {
        return $this->titre ;
    }
}

