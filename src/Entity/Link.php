<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LinkRepository")
 */
class Link
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=4000)
     */
    private $url;

    /**
     * @ORM\Column(type="integer", options={"default": 0})
     */
    private $visits = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCustomSlug;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getVisits(): ?int
    {
        return $this->visits;
    }

    public function setVisits(int $visits): self
    {
        $this->visits = $visits;

        return $this;
    }

    public function addOneVisit(): self
    {
        $this->visits += 1;

        return $this;
    }

    public function getIsCustomSlug(): ?bool
    {
        return $this->isCustomSlug;
    }

    public function setIsCustomSlug(bool $isCustomSlug): self
    {
        $this->isCustomSlug = $isCustomSlug;

        return $this;
    }
}
