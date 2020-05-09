<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *  fields={"email"},
 *  message="E-mail already used !"
 * )
 * @UniqueEntity(
 *  fields={"username"},
 *  message="Pseudo already used !"
 * )
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, unique=true)
     * @Assert\NotBlank(message="Please insert an username")
     * @Assert\Length(max=100, maxMessage="Your username must not exceed 100 caracteres !")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message="Please insert a valid Email")
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Snowtrick", mappedBy="author")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $snowtricks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Token", mappedBy="user", cascade={"persist", "remove"})
     */
    private $tokens;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Picture", mappedBy="user", cascade={"persist","remove"}, orphanRemoval=true)
     */
    private $picture;

    /**
     * @Assert\NotBlank()
     * @Assert\Image(
     * mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg", "image/webp"},
     * maxSize="2M",
     * )
     */
    private $avatar;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->snowtricks = new ArrayCollection();
        $this->roles = ['ROLE_MEMBER'];
        $this->tokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Snowtrick[]
     */
    public function getSnowtricks(): Collection
    {
        return $this->snowtricks;
    }

    public function setSnowtricks(Snowtrick $snowtricks): self
    {
        $this->snowtricks = $snowtricks;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function setComments(Comment $comments) :self
    {
        $this->comments = $comments;

        return $this;
    }
    
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->email
            ]);
    }
    
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password,
            $this->email
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|Token[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setUser($this);
        }

        return $this;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getUser() === $this) {
                $token->setUser(null);
            }
        }

        return $this;
    }

    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    public function setPicture(?Picture $picture): self
    {
        $this->picture = $picture;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $picture ? null : $this;
        if ($picture->getUser() !== $newUser) {
            $picture->setUser($newUser);
        }

        return $this;
    }

    /**
     * Get mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg"},
     */ 
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg"},
     *
     * @return  self
     */ 
    public function setAvatar(UploadedFile $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}
