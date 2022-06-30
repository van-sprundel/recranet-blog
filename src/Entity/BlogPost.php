<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $subtitle;

    #[ORM\Column(type: 'string', length: 255)]
    private $content;

    #[ORM\Column(type: 'date')]
    private $createdOn;

    #[ORM\Column(type: 'string', length: 255)]
    private $headImage;

    #[ORM\Column(type: 'array', nullable: true)]
    private $extraImages = [];

    #[ORM\OneToMany(mappedBy: 'blogPosts', targetEntity: User::class)]
    private $createdBy;

    public function getId(): ?int
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): self
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getHeadImage(): ?string
    {
        return $this->headImage;
    }

    public function setHeadImage(string $headImage): self
    {
        $this->headImage = $headImage;

        return $this;
    }

    public function getExtraImages(): ?array
    {
        return $this->extraImages;
    }

    public function setExtraImages(?array $extraImages): self
    {
        $this->extraImages = $extraImages;

        return $this;
    }
}
