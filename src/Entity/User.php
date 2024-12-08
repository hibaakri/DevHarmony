<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

//      #[ORM\OneToOne(mappedBy:'user', cascade:['persist', 'remove'])]

//    private $panier;

//    // Getter et setter pour panier
//    public function getPanier(): ?Panier
//    {
//        return $this->panier;
//    }

//    public function setPanier(?Panier $panier): self
//    {
//        $this->panier = $panier;
//        return $this;
//    }




    // #[ORM\OneToMany(mappedBy: 'user', targetEntity: Panier::class, cascade: ['persist', 'remove'])]
    // private Collection $paniers;

    // public function __construct()
    // {
    //     $this->paniers = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @return array<string> The roles of the user
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Guarantee every user has at least ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
    foreach ($roles as $role) {
        if (!is_string($role)) {
            throw new \InvalidArgumentException('Chaque rôle doit être une chaîne.');
        }
     }

     $this->roles = $roles;

     return $this;
    }


    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is not needed if you're using a modern algorithm like bcrypt or sodium.
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // /**
    //  * @return Collection<int, Panier>
    //  */
    // public function getPaniers(): Collection
    // {
    //     return $this->paniers;
    // }

    // public function addPanier(Panier $panier): self
    // {
    //     if (!$this->paniers->contains($panier)) {
    //         $this->paniers[] = $panier;
    //         $panier->setUser($this);
    //     }

    //     return $this;
    // }

    // public function removePanier(Panier $panier): self
    // {
    //     if ($this->paniers->removeElement($panier)) {
    //         // Unset the owning side of the relationship
    //         if ($panier->getUser() === $this) {
    //             $panier->setUser(null);
    //         }
    //     }

    //     return $this;
    // }

    // public function getPanier(): ?Panier
    // {
    //     return $this->panier;
    // }

    // public function setPanier(?Panier $panier): static
    // {
    //     // unset the owning side of the relation if necessary
    //     if ($panier === null && $this->panier !== null) {
    //         $this->panier->setUser(null);
    //     }

        // set the owning side of the relation if necessary
    //     if ($panier !== null && $panier->getUser() !== $this) {
    //         $panier->setUser($this);
    //     }

    //     $this->panier = $panier;

    //     return $this;
    // }
   

}
