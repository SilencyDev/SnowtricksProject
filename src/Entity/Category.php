<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
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
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\snowtrick", mappedBy="categories")
     */
    private $snowtricks;

    public function __construct()
    {
        $this->snowtricks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|snowtrick[]
     */
    public function getSnowtricks(): Collection
    {
        return $this->snowtricks;
    }

    public function addSnowtrick(snowtrick $snowtrick): self
    {
        if (!$this->snowtricks->contains($snowtrick)) {
            $this->snowtricks[] = $snowtrick;
        }

        return $this;
    }

    public function removeSnowtrick(snowtrick $snowtrick): self
    {
        if ($this->snowtricks->contains($snowtrick)) {
            $this->snowtricks->removeElement($snowtrick);
        }

        return $this;
    }
}
