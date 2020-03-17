<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\Regex(
     *     pattern="#(?:https?:\/\/)?(?:www\.)?youtu\.?be(?:\.com)?\/?.*(?:watch|embed)?(?:.*v=|v\/|\/)([\w\-_]+)\&?#",
     *     match=true,
     *     message="Please copy a youtube link !"
     * )
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Snowtrick", inversedBy="videos", cascade={"persist"})
     */
    private $snowtrick;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get the value of snowtrick
     */
    public function getSnowtrick()
    {
        return $this->snowtrick;
    }

    /**
     * Set the value of snowtrick
     *
     * @return  self
     */
    public function setSnowtrick($snowtrick)
    {
        $this->snowtrick = $snowtrick;

        return $this;
    }
}