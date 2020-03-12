<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SnowtrickRepository")
 * @UniqueEntity("title")
 */
class Snowtrick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="snowtrick")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="snowtricks")
     */
    private $author;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="snowtricks")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="snowtrick", cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="snowtrick", cascade={"persist","remove"})
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="snowtrick", cascade={"persist","remove"})
     */
    private $pictures;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Mainpicture", mappedBy="snowtrick", cascade={"persist","remove"})
     */
    private $mainpicture;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getValidated(): bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }


    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addSnowtrick($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
            $category->removeSnowtrick($this);
        }

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

    /**
     * @return Collection|Video[]
     */
    public function getVideos()
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos->add($video);
            $video->setSnowtrick($this);
        }

        return $this;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getPictures()
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setSnowtrick($this);
        }

        return $this;
    }

    /**
     * Get the value of mainpicture
     */
    public function getMainpicture()
    {
        return $this->mainpicture;
    }

    /**
     * Set the value of mainpicture
     *
     * @return  self
     */
    public function setMainpicture($mainpicture)
    {
        $this->mainpicture = $mainpicture;

        return $this;
    }

    public function addMainpicture(Mainpicture $mainpicture): self
    {
        
        $this->mainpicture = $mainpicture;;
        $mainpicture->setSnowtrick($this);

        return $this;
    }
}
