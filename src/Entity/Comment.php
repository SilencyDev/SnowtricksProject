<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="username")
     * @ORM\Column(type="string", length=100)
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Snowtrick", inversedBy="id")
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(nullable=false)
     */
    private $snowtrick;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->Content = $content;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getValidated(): ?Bool
    {
        return $this->validated;
    }

    public function setValidated($validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getSnowtrick(): ?int
    {
        return $this->snowtrick;
    }

    public function setSnowtrick(int $snowtrick): self
    {
        $this->snowtrick = $snowtrick;

        return $this;
    }
}
