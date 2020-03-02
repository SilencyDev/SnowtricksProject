<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TokenRepository")
 */
class Token
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationdate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tokens", cascade={"persist"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    public function __construct(User $user)
    {
        $this->creationdate = new \DateTime();
        $this->user = $user;
        $this->value = md5(uniqid());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationdate(): ?\DateTimeInterface
    {
        return $this->creationdate;
    }

    public function setCreationdate(\DateTimeInterface $creationdate): self
    {
        $this->creationdate = $creationdate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function isValid()
    {
        $interval = new \DateInterval('PT15M');

        return $this->creationdate->add($interval) >= new \DateTime();
    }
}
