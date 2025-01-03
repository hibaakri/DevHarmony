<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
 use Doctrine\Common\Collections\ArrayCollection; 
use Doctrine\Common\Collections\Collection;


 
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column] ///////////////////////////
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'le Username doit contenir au min{{ 3 }} caractère ',
        maxMessage: 'le Username doit contenir au max{{ 150 }} caractère',
        )]
    private ?String $username = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'mot de passe doit contenir au min{{ 3 }} caractère ',
        maxMessage: 'mot de passe doit contenir au max{{ 150 }} caractère',
        )]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Whishliste::class)]
    private Collection $whishlistes;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $resetToken = null;

    public function __construct()
    {
        $this->whishlistes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
 
    public function __toString()
    {
        return $this->username;
    }

 

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
 


    public function getWhishlistes(): Collection
    {
        return $this->whishlistes;
    }

    

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;
    
        return $this;
    }

    public function addWhishliste(Whishliste $whishliste): static
    {
        if (!$this->whishlistes->contains($whishliste)) {
            $this->whishlistes[] = $whishliste;
            $whishliste->setUser($this); // Associer l'utilisateur à la wishlist
        }
        return $this;
    }

    public function removeWhishliste(Whishliste $whishliste): static
    {
        if ($this->whishlistes->removeElement($whishliste)) {
            // Si la wishlist est supprimée, dissocier l'utilisateur
            if ($whishliste->getUser() === $this) {
                $whishliste->setUser(null);
            }
        }
        return $this;
    }

    
}
 
