<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
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
     * @Assert\Valid()
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="snowtrick", cascade={"remove"})
     * @Assert\Valid()
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="snowtrick", cascade={"persist","remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $videos;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Picture", mappedBy="snowtrick", cascade={"persist","remove"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    private $pictures;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @Assert\All({
     *      @Assert\Image(
     *           mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg", "image/webp"},
     *           mimeTypesMessage = "The file must be in GIF/JPG/JPEG or PNG format", 
     *           )
     * })
     */
    private $file;

    /**
     * @Assert\Image(
     * mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg", "image/webp"},
     * mimeTypesMessage = "The file must be in GIF/JPG/JPEG or PNG format", 
     * )
     */
    private $mainFile;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->pictures = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->createdAt = new \DateTime("now");
        $this->updatedAt = null;
        $this->validated = false;
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

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setSnowtrick($this);
        }

        return $this;
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

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
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

    public function getMainPicture(): ?Picture
    {
        $main = $this->pictures->filter(function(Picture $picture) {
                return $picture->getIsMainPicture();
        })->first();

        return $main === false ? null : $main;
    }

    /**
     * @return Collection|Picture[]
     */
    public function getMediaPictures()
    {
        return $this->pictures->filter(function(Picture $picture) {
                return $picture->getIsMainPicture() === false;
        });
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setSnowtrick($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
        }

        return $this;
    }

    /**
     * Set the value of mainpicture
     *
     * @return  self
     */
    public function setMainpicture(Picture $mainpicture)
    {
        $this->mainpicture = $mainpicture;
        $mainpicture->setSnowtrick($this);

        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime("now");

        return $this;
    }

    public function getUpdatedAt()
    {
        if ($this->updatedAt === null) {
            return null;
        }
        return $this->updatedAt;
    }

    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime("now");

        return $this;
    }

    /**
     * Get mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg"},
     */ 
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg"},
     *
     * @return  self
     */ 
    public function setFile($file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg", "image/webp"},
     */ 
    public function getMainFile()
    {
        return $this->mainFile;
    }

    /**
     * Set mimeTypes= {"image/gif", "image/png", "image/jpeg", "image/jpg", "image/webp"},
     *
     * @return  self
     */ 
    public function setMainFile( $mainFile): self
    {
        $this->mainFile = $mainFile;

        return $this;
    }
}
