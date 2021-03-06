<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @UniqueEntity("name")
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
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     * )
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Snowtrick", mappedBy="categories")
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
