<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Timestampable;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Impossible de créer un compte avec cet email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;



    #[Assert\NotBlank(
        message: 'Le prénom est obligatoire.'
    )]
    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;
    


    #[Assert\NotBlank(
        message: 'Le nom est obligatoire.'
    )]
    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;



    #[Assert\NotBlank(
        message: 'L\'email est obligatoire.'
    )]
    #[Assert\Email(
        message: 'L\'email {{ value }} n\'est valide.'
    )]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private $email;



    #[ORM\Column(type: 'json')]
    private $roles = [];
    


    #[Assert\NotBlank(
        message: 'Le mot de passe est obligatoire.'
    )]
    #[Assert\Length(
        min: 6,
        max: 100,
        minMessage: 'Le mot de passe doit contenir {{ limit }} caractères minimum.',
        maxMessage: 'Le mot de passe doit contenir {{ limit }} caractères maximum.',
    )]
    #[Assert\Regex(
        pattern: '#^(?=.*[a-zà-ÿ])(?=.*[A-ZÀ-Ỳ])(?=.*[0-9])(?=.*[^a-zà-ÿA-ZÀ-Ỳ0-9]).{8,4096}$#',
        match: true,
        message: 'Votre mot de passe doit contenir au moins un chiffre, une lettre minuscule, une lettre majuscule et un caractère spécial.',
    )]
    #[Assert\NotCompromisedPassword(
        message: 'La sécurité de votre mot de passe est faible, veuillez en choisir un autre.'
    )]
    #[ORM\Column(type: 'string', length: 255)]
    private $password;



    #[ORM\Column(type: 'boolean', options: array('default' => false))]
    private $isVerified;



    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $tokenForEmailVerification;



    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $deadLineForEmailVerification;


        /**
     * @var \DateTimeImmutable
     */
    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $createdAt;



    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $verifiedAt;



        /**
     * @var \DateTimeImmutable
     */
    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private $updatedAt;

    

    // Pour automatiser les colonnes createdAt + updatedAt
    public function __construct()
    {   
        // Compte pas vérifié au moment de l'inscription
        $this->isVerified = false;

        // Role User par défaut
        $this->roles[] = "ROLE_USER";

        // On utilise '\' pour accéder à la class venant du code source de php
        // $this->createdAt = new \DateTimeImmutable();
        // $this->updatedAt = new \DateTimeImmutable();
    }

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
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getTokenForEmailVerification(): ?string
    {
        return $this->tokenForEmailVerification;
    }

    public function setTokenForEmailVerification(?string $tokenForEmailVerification): self
    {
        $this->tokenForEmailVerification = $tokenForEmailVerification;

        return $this;
    }

    public function getDeadLineForEmailVerification(): ?\DateTimeImmutable
    {
        return $this->deadLineForEmailVerification;
    }

    public function setDeadLineForEmailVerification(?\DateTimeImmutable $deadLineForEmailVerification): self
    {
        $this->deadLineForEmailVerification = $deadLineForEmailVerification;

        return $this;
    }

    public function getVerifiedAt(): ?\DateTimeImmutable
    {
        return $this->verifiedAt;
    }

    public function setVerifiedAt(?\DateTimeImmutable $verifiedAt): self
    {
        $this->verifiedAt = $verifiedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
